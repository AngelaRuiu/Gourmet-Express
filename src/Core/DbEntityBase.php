<?php

namespace App\Core;

use PDO;
use App\Core\Database;

/**
 * DbEntityBase
 * Provides centralized helper methods for building and executing database queries.
 * This class is not meant to be instantiated directly, but rather extended by specific entity classes (ex., User, MenuItem) that represent database tables.
 * 
 */
abstract class DbEntityBase
{
      
    /**
     * Build a SELECT query with the given parameters.
     *
     * @param string   $tableName        Bare table name, ex. 'orders'
     * @param string   $selectCols       Column expression, ex. 't.*' or 't.id, t.name'
     * @param string[] $whereConditions  Raw SQL snippets joined with AND,
     *                                   ex. ['t.is_active = :active', 't.role = :role']
     * @param string   $orderBy          Raw ORDER BY expression, ex. 't.created_at DESC'
     * @param string[] $joins            Raw JOIN clauses, ex. ['LEFT JOIN categories c ON c.id = t.category_id']
     * @param bool     $distinct         Whether to add DISTINCT to the SELECT
     *
     * @return string The complete SQL query string.
     */
    
    protected static function buildSelectQuery(
        string $tableName,
        string $selectCols,
        array $whereConditions,
        string $orderBy,
        array $joins,
        bool $distinct
    ): string {
        $distinctStr = $distinct ? "DISTINCT " : "";
        $sql = "SELECT {$distinctStr}{$selectCols} FROM {$tableName} AS t";

        if (!empty($joins)) {
            $sql .= " " . implode(" ", $joins);
        }

        if (!empty($whereConditions)) {
            $sql .= " WHERE " . implode(" AND ", $whereConditions);
        }

        if (!empty($orderBy)) {
            $sql .= " ORDER BY " . $orderBy;
        }

        return $sql;
    }

