<?php
$dumpPath = __DIR__ . '/base.sql';
$dsn = 'mysql:host=127.0.0.1;port=3306;dbname=esam_academico;charset=utf8mb4';

echo "Leyendo dump linea por linea...\n";
$lines = file($dumpPath, FILE_IGNORE_NEW_LINES);
if (!$lines) die("Error al leer el dump\n");
echo "Total lineas: " . count($lines) . "\n";

// Parse INSERT statements line by line
$inserts = [];
$inInsert = false;
$currentTable = '';
$currentSql = '';

foreach ($lines as $line) {
    if (!$inInsert) {
        // Check if this line starts an INSERT
        if (preg_match('/^INSERT INTO `(\w+)`/i', $line, $m)) {
            $inInsert = true;
            $currentTable = $m[1];
            $currentSql = $line;
            // Check if the INSERT ends on the same line (single-line INSERT)
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

echo "Tablas encontradas: " . count($inserts) . "\n";
foreach ($inserts as $table => $stmts) {
    echo "  $table: " . count($stmts) . " sentencias\n";
}

$pdo = new PDO($dsn, 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
$pdo->exec("SET UNIQUE_CHECKS = 0");

$stmt = $pdo->query("SHOW TABLES");
$currentTables = $stmt->fetchAll(PDO::FETCH_COLUMN);

$totalInserted = 0;
$tableCounts = [];

foreach ($inserts as $table => $stmts) {
    if (!in_array($table, $currentTables)) {
        echo "  [SKIP] $table: no existe en DB actual\n";
        continue;
    }

    try {
        // Count rows before delete
        $before = (int)$pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
        $pdo->exec("DELETE FROM `$table`");

        $count = 0;
        foreach ($stmts as $stmtSql) {
            $affected = $pdo->exec($stmtSql);
            if ($affected !== false) $count += $affected;
        }

        if ($count === 0) {
            $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
        }

        $tableCounts[$table] = (int)$count;
        $totalInserted += (int)$count;
        echo "  [OK] $table: $count filas (antes: $before)\n";
    } catch (PDOException $e) {
        echo "  [ERROR] $table: " . $e->getMessage() . "\n";
    }
}

$pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
$pdo->exec("SET UNIQUE_CHECKS = 1");

echo "\n--- COMPLETADO: $totalInserted filas en " . count($tableCounts) . " tablas ---\n";
