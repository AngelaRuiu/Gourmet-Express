<?php

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Database
 *
 *  A class that manages the PDO connection to the MySQL database.
 *  Obtain a connection anywhere with:  Database::getInstance()->getConnection()
 *
 * Every table name lives here as a constant so Managers never use raw strings.
 * Rename a table in schema.sql, update the constant here — nothing else breaks.
 */
class Database
{
    // Table name constants — used by all Manager classes to avoid hardcoding strings everywhere

    public const TABLE_RESTAURANT_INFO     = 'restaurant_info';
    public const TABLE_USERS               = 'users';
    public const TABLE_CATEGORIES          = 'categories';
    public const TABLE_MENU_ITEMS          = 'menu_items';
    public const TABLE_DINING_TABLES       = 'restaurant_tables';
    public const TABLE_RESERVATIONS        = 'reservations';
    public const TABLE_ORDER_STATUS        = 'order_status';
    public const TABLE_PAYMENT_STATUS      = 'payment_status';
    public const TABLE_ADDRESSES           = 'addresses';
    public const TABLE_ORDERS              = 'orders';
    public const TABLE_ORDER_ITEMS         = 'order_items';
    public const TABLE_PAYPAL_TRANSACTIONS = 'paypal_transactions';
    public const TABLE_PAYMENT_LOGS        = 'payment_logs';
    public const TABLE_REVIEWS             = 'reviews';
    public const TABLE_CONTACTS            = 'contacts';
    public const TABLE_JOB_POSITIONS       = 'job_positions';
    public const TABLE_JOB_APPLICATIONS    = 'job_applications';



    private static ?self $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $host = Config::get('db.host');
        $port = Config::get('db.port', 3306);
        $name = Config::get('db.name');
        $user = Config::get('db.user');
        $pass = Config::get('db.pass');

        $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

        // Calculate the current offset for Germany dynamically
        $now = new \DateTime('now', new \DateTimeZone('Europe/Berlin'));
        $offset = $now->format('P'); // Returns "+01:00" or "+02:00" automatically

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci, time_zone = '{$offset}'",
        ];

        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            $message = Config::isDev()
                ? "Database connection failed: {$e->getMessage()}"
                : "Database connection failed. Please contact support.";

            throw new RuntimeException($message, (int) $e->getCode(), $e);
        }
    }

    /** Prevent cloning of the singleton. */
    private function __clone() {}

    /** Returns the single Database instance. */
    public static function getInstance(): static
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /** Returns the underlying PDO connection. */
    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    // Convenience query helpers — used by all Manager classes to avoid boilerplate PDO code

    /**
     * Run a SELECT and return every matching row.
     * Example:
     *  $rows = Database::getInstance()->fetchAll(
     *    "SELECT * FROM users WHERE email = :email",
     *   [':email' => $email]
     * );
     *
     * @param  string $sql
     * @param  array  $params  Named bound parameters, e.g. [':id' => 1]
     * @return array<int, array<string, mixed>>
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Run a SELECT and return the first row, or null if nothing matched.
     *
     * @return array<string, mixed>|null
     */
    public function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Run an UPDATE / DELETE and return the number of affected rows.
     */
    public function execute(string $sql, array $params = []): int
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Run an INSERT and return the new auto-increment ID.
     */
    public function insert(string $sql, array $params = []): int
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Wrap a callable in a transaction; rolls back automatically on any exception.
     *
     * Usage:
     *   $id = Database::getInstance()->transaction(function () use ($data) {
     *       // multiple inserts / updates here
     *       return $newId;
     *   });
     *
     * @throws \Throwable 
     */
    public function transaction(callable $callback): mixed
    {
        $this->pdo->beginTransaction();

        try {
            $result = $callback();
            $this->pdo->commit();
            return $result;
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}