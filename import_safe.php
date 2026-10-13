<?php
/**
 * Script de Importación Segura para base.sql
 * Preserva nuevas tablas, nuevas columnas y datos locales.
 */

// 1. Cargar configuración de base de datos desde .env
$envPath = __DIR__ . '/.env';
if (!file_exists($envPath)) {
    die("Error: No se encontró el archivo .env en " . $envPath . "\n");
}

echo "Cargando configuración desde .env...\n";
$env = file_get_contents($envPath);
$lines = explode("\n", $env);
$db_config = [];
foreach ($lines as $line) {
    $line = trim($line);
    if (!$line || strpos($line, '#') === 0) continue;
    $parts = explode('=', $line, 2);
    if (count($parts) === 2) {
        $key = trim($parts[0]);
        $val = trim($parts[1]);
        $db_config[$key] = trim($val, '"\'');
    }
}

$host = $db_config['DB_HOST'] ?? '127.0.0.1';
$port = $db_config['DB_PORT'] ?? '3306';
$dbname = $db_config['DB_DATABASE'] ?? 'esam_academico';
$username = $db_config['DB_USERNAME'] ?? 'root';
$password = $db_config['DB_PASSWORD'] ?? '';

echo "Conectando a la base de datos `$dbname` en $host:$port...\n";
try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage() . "\n");
}

// 2. Leer base.sql
$dumpPath = __DIR__ . '/base.sql';
if (!file_exists($dumpPath)) {
    die("Error: No se encontró base.sql en " . $dumpPath . "\n");
}

echo "Leyendo base.sql...\n";
$dumpLines = file($dumpPath, FILE_IGNORE_NEW_LINES);
if (!$dumpLines) {
    die("Error al leer base.sql\n");
}
echo "Total líneas en base.sql: " . count($dumpLines) . "\n";

// Helper functions for parsing
function parseValuesBlock($valuesBlock) {
    $rows = [];
    $len = strlen($valuesBlock);
    $inString = false;
    $stringChar = '';
    $currentRow = '';
    $inRow = false;
    
    for ($i = 0; $i < $len; $i++) {
        $char = $valuesBlock[$i];
        
        if ($inString) {
            $currentRow .= $char;
            if ($char === $stringChar) {
                if ($i > 0 && $valuesBlock[$i-1] === '\\') {
                    // Escaped
                } else {
                    $inString = false;
                }
            }
        } else {
            if ($char === "'" || $char === '"') {
                $inString = true;
                $stringChar = $char;
                $currentRow .= $char;
            } else if ($char === '(') {
                if (!$inRow) {
                    $inRow = true;
                    $currentRow = '';
                } else {
                    $currentRow .= $char;
                }
            } else if ($char === ')') {
                if ($inRow) {
                    $nextNonWhitespace = '';
                    for ($j = $i + 1; $j < $len; $j++) {
                        if (!ctype_space($valuesBlock[$j])) {
                            $nextNonWhitespace = $valuesBlock[$j];
                            break;
                        }
                    }
                    if ($nextNonWhitespace === ',' || $nextNonWhitespace === ';' || $nextNonWhitespace === '') {
                        $rows[] = $currentRow;
                        $inRow = false;
                    } else {
                        $currentRow .= $char;
                    }
                } else {
                    $currentRow .= $char;
                }
            } else {
                if ($inRow) {
                    $currentRow .= $char;
                }
            }
        }
    }
    return $rows;
}

function parseRowColumns($rowStr) {
    $cols = [];
    $len = strlen($rowStr);
    $inString = false;
    $stringChar = '';
    $currentCol = '';
    
    for ($i = 0; $i < $len; $i++) {
        $char = $rowStr[$i];
        
        if ($inString) {
            $currentCol .= $char;
            if ($char === $stringChar) {
                if ($i > 0 && $rowStr[$i-1] === '\\') {
                    // Escaped
                } else {
                    $inString = false;
                }
            }
        } else {
            if ($char === "'" || $char === '"') {
                $inString = true;
                $stringChar = $char;
                $currentCol .= $char;
            } else if ($char === ',') {
                $cols[] = trim($currentCol);
                $currentCol = '';
            } else {
                $currentCol .= $char;
            }
        }
    }
    $cols[] = trim($currentCol);
    
    return array_map(function($val) {
        $val = trim($val);
        if (strcasecmp($val, 'NULL') === 0) {
            return null;
        }
        if (strlen($val) >= 2 && (($val[0] === "'" && substr($val, -1) === "'") || ($val[0] === '"' && substr($val, -1) === '"'))) {
            $cleaned = substr($val, 1, -1);
            $cleaned = stripcslashes($cleaned);
            return $cleaned;
        }
        return $val;
    }, $cols);
}

function getPrimaryKeyColumns($pdo, $table) {
    $stmt = $pdo->prepare("SHOW KEYS FROM `$table` WHERE Key_name = 'PRIMARY'");
    $stmt->execute();
    $keys = $stmt->fetchAll();
    $cols = [];
    foreach ($keys as $k) {
        $cols[] = $k['Column_name'];
    }
    return $cols;
}

