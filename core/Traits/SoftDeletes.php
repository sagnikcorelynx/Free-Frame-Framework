<?php

namespace Core\Traits;

use DateTime;

trait SoftDeletes
{
    public function delete(): bool
    {
        if (!property_exists($this, 'table')) {
            throw new \Exception("Property \$table must be defined in the model using SoftDeletes.");
        }

        $now = (new DateTime())->format('Y-m-d H:i:s');

        $query = "UPDATE {$this->table} SET deleted_at = :deleted_at WHERE id = :id";
        $stmt = $this->getConnection()->prepare($query);
        return $stmt->execute([
            'deleted_at' => $now,
            'id' => $this->attributes['id']
        ]);
    }

    public static function withTrashed()
    {
        return static::query(false); // include deleted
    }

    public static function onlyTrashed()
    {
        return static::query(true); // only deleted
    }

    protected static function query($onlyDeleted = false)
    {
        $instance = new static;
        $sql = "SELECT * FROM {$instance->table}";
        if ($onlyDeleted === false) {
            $sql .= " WHERE deleted_at IS NULL";
        } elseif ($onlyDeleted === true) {
            $sql .= " WHERE deleted_at IS NOT NULL";
        }

        $stmt = $instance->getConnection()->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function restore(): bool
    {
        $query = "UPDATE {$this->table} SET deleted_at = NULL WHERE id = :id";
        $stmt = $this->getConnection()->prepare($query);
        return $stmt->execute([
            'id' => $this->attributes['id']
        ]);
    }

    public function forceDelete(): bool
    {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->getConnection()->prepare($query);
        return $stmt->execute([
            'id' => $this->attributes['id']
        ]);
    }
}