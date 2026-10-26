<?php
/**
 * Konfigurasi Database
 * Sesuaikan dengan setting Laragon Anda
 */

$host = 'localhost';
$dbname = 'catatan_harian';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Buat database jika belum ada
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbname`");

    // Buat tabel users
    $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `nama` VARCHAR(100) NOT NULL,
        `username` VARCHAR(50) NOT NULL UNIQUE,
        `password` VARCHAR(255) NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Buat tabel catatan_harian
    $pdo->exec("CREATE TABLE IF NOT EXISTS `catatan_harian` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `nomor` VARCHAR(20) NOT NULL,
        `tanggal` DATE NOT NULL,
        `deskripsi` TEXT NOT NULL,
        `keterangan` TEXT,
        `user_id` INT NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Insert default admin user jika belum ada
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute(['admin']);
    if ($stmt->fetchColumn() == 0) {
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (nama, username, password) VALUES (?, ?, ?)");
        $stmt->execute(['Administrator', 'admin', $hashedPassword]);
    }
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

