<?php
require_once __DIR__ . "/../config.php";

/**
 * Pembungkus koneksi MySQL (mysqli) dengan prepared statement.
 * Seluruh model memakai kelas ini sehingga tidak ada query yang
 * dirakit dari input pengguna secara langsung.
 */
class DB
{
    private static ?mysqli $conn = null;

    public static function connect(): mysqli
    {
        if (self::$conn === null) {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            self::$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            self::$conn->set_charset("utf8mb4");
        }
        return self::$conn;
    }

    /** Menjalankan SELECT dan mengembalikan seluruh baris sebagai array asosiatif. */
    public static function select(string $sql, string $types = "", array $params = []): array
    {
        $stmt = self::run($sql, $types, $params);
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    /** Menjalankan INSERT/UPDATE/DELETE dan mengembalikan jumlah baris terdampak. */
    public static function execute(string $sql, string $types = "", array $params = []): int
    {
        $stmt = self::run($sql, $types, $params);
        $affected = $stmt->affected_rows;
        $stmt->close();
        return $affected;
    }

    private static function run(string $sql, string $types, array $params): mysqli_stmt
    {
        $stmt = self::connect()->prepare($sql);
        if ($types !== "") {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt;
    }
}
