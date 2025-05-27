<?php

namespace Core;

use PDO;
use PDOException;
use MongoDB\Driver\Exception\Exception as MongoDBException;
use MongoDB\Driver\Manager as MongoDBManager;
use MongoDB\Driver\Query as MongoDBQuery;
use MongoDB\Driver\Cursor as MongoDBCursor;
use Core\Database\QueryBuilder;
use Core\Traits\SoftDeletes;

class Model extends QueryBuilder
{
    use SoftDeletes;
    /**
     * @var PDO|null
     */
    protected static $connection;
    protected $table;

    /**
     * Model constructor.
     *
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        
        if (!$this->table) {
            $this->table = strtolower((new \ReflectionClass($this))->getShortName()) . 's';
        }

        $this->table($this->table); // Set default table
        
        if (!self::$connection) {
            $this->connect();
        }
    }

    /**
     * Connect to the database.
     *
     * @return void
     */
    protected function connect()
    {
        $config = require __DIR__ . '/../config/database.php';

        if ($config['driver'] === 'mysql') {
            $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset=utf8";
            try {
                self::$connection = new PDO($dsn, $config['username'], $config['password']);
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                error_log($e->getMessage(), 3, __DIR__ . '/../Storage/Logs/error.log');
                die("Database connection error");
            }
        } else {
            $dsn = "mongodb://{$config['host']}:{$config['port']}";
            try {
                self::$connection = new \MongoDB\Driver\Manager($dsn);
            } catch (\MongoDB\Driver\Exception\Exception $e) {
                error_log($e->getMessage(), 3, __DIR__ . '/../Storage/Logs/error.log');
                die("Database connection error");
            }
        }
    }

    public static function getConnection()
    {
        return self::$connection;
    }

    /**
     * Retrieve all records from the current table.
     *
     * @return array An array of associative arrays representing all records.
     */

    public function all()
    {
        $stmt = self::$connection->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieve a single record from the current table by its id.
     *
     * @param int $id The id of the record to retrieve.
     *
     * @return array|null An associative array representing the record, or null if no record is found.
     */
    public function find($id)
    {
        $stmt = self::$connection->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieve a related model instance given a foreign key.
     *
     * @param string $relatedModel The fully qualified name of the related model class.
     * @param string $foreignKey The foreign key of the related model.
     * @param string $localKey The local key of the current model.
     *
     * @return Model|null The related model instance, or null if no record is found.
     */
    public function hasOnlyOne($relatedModel, $foreignKey, $localKey = 'id')
    {
        $relatedInstance = new $relatedModel;
        return $relatedInstance->where($foreignKey, $this->$localKey)->first();
    }

    /**
     * Retrieve all related model instances given a foreign key.
     *
     * @param string $relatedModel The fully qualified name of the related model class.
     * @param string $foreignKey The foreign key of the related model.
     * @param string $localKey The local key of the current model.
     *
     * @return array An array of related model instances.
     */

    public function hasManyMore($relatedModel, $foreignKey, $localKey = 'id')
    {
        $relatedInstance = new $relatedModel;
        return $relatedInstance->where($foreignKey, $this->$localKey)->get();
    }

    /**
     * Retrieve the related model instance given a foreign key.
     *
     * @param string $relatedModel The fully qualified name of the related model class.
     * @param string $foreignKey The foreign key of the current model.
     * @param string $ownerKey The owner key of the related model.
     *
     * @return Model|null The related model instance, or null if no record is found.
     */
    public function belongsToOnly($relatedModel, $foreignKey, $ownerKey = 'id')
    {
        $relatedInstance = new $relatedModel;
        return $relatedInstance->where($ownerKey, $this->$foreignKey)->first();
    }
}