<?php

namespace Core\Database;

use PDO;

class QueryBuilder
{
    protected $pdo;
    protected $table;
    protected $columns = '*';
    protected $conditions = [];
    protected $bindings = [];
    protected $limit;
    protected $offset;
    protected $orderBy;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function table(string $table)
    {
        $this->table = $table;
        return $this;
    }

    public function select(string $columns = '*')
    {
        $this->columns = $columns;
        return $this;
    }

    public function where(string $column, string $operator, $value)
    {
        $this->conditions[] = "$column $operator ?";
        $this->bindings[] = $value;
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC')
    {
        $this->orderBy = "ORDER BY $column $direction";
        return $this;
    }

    public function limit(int $limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset)
    {
        $this->offset = $offset;
        return $this;
    }

    public function get()
    {
        $sql = "SELECT {$this->columns} FROM {$this->table}";

        if ($this->conditions) {
            $sql .= " WHERE " . implode(' AND ', $this->conditions);
        }

        if ($this->orderBy) {
            $sql .= " " . $this->orderBy;
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }

        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert(array $data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_values($data));
        return $this->pdo->lastInsertId();
    }

    public function update(array $data)
    {
        $setClause = implode(', ', array_map(fn($key) => "$key = ?", array_keys($data)));
        $sql = "UPDATE {$this->table} SET $setClause";
        
        if ($this->conditions) {
            $sql .= " WHERE " . implode(' AND ', $this->conditions);
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_merge(array_values($data), $this->bindings));
        return $stmt->rowCount();
    }

    public function delete()
    {
        $sql = "DELETE FROM {$this->table}";
        
        if ($this->conditions) {
            $sql .= " WHERE " . implode(' AND ', $this->conditions);
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);
        return $stmt->rowCount();
    }

    public function first()
    {
        $this->limit = 1;
        $results = $this->get();
        return $results ? $results[0] : null;
    }

    public function join(string $table, string $first, string $operator, string $second, string $type = 'INNER')
    {
        $joinClause = "$type JOIN $table ON $first $operator $second";
        $this->conditions[] = $joinClause;
        return $this;
    }

    
    public function paginate(int $perPage, int $currentPage = 1)
    {
        $this->limit = $perPage;
        $this->offset = ($currentPage - 1) * $perPage;
        return $this->get();
    }

}