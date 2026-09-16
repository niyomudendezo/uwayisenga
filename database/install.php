#!/usr/bin/env php
<?php
/**
 * AgruKrwanda Database Installer
 * Run: php database/install.php
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');

echo "=== AgruKrwanda Database Installer ===\n\n";

try {
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $schema = file_get_contents(__DIR__ . '/schema.sql');
    $seeds  = file_get_contents(__DIR__ . '/seeds.sql');

    echo "Creating database and tables...\n";
    foreach (array_filter(array_map('trim', explode(';', $schema))) as $sql) {
        if ($sql) $pdo->exec($sql);
    }
    echo "✓ Schema created\n";

    echo "Inserting seed data...\n";
    $pdo->exec("USE agrukrwanda");
    foreach (array_filter(array_map('trim', explode(';', $seeds))) as $sql) {
        if ($sql) $pdo->exec($sql);
    }
    echo "✓ Seed data inserted\n\n";

    echo "=== Installation Complete ===\n";
    echo "Default admin credentials:\n";
    echo "  Email:    admin@agrukrwanda.rw\n";
    echo "  Password: Admin@1234\n\n";
    echo "Access the system at: http://localhost/agrukrwanda\n";

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
