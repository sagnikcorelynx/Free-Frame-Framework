<?php
namespace Core\Database\Cassandra;

class CassandraQueryBuilder
{
    protected \Cassandra\Session $session;
    protected string $table;

    public function __construct(string $table)
    {
        $this->session = CassandraConnection::connect();
        $this->table = $table;
    }

    public function insert(array $data): bool
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $query = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $statement = new \Cassandra\SimpleStatement($query);

        $options = ['arguments' => array_values($data)];

        $this->session->execute($statement, new \Cassandra\ExecutionOptions($options));
        return true;
    }

    public function where(string $column, $value): self
    {
        $this->where = [$column => $value];
        return $this;
    }

    public function get(): array
    {
        $whereSql = '';
        $params = [];

        if (!empty($this->where)) {
            foreach ($this->where as $col => $val) {
                $whereSql = " WHERE $col = ?";
                $params[] = $val;
            }
        }

        $query = "SELECT * FROM {$this->table}" . $whereSql;
        $statement = new \Cassandra\SimpleStatement($query);
        $options = ['arguments' => $params];

        $result = $this->session->execute($statement, new \Cassandra\ExecutionOptions($options));

        $rows = [];
        foreach ($result as $row) {
            $rows[] = $row;
        }

        return $rows;
    }

    public function update(array $data): bool
    {
        $setSql = '';
        $params = [];

        foreach ($data as $column => $value) {
            $setSql .= "$column = ?, ";
            $params[] = $value;
        }
        $setSql = rtrim($setSql, ', ');

        $query = "UPDATE {$this->table} SET $setSql";

        if (!empty($this->where)) {
            $whereSql = '';
            foreach ($this->where as $col => $val) {
                $whereSql .= " WHERE $col = ?";
                $params[] = $val;
            }
            $query .= $whereSql;
        }

        $statement = new \Cassandra\SimpleStatement($query);
        $options = ['arguments' => $params];

        $this->session->execute($statement, new \Cassandra\ExecutionOptions($options));
        return true;
    }

    public function delete(): bool
    {
        $whereSql = '';
        $params = [];

        if (!empty($this->where)) {
            foreach ($this->where as $col => $val) {
                $whereSql = " WHERE $col = ?";
                $params[] = $val;
            }
        }

        $query = "DELETE FROM {$this->table}" . $whereSql;
        $statement = new \Cassandra\SimpleStatement($query);
        $options = ['arguments' => $params];

        $this->session->execute($statement, new \Cassandra\ExecutionOptions($options));
        return true;
    }

    
    public function paginate(int $perPage, int $currentPage = 1)
    {
        $offset = ($currentPage - 1) * $perPage;
        $statement = new \Cassandra\SimpleStatement("SELECT * FROM {$this->table} LIMIT $perPage OFFSET $offset");
        
        $options = ['arguments' => []];
        
        $result = $this->session->execute($statement, new \Cassandra\ExecutionOptions($options));

        $rows = [];
        foreach ($result as $row) {
            $rows[] = $row;
        }

        return $rows;
    }

    
    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function latest(string $column = 'created_at'): self
    {
        $this->orderBy = "$column DESC";
        return $this;
    }

    
    public function groupBy(string $column): self
    {
        $this->groupBy = $column;
        return $this;
    }

}