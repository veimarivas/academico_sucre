<?php
$dump = file_get_contents(__DIR__ . '/base.sql');

preg_match_all('/CREATE TABLE (?:IF NOT EXISTS )?`(\w+)`\s*\(/', $dump, $tables);
$oldTableNames = array_unique($tables[1]);
echo "=== OLD TABLES ===\n";
echo implode(', ', $oldTableNames) . "\n";

// Extract columns for each old table
$oldTableCols = [];
foreach ($oldTableNames as $tbl) {
    // Find the CREATE TABLE block for this table
    $pattern = '/CREATE TABLE (?:IF NOT EXISTS )?`' . preg_quote($tbl, '/') . '`\s*\((.+?)\)\s*ENGINE=/s';
    if (preg_match($pattern, $dump, $m)) {
        preg_match_all('/^\s+`(\w+)`/m', $m[1], $cols);
        $oldTableCols[$tbl] = $cols[1];
    }
}

// Connect to current DB
$dsn = 'mysql:host=127.0.0.1;port=3306;dbname=esam_academico;charset=utf8mb4';
$pdo = new PDO($dsn, 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$stmt = $pdo->query("SHOW TABLES");
$currentTables = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo "\n=== COMPARISON ===\n";
foreach ($oldTableCols as $tbl => $oldCols) {
    if (!in_array($tbl, $currentTables)) {
        echo "[SKIP] $tbl: not in current DB\n";
        continue;
    }
    $stmt = $pdo->query("SHOW COLUMNS FROM `$tbl`");
    $currentCols = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    $common = array_intersect($oldCols, $currentCols);
    $oldOnly = array_diff($oldCols, $currentCols);
    $newOnly = array_diff($currentCols, $oldCols);
    echo "[OK] $tbl: " . count($common) . " common";
    if ($oldOnly) echo " | old-only: " . implode(',', $oldOnly);
    if ($newOnly) echo " | new-only: " . implode(',', $newOnly);
    echo "\n";
}
