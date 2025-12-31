<?php
$env = file_get_contents(__DIR__ . '/../.env');
if (!$env) {
    echo "Cannot read .env\n";
    exit(1);
}
if (!preg_match('/DATABASE_URL\s*=\s*"([^"]+)"/', $env, $m)) {
    echo "DATABASE_URL not found in .env\n";
    exit(1);
}
$url = $m[1];
$parts = parse_url($url);
if (!$parts) { echo "Invalid DATABASE_URL\n"; exit(1); }
$user = $parts['user'] ?? null;
$pass = $parts['pass'] ?? null;
$host = $parts['host'] ?? 'localhost';
$port = $parts['port'] ?? 5432;
$path = $parts['path'] ?? '';
$dbname = ltrim($path, '/');
$opts = [];
if (isset($parts['query'])) {
    parse_str($parts['query'], $opts);
}
$ssl = ($opts['sslmode'] ?? '') === 'require';
$dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";
try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $stmt = $pdo->prepare("SELECT column_name FROM information_schema.columns WHERE table_name = ? ORDER BY ordinal_position");
    $stmt->execute(['commandes']);
    $cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (!$cols) {
        echo "No columns found for 'commandes' or table doesn't exist.\n";
        exit(0);
    }
    foreach ($cols as $c) echo $c, "\n";
} catch (Exception $e) {
    echo "ERROR: ", $e->getMessage(), "\n";
    exit(1);
}