function getPkKey($pkVals, $pkCols) {
    if (count($pkCols) === 1) {
        return strval(current($pkVals));
    }
    ksort($pkVals);
    return implode('-', array_map('strval', $pkVals));
}

// 3. Procesar líneas del dump para extraer INSERTs e IDs
echo "Procesando INSERTs del dump...\n";
$inserts = [];
$dumpTableIds = [];
$inInsert = false;
$currentTable = '';
$currentSql = '';

// Get schema tables from DB for validation
$stmt = $pdo->query("SHOW TABLES");
$currentTables = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Cache primary keys
$tablePKs = [];
foreach ($currentTables as $tbl) {
    $tablePKs[$tbl] = getPrimaryKeyColumns($pdo, $tbl);
}

foreach ($dumpLines as $line) {
    if (!$inInsert) {
        if (preg_match('/^INSERT INTO `(\w+)`/i', $line, $m)) {
            $inInsert = true;
            $currentTable = $m[1];
            $currentSql = $line;
            $trimmed = rtrim($line);
            if (substr($trimmed, -1) === ';') {
                $inserts[$currentTable][] = $currentSql;
                $inInsert = false;
                $currentTable = '';
                $currentSql = '';
            }
        }
    } else {
        $currentSql .= "\n" . $line;
        $trimmed = rtrim($line);
        if (substr($trimmed, -1) === ';') {
            $inserts[$currentTable][] = $currentSql;
            $inInsert = false;
            $currentTable = '';
            $currentSql = '';
        }
    }
}

// Extraer IDs de los INSERTs del dump
echo "Extrayendo IDs del dump para identificar filas comunes...\n";
foreach ($inserts as $table => $sqlList) {
    if (!in_array($table, $currentTables)) {
        continue;
    }
    $pkCols = $tablePKs[$table];
    if (empty($pkCols)) continue;
    
    foreach ($sqlList as $sql) {
        if (preg_match('/INSERT INTO `' . preg_quote($table, '/') . '` \((.+?)\) VALUES/i', $sql, $m)) {
            $cols = array_map(function($c) { return trim(trim($c), '`'); }, explode(',', $m[1]));
            $pkIndexes = [];
            foreach ($pkCols as $pkc) {
                $idx = array_search($pkc, $cols);
                if ($idx !== false) {
                    $pkIndexes[$pkc] = $idx;
                }
            }
            if (empty($pkIndexes)) continue;
            
            $pos = stripos($sql, 'VALUES');
            $valuesBlock = substr($sql, $pos + 6);
            $rowStrings = parseValuesBlock($valuesBlock);
            foreach ($rowStrings as $rowStr) {
                $rowVals = parseRowColumns($rowStr);
                $pkVals = [];
                foreach ($pkIndexes as $pkc => $idx) {
                    if (isset($rowVals[$idx])) {
                        $pkVals[$pkc] = $rowVals[$idx];
                    }
                }
                if (count($pkVals) === count($pkCols)) {
                    $pkKey = getPkKey($pkVals, $pkCols);
                    $dumpTableIds[$table][$pkKey] = true;
                }
            }
        }
    }
}

// 4. Analizar diferencias de columnas (Nuevas Columnas)
echo "Analizando nuevas columnas locales...\n";
$tablesWithNewCols = [];
$dumpContent = implode("\n", $dumpLines);
preg_match_all('/CREATE TABLE (?:IF NOT EXISTS )?`(\w+)`\s*\((.+?)\)\s*ENGINE=/s', $dumpContent, $createBlocks);
$dumpTableCols = [];
foreach ($createBlocks[1] as $i => $tbl) {
    preg_match_all('/^\s+`(\w+)`/m', $createBlocks[2][$i], $cols);
    $dumpTableCols[$tbl] = $cols[1];
}

foreach ($currentTables as $tbl) {
    if (!isset($dumpTableCols[$tbl])) continue;
    
    $stmt = $pdo->query("SHOW COLUMNS FROM `$tbl`");
    $currentCols = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    
    $newCols = array_diff($currentCols, $dumpTableCols[$tbl]);
    if ($newCols) {
        $tablesWithNewCols[$tbl] = array_values($newCols);
        echo " - Tabla `$tbl` tiene nuevas columnas: " . implode(', ', $newCols) . "\n";
    }
}

// 5. RESPALDAR DATOS LOCALES
echo "\n=== RESPALDANDO DATOS LOCALES ===\n";

// Respaldar filas locales exclusivas de tablas comunes
$localOnlyRowsBackup = [];
foreach ($currentTables as $tbl) {
    if (!isset($inserts[$tbl])) continue; // No está en el dump
    $pkCols = $tablePKs[$tbl];
    if (empty($pkCols)) continue;
    
    // Obtener todas las filas locales
    $stmt = $pdo->query("SELECT * FROM `$tbl`");
    $allRows = $stmt->fetchAll();
    
    $localOnlyRows = [];
    foreach ($allRows as $row) {
        $pkVals = [];
        foreach ($pkCols as $pkc) {
            $pkVals[$pkc] = $row[$pkc];
        }
        $pkKey = getPkKey($pkVals, $pkCols);
        
        if (!isset($dumpTableIds[$tbl][$pkKey])) {
            $localOnlyRows[] = $row;
        }
    }
    
    if (count($localOnlyRows) > 0) {
        $localOnlyRowsBackup[$tbl] = $localOnlyRows;
        echo " - Respaldadas " . count($localOnlyRows) . " filas locales exclusivas de `$tbl`.\n";
    }
}

