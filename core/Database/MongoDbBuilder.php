<?php

namespace Core\Database;

use Core\Model;
use MongoDB\Driver\Manager;
use MongoDB\Driver\Query;

class MongoDbBuilder
{
    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var string
     */
    protected $collectionName;

    /**
     * MongoDbBuilder constructor.
     * @param Model $model
     * @param Manager $manager
     */
    public function __construct(Model $model, Manager $manager)
    {
        $this->model = $model;
        $this->manager = $manager;
        $this->collectionName = $model->getTable();
    }

    /**
     * @param array $conditions
     * @param array $fields
     * @param array $options
     * @return \MongoDB\Driver\Cursor
     */
    public function get(array $conditions = [], array $fields = [], array $options = [])
    {
        $query = new Query($conditions, $fields);
        return $this->manager->executeQuery($this->collectionName, $query);
    }

    /**
     * @param array $conditions
     * @return int
     */
    public function count(array $conditions = [])
    {
        return $this->manager->executeCommand($this->collectionName, [
            'count' => $this->collectionName,
            'query' => $conditions,
        ]);
    }

    /**
     * @param array $data
     * @return \MongoDB\Driver\WriteResult
     */
    public function create(array $data)
    {
        return $this->manager->executeBulkWrite($this->collectionName, [
            [
                'insertOne' => [
                    'document' => $data,
                ],
            ],
        ]);
    }

    /**
     * @param array $conditions
     * @param array $data
     * @return \MongoDB\Driver\WriteResult
     */
    public function update(array $conditions, array $data)
    {
        return $this->manager->executeBulkWrite($this->collectionName, [
            [
                'updateOne' => [
                    'filter' => $conditions,
                    'update' => ['$set' => $data],
                    'upsert' => false,
                ],
            ],
        ]);
    }

    /**
     * @param array $conditions
     * @return \MongoDB\Driver\WriteResult
     */
    public function delete(array $conditions)
    {
        return $this->manager->executeBulkWrite($this->collectionName, [
            [
                'deleteOne' => [
                    'filter' => $conditions,
                ],
            ],
        ]);
    }

    
    /**
     * @param array $conditions
     * @param int $limit
     * @param int $skip
     * @return \MongoDB\Driver\Cursor
     */
    public function paginate(array $conditions = [], int $limit = 10, int $skip = 0)
    {
        $query = new Query($conditions, [], ['limit' => $limit, 'skip' => $skip]);
        return $this->manager->executeQuery($this->collectionName, $query);
    }

    
    /**
     * @return \MongoDB\Driver\WriteResult
     */
    public function truncate()
    {
        return $this->manager->executeCommand($this->collectionName, [
            'deleteMany' => [
                'filter' => [],
            ],
        ]);
    }

    
    /**
     * @param int $limit
     * @return $this
     */
    public function limit(int $limit)
    {
        $this->options['limit'] = $limit;
        return $this;
    }
}