     /**
     * Add a colon prefix to every key in an associative array so it can be used
     * directly as a PDO named-parameter map.
     *
     * ['name' => 'Alice'] → [':name' => 'Alice']
     *
     * @param  array<string, mixed> $data
     * @return array<string, mixed>
     */
    private static function prefixKeys(array $data): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            $result[":{$key}"] = $value;
        }
        return $result;
    }

    /**
     * Execute a fully parameterised SELECT and return all matching rows.
     *
     * @param string   $tableName        Bare table name, ex. 'orders'
     * @param string   $selectCols       Column expression, ex. 't.*' or 't.id, t.name'
     * @param string[] $whereConditions  Raw SQL snippets joined with AND,
     *                                   ex. ['t.is_active = :active', 't.role = :role']
     * @param array    $params           Bound parameter map, ex. [':active' => 1]
     * @param string   $orderBy          Raw ORDER BY expression, ex. 't.created_at DESC'
     * @param string[] $joins            Raw JOIN clauses, ex. ['LEFT JOIN categories c ON c.id = t.category_id']
     * @param bool     $distinct         Whether to add DISTINCT to the SELECT
     *
     * @return array<int, array<string, mixed>>
     */
    public static function selectWithTable(
        string $tableName,
        string $selectCols = "t.*",
        array $whereConditions = [],
        array $params = [],
        string $orderBy = "",
        array $joins = [],
        bool $distinct = false
    ): array {
        $db = Database::getInstance();

        $sql = self::buildSelectQuery(
            $tableName,
            $selectCols,
            $whereConditions,
            $orderBy,
            $joins,
            $distinct
        );

        return $db->fetchAll($sql, $params);
    }

     /**
     * Like selectWithTable() but returns only the first row, or null.
     *
     * @return array<string, mixed>|null
     */
    public static function selectOneWithTable(
        string $tableName,
        string $selectCols       = 't.*',
        array  $whereConditions  = [],
        array  $params           = [],
        string $orderBy          = '',
        array  $joins            = []
    ): ?array {
        $sql = self::buildSelectQuery($tableName, $selectCols, $whereConditions, $orderBy, $joins, false);
        $sql .= ' LIMIT 1';
        return Database::getInstance()->fetchOne($sql, $params);
    }

    /**
     * Return the count of rows matching the given conditions.
     *
     * @param string[] $whereConditions
     */
    public static function rawCountTable(
        string $tableName,
        array  $whereConditions = [],
        array  $params          = [],
        array  $joins           = []
    ): int {
        $sql = self::buildSelectQuery(
            $tableName, 'COUNT(*) AS `total`', $whereConditions, '', $joins, false
        );
        $row = Database::getInstance()->fetchOne($sql, $params);
        return (int) ($row['total'] ?? 0);
    }


    /**
     * Return true if at least one row matches the given conditions.
     *
     * @param string[] $whereConditions
     */
    public static function rawExists(
        string $tableName,
        array  $whereConditions,
        array  $params
    ): bool {
        return self::rawCountTable($tableName, $whereConditions, $params) > 0;
    }


    /**
     * Insert a single row and return the new auto-increment ID.
     *
     * @param array<string, mixed> $data  Associative column values, ex. ['name' => 'Alice', 'email' => 'alice@example.com']
     *                                    Keys must be plain column names (no colons).
     */
    public static function rawInsert(string $tableName, array $data): int
    {
        $cols         = array_keys($data);
        $colList      = implode(', ', array_map(fn($c) => "`{$c}`", $cols));
        $paramList    = implode(', ', array_map(fn($c) => ":{$c}", $cols));
        $sql          = "INSERT INTO `{$tableName}` ({$colList}) VALUES ({$paramList})";
        $boundParams  = self::prefixKeys($data);
 
        return Database::getInstance()->insert($sql, $boundParams);
    }

     /**
     * Insert multiple rows in a single statement for performance.
     * All rows must share the same column set.
     * Example usage:
     *  $rows = [
     *    ['name' => 'Alice', 'email' => 'alice@example.com'],
     *    ['name' => 'Bob', 'email' => 'bob@example.com'],
     * ];
     * Database::rawBulkInsert('users', $rows);
     * @param array<int, array<string, mixed>> $rows  Array of column → value maps.
     *
     * @throws \InvalidArgumentException  If $rows is empty or columns differ between rows.
     */
    public static function rawBulkInsert(string $tableName, array $rows): int
    {
        if (empty($rows)) {
            throw new \InvalidArgumentException('rawBulkInsert requires at least one row.');
        }
 
        $cols      = array_keys($rows[0]);
        $colList   = implode(', ', array_map(fn($c) => "`{$c}`", $cols));
        $params    = [];
        $valueSets = [];
 
        foreach ($rows as $i => $row) {
            $placeholders = [];
            foreach ($cols as $col) {
                $key              = ":{$col}_{$i}";
                $placeholders[]   = $key;
                $params[$key]     = $row[$col] ?? null;
            }
            $valueSets[] = '(' . implode(', ', $placeholders) . ')';
        }
 
        $sql = "INSERT INTO `{$tableName}` ({$colList}) VALUES " . implode(', ', $valueSets);
        return Database::getInstance()->execute($sql, $params);
    }

    /**
     * Executes an "Upsert" operation (Insert or Update on Duplicate Key).
     * 
     * This method attempts to insert a record. If a conflict occurs on a 
     * Primary or Unique key, it performs an update on the specified columns instead.
     * To be used when wanna ensure a record exists with certain values, regardless of whether it's new or existing.
     *
     * @param string $tableName     The target table name.
     * @param array  $data          Associative array of column => value for the INSERT.
     * @param array  $updateColumns List of columns to overwrite if the record already exists.
     * @return int                  The number of affected rows (1 for insert, 2 for update).
     */
    public static function syncRecordWithTable(string $tableName, array $data, array $updateColumns): int
    {
        $cols        = array_keys($data);
        $colList     = implode(', ', array_map(fn($c) => "`{$c}`", $cols));
        $paramList   = implode(', ', array_map(fn($c) => ":{$c}", $cols));
        $boundParams = self::prefixKeys($data);
 
        $updateParts = array_map(fn($c) => "`{$c}` = VALUES(`{$c}`)", $updateColumns);
        $updateClause = implode(', ', $updateParts);
 
        $sql = "INSERT INTO `{$tableName}` ({$colList}) VALUES ({$paramList})
                ON DUPLICATE KEY UPDATE {$updateClause}";
 
        return Database::getInstance()->execute($sql, $boundParams);
    }

    /**
     * Update rows by a single column condition.
     *
     * @param array<string, mixed> $data      Columns to update (column → value).
     * @param string               $idColumn  Column name of the WHERE condition.
     * @param mixed                $idValue   Value to match in the WHERE condition.
     */
    public static function rawUpdateTable(
        string $tableName,
        array  $data,
        string $idColumn,
        mixed  $idValue
    ): int {
        $setParts  = array_map(fn($col) => "`{$col}` = :{$col}", array_keys($data));
        $setClause = implode(', ', $setParts);
        $sql       = "UPDATE `{$tableName}` SET {$setClause} WHERE `{$idColumn}` = :__target_id";
 
        $params                = self::prefixKeys($data);
        $params[':__target_id'] = $idValue;
 
        return Database::getInstance()->execute($sql, $params);
    }

    /**
     * Update rows matching arbitrary WHERE conditions.
     *
     * @param array<string, mixed> $data             Columns to update.
     * @param string[]             $whereConditions  Raw SQL snippets joined with AND.
     * @param array                $whereParams      Bound params for the WHERE clause.
     */
    public static function rawUpdateTableWhere(
        string $tableName,
        array  $data,
        array  $whereConditions,
        array  $whereParams = []
    ): int {
        if (empty($whereConditions)) {
            throw new \InvalidArgumentException(
                'updateWhere requires at least one WHERE condition to prevent accidental full-table updates.'
            );
        }
 
        $setParts   = array_map(fn($col) => "`{$col}` = :set_{$col}", array_keys($data));
        $setClause  = implode(', ', $setParts);
        $whereClause = implode(' AND ', $whereConditions);
 
        $sql = "UPDATE `{$tableName}` SET {$setClause} WHERE {$whereClause}";
 
        $setParams = [];
        foreach ($data as $col => $val) {
            $setParams[":set_{$col}"] = $val;
        }
 
        return Database::getInstance()->execute($sql, array_merge($setParams, $whereParams));
    }

    /**
     * Delete a single row by a column value.
     *
     * @param string $idColumn  Column name used in WHERE, ex. 'id'.
     * @param mixed  $idValue   Value to match.
     */
    public static function rawDeleteTable(string $tableName, string $idColumn, mixed $idValue): int
    {
        $sql = "DELETE FROM `{$tableName}` WHERE `{$idColumn}` = :id";
        return Database::getInstance()->execute($sql, [':id' => $idValue]);
    }
 
    /**
     * Delete rows matching arbitrary WHERE conditions.
     * Requires at least one condition to prevent accidental full-table deletes.
     *
     * @param string[] $whereConditions
     */
    public static function rawDeleteTableWhere(
        string $tableName,
        array  $whereConditions,
        array  $params
    ): int {
        if (empty($whereConditions)) {
            throw new \InvalidArgumentException(
                'rawDeleteTableWhere requires at least one WHERE condition to prevent accidental full-table deletes.'
            );
        }
 
        $sql = "DELETE FROM `{$tableName}` WHERE " . implode(' AND ', $whereConditions);
        return Database::getInstance()->execute($sql, $params);
    }

    /**
     * Whitelist-filter an associative array so only allowed columns survive.
     *
     * Use this when column names originate from user input (ex. a sort parameter
     * passed via query string) to prevent column-injection attacks.
     *
     * Example:
     *   $safe = DbEntityBase::filterColumns($_reqData, ['name', 'email', 'phone']);
     *
     * @param array<string, mixed> $data            Raw input data.
     * @param string[]             $allowedColumns  Columns that may pass through.
     *
     * @return array<string, mixed>
     */
    public static function filterColumns(array $data, array $allowedColumns): array
    {
        return array_intersect_key($data, array_flip($allowedColumns));
    }
}