// Respaldar valores de nuevas columnas para filas comunes
$commonNewColsBackup = [];
foreach ($tablesWithNewCols as $tbl => $newCols) {
    $pkCols = $tablePKs[$tbl];
    if (empty($pkCols)) continue;
    
    $selectCols = array_merge($pkCols, $newCols);
    $selectList = implode(', ', array_map(function($c) { return "`$c`"; }, $selectCols));
    
    $stmt = $pdo->query("SELECT $selectList FROM `$tbl`");
    $rows = $stmt->fetchAll();
    
    foreach ($rows as $row) {
        $pkVals = [];
        foreach ($pkCols as $pkc) {
            $pkVals[$pkc] = $row[$pkc];
        }
        $pkKey = getPkKey($pkVals, $pkCols);
        
        if (isset($dumpTableIds[$tbl][$pkKey])) {
            $backupVals = [];
            foreach ($newCols as $col) {
                $backupVals[$col] = $row[$col];
            }
            $commonNewColsBackup[$tbl][$pkKey] = $backupVals;
        }
    }
    if (isset($commonNewColsBackup[$tbl])) {
        echo " - Respaldados nuevos campos para " . count($commonNewColsBackup[$tbl]) . " filas comunes en `$tbl`.\n";
    }
}

// 6. EJECUTAR LIMPIEZA E IMPORTACIÓN
echo "\n=== INICIANDO IMPORTACIÓN DE DATOS DEL SERVIDOR ===\n";

$pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
$pdo->exec("SET UNIQUE_CHECKS = 0");

$totalInserted = 0;
$importedTablesCount = 0;

foreach ($inserts as $table => $sqlList) {
    if (!in_array($table, $currentTables)) {
        echo " - [SALTAR] `$table`: no existe en la base de datos local.\n";
        continue;
    }
    
    try {
        $pdo->exec("DELETE FROM `$table`");
        
        $count = 0;
        foreach ($sqlList as $sql) {
            $affected = $pdo->exec($sql);
            if ($affected !== false) {
                $count += $affected;
            }
        }
        
        if ($count === 0) {
            $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
        }
        
        echo " - [OK] Importada tabla `$table`: $count filas.\n";
        $totalInserted += $count;
        $importedTablesCount++;
    } catch (PDOException $e) {
        echo " - [ERROR] Error al importar `$table`: " . $e->getMessage() . "\n";
    }
}

// 7. RESTAURAR DATOS LOCALES RESPALDADOS
echo "\n=== RESTAURANDO ADICIONES LOCALES ===\n";

// Restaurar filas locales exclusivas
foreach ($localOnlyRowsBackup as $tbl => $rows) {
    if (empty($rows)) continue;
    
    $columns = array_keys($rows[0]);
    $colList = implode(', ', array_map(function($c) { return "`$c`"; }, $columns));
    $placeholders = implode(', ', array_fill(0, count($columns), '?'));
    
    $sql = "INSERT INTO `$tbl` ($colList) VALUES ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $restoredCount = 0;
    $errorCount = 0;
    foreach ($rows as $row) {
        try {
            $stmt->execute(array_values($row));
            $restoredCount++;
        } catch (PDOException $e) {
            $errorCount++;
        }
    }
    echo " - [RESTORED] Reinsertadas $restoredCount filas locales-only en `$tbl`" . ($errorCount > 0 ? " ($errorCount errores)" : "") . ".\n";
}

// Restaurar nuevos campos para filas comunes
foreach ($commonNewColsBackup as $tbl => $backupMap) {
    if (empty($backupMap)) continue;
    
    $pkCols = $tablePKs[$tbl];
    $pkCol = $pkCols[0];
    
    $restoredCount = 0;
    foreach ($backupMap as $pkKey => $newColVals) {
        $setParts = [];
        $params = [];
        foreach ($newColVals as $col => $val) {
            $setParts[] = "`$col` = ?";
            $params[] = $val;
        }
        $params[] = $pkKey;
        
        $sql = "UPDATE `$tbl` SET " . implode(', ', $setParts) . " WHERE `$pkCol` = ?";
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $restoredCount++;
        } catch (PDOException $e) {
            echo " - [ERROR RESTORE FIELD] Error en `$tbl` ID $pkKey: " . $e->getMessage() . "\n";
        }
    }
    echo " - [RESTORED FIELDS] Restaurados nuevos campos de $restoredCount filas comunes en `$tbl`.\n";
}

$pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
$pdo->exec("SET UNIQUE_CHECKS = 1");

echo "\n=== PROCESO COMPLETADO ===\n";
echo "Tablas comunes importadas: $importedTablesCount\n";
echo "Filas importadas desde el dump: $totalInserted\n";
