<?php
$dump = file_get_contents('C:/xampp_82_12/htdocs/laravel_esamsucre/sucre1_sucre.sql');

// Get old tables and columns
preg_match_all('/CREATE TABLE IF NOT EXISTS `(\w+)` \(([^;]+)\)/s', $dump, $createBlocks);
$oldTables = [];
foreach ($createBlocks[1] as $i => $tbl) {
    preg_match_all('/^\s+`(\w+)`/m', $createBlocks[2][$i], $cols);
    $oldTables[$tbl] = $cols[1];
}
echo "=== OLD TABLES ===\n";
foreach ($oldTables as $t => $cols) {
    echo "$t: " . implode(', ', $cols) . "\n";
}

// Connect to current DB
$dsn = 'mysql:host=127.0.0.1;port=3306;dbname=esam_academico;charset=utf8mb4';
$pdo = new PDO($dsn, 'root', '');

$stmt = $pdo->query("SHOW TABLES");
$currentTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo "\n=== CURRENT TABLES ===\n";
echo implode(', ', $currentTables) . "\n";

// For each old table, compare columns with current
echo "\n=== COMPARISON ===\n";
foreach ($oldTables as $tbl => $oldCols) {
    if (!in_array($tbl, $currentTables)) {
        echo "[SKIP] $tbl: does not exist in current DB\n";
        continue;
    }
    $stmt = $pdo->query("SHOW COLUMNS FROM `$tbl`");
    $currentCols = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    $common = array_intersect($oldCols, $currentCols);
    $oldOnly = array_diff($oldCols, $currentCols);
    $newOnly = array_diff($currentCols, $oldCols);
    echo "[OK] $tbl: " . count($common) . " common cols";
    if ($oldOnly) echo " | OLD-only: " . implode(',', $oldOnly);
    if ($newOnly) echo " | NEW-only: " . implode(',', $newOnly);
    echo "\n";
}
