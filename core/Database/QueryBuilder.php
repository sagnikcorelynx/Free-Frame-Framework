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

    /**
     * Set the table to be queried
     *
     * @param string $table
     *
     * @return $this
     */
    public function table(string $table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Specify the columns to be selected in the query.
     *
     * @param string $columns The columns to select, separated by commas. Defaults to '*' for all columns.
     *
     * @return $this
     */

    public function select(string $columns = '*')
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * Add a condition to the query
     *
     * @param string $column The column to condition on
     * @param string $operator The operator to use
     * @param mixed $value The value to condition on
     *
     * @return $this
     */
    public function where(string $column, string $operator, $value)
    {
        $this->conditions[] = "$column $operator ?";
        $this->bindings[] = $value;
        return $this;
    }

    /**
     * Add an ORDER BY clause to the query
     *
     * @param string $column The column to order by
     * @param string $direction The direction of the ordering. Defaults to 'ASC' for ascending.
     *
     * @return $this
     */
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

    /**
     * Set the offset for the query results.
     *
     * @param int $offset The number of records to skip.
     *
     * @return $this
     */

    public function offset(int $offset)
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Execute the query as a SELECT statement and return the results as an array of associative arrays.
     *
     * @return array
     */
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

    /**
     * Execute the query as an INSERT statement and return the ID of the last inserted row.
     *
     * @param array $data
     * @return int
     */
    public function insert(array $data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_values($data));
        return $this->pdo->lastInsertId();
    }

    /**
     * Execute the query as an UPDATE statement and return the number of rows affected.
     *
     * @param array $data
     * @return int
     */
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

    /**
     * Execute the query as a DELETE statement and return the number of rows affected.
     *
     * @return int
     */
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

    /**
     * Executes the query and returns the first result.
     *
     * @return mixed|null
     */
    public function first()
    {
        $this->limit = 1;
        $results = $this->get();
        return $results ? $results[0] : null;
    }

    /**
     * Add a join clause to the query.
     *
     * @param string $table The name of the table to join.
     * @param string $first The first column for the join condition.
     * @param string $operator The operator for the join condition (e.g., '=', '>', etc.).
     * @param string $second The second column for the join condition.
     * @param string $type The type of join (e.g., 'INNER', 'LEFT'). Defaults to 'INNER'.
     * @return $this
     */

    public function join(string $table, string $first, string $operator, string $second, string $type = 'INNER')
    {
        $joinClause = "$type JOIN $table ON $first $operator $second";
        $this->conditions[] = $joinClause;
        return $this;
    }

    
    /**
     * Paginate the query results.
     *
     * @param int $perPage The number of results to display per page.
     * @param int $currentPage The current page number. Defaults to 1.
     *
     * @return array The paginated results as an array of associative arrays.
     */

    public function paginate(int $perPage, int $currentPage = 1)
    {
        $this->limit = $perPage;
        $this->offset = ($currentPage - 1) * $perPage;
        return $this->get();
    }

    
    /**
     * Save the given data to the database. If conditions are set, update the existing record(s), 
     * otherwise insert a new record.
     *
     * @param array $data The data to be saved to the database.
     * @return int|string The number of rows affected for an update or the ID of the last inserted row for an insert.
     */

    public function save(array $data)
    {
        if (!empty($this->conditions)) {
            return $this->update($data);
        }
        return $this->insert($data);
    }

    
    /**
     * Execute a union query.
     *
     * @param array $queries The queries to be combined.
     * @param string $type The type of union query to run. Defaults to 'union'.
     *
     * @return array The results of the union query.
     */

    public function union(array $queries, string $type = 'union')
    {
        $sql = '';
        foreach ($queries as $i => $query) {
            if ($i > 0) {
                $sql .= " $type ";
            }
            $sql .= $query;
        }
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

}