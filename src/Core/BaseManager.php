<?php

namespace App\Core;

/**
 * BaseManager
 *
 * The instance-level foundation for every Manager class.
 * It extends DbEntityBase (static SQL builder) and adds:
 *   • A bound $db handle and a $table declaration contract
 *   • Instance wrappers for every DbEntityBase static method so callers
 *     never need to pass the table name explicitly
 */
abstract class BaseManager extends DbEntityBase
{
    protected Database $db;

    /**
     * Every concrete Manager declares which table it owns.
     * Use the constants from Database::TABLE_* for safety.
     */
    abstract protected function getTable(): string;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Find a single record by its primary key (`id`).
     *
     * @return array<string, mixed>|null
     */
    public function findById(int $id): ?array
    {
        return parent::selectOneWithTable(
            $this->getTable(), 
            't.*', 
            ['t.id = :id'], 
            [':id' => $id]
        );
    }

    /**
     * Fetch every row in the table.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findAll(string $orderBy = ''): array
    {
        return parent::selectWithTable($this->getTable(), 't.*', [], [], $orderBy);
    }

    /**
     * Insert a row and return the new auto-increment ID.
     *
     * @param array<string, mixed> $data  Column → value map.
     */
    public function create(array $data): int
    {
        return parent::rawInsert($this->getTable(), $data);
    }

    /**
     * Update a row by its primary key (`id`).
     * Returns the number of affected rows.
     *
     * @param array<string, mixed> $data  Columns to update.
     */
    public function updateById(int $id, array $data): int
    {
        return parent::rawUpdateTable($this->getTable(), $data, 'id', $id);
    }

    /**
     * Hard-delete a row by its primary key (`id`).
     * Returns the number of affected rows.
     */
    public function deleteById(int $id): int
    {
        return parent::rawDeleteTable($this->getTable(), 'id', $id);
    }

    /**
     * Fetch rows matching arbitrary WHERE conditions.
     *
     * @param string[] $whereConditions  Raw SQL snippets joined with AND.
     * @param array    $params           Bound parameter map.
     * @param string   $orderBy          Raw ORDER BY expression (optional).
     * @param string   $selectCols       Column projection (default t.*).
     * @param string[] $joins            Additional JOIN clauses (optional).
     *
     * @return array<int, array<string, mixed>>
     */
    public function findWhere(
        array  $whereConditions,
        array  $params      = [],
        string $orderBy     = '',
        string $selectCols  = 't.*',
        array  $joins       = []
    ): array {
        return parent::selectWithTable(
            $this->getTable(), $selectCols, $whereConditions, $params, $orderBy, $joins
        );
    }

    /**
     * Find the first row matching arbitrary WHERE conditions, or null if nothing matches.
     *
     * @return array<string, mixed>|null
     */
    public function findOneWhere(
        array  $whereConditions,
        array  $params     = [],
        string $orderBy    = '',
        string $selectCols = 't.*',
        array  $joins      = []
    ): ?array {
        return parent::selectOneWithTable(
            $this->getTable(), $selectCols, $whereConditions, $params, $orderBy, $joins
        );
    }

    /**
     * Return the total number of rows in the table.
     */
    public function count(): int
    {
        return parent::rawCountTable($this->getTable());
    }

    /**
     * Count rows matching arbitrary WHERE conditions.
     *
     * @param string[] $whereConditions
     */
    public function countWhere(array $whereConditions = [], array $params = []): int
    {
        return parent::rawCountTable($this->getTable(), $whereConditions, $params);
    }

    /**
     * Return true if at least one row matches the given WHERE conditions.
     *
     * @param string[] $whereConditions
     */
    public function exists(array $whereConditions, array $params): bool
    {
        return parent::rawExists($this->getTable(), $whereConditions, $params);
    }

    /**
     * Insert multiple rows in a single query.
     * All rows must have the same column set.
     *
     * @param array<int, array<string, mixed>> $rows
     */
    public function bulkInsert(array $rows): int
    {
        return parent::rawBulkInsert($this->getTable(), $rows);
    }

    /**
     * Synchronize a record: Insert or Update on duplicate key conflict.
     *
     * @param array<string, mixed> $data
     * @param string[]             $updateColumns  Columns to overwrite on conflict.
     */
    public function sync(array $data, array $updateColumns): int
    {
        return parent::syncRecordWithTable($this->getTable(), $data, $updateColumns);
    }

    /**
     * Update rows matching arbitrary WHERE conditions.
     *
     * @param array<string, mixed> $data
     * @param string[]             $whereConditions
     */
    public function updateWhere(array $data, array $whereConditions, array $whereParams = []): int
    {
        return parent::rawUpdateTableWhere($this->getTable(), $data, $whereConditions, $whereParams);
    }

    /**
     * Delete rows matching arbitrary WHERE conditions.
     *
     * @param string[] $whereConditions
     */
    public function deleteWhere(array $whereConditions, array $params): int
    {
        return parent::rawDeleteTableWhere($this->getTable(), $whereConditions, $params);
    }

    /**
     * Whitelist-filter $data to only columns allowed for this Manager.
     *
     * @param array<string, mixed> $data
     * @param string[]             $allowed
     *
     * @return array<string, mixed>
     */
    protected function filterFillable(array $data, array $allowed): array
    {
        return parent::filterColumns($data, $allowed);
    }
}