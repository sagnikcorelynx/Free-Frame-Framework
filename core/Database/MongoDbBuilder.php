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
     * @return array
     */
    public function first(array $conditions = [])
    {
        $cursor = $this->get($conditions);
        $result = $cursor->toArray();
        return reset($result);
    }

    
    /**
     * @param array $pipeline
     * @return \MongoDB\Driver\Cursor
     */
    public function aggregate(array $pipeline)
    {
        return $this->manager->executeAggregate($this->collectionName, $pipeline);
    }

    
    /**
     * @param string $collection
     * @param string $foreign_key
     * @param string $local_key
     * @return \MongoDB\Driver\Cursor
     */
    public function join(string $collection, string $foreign_key, string $local_key)
    {
        $pipeline = [
            [
                '$lookup' => [
                    'from' => $collection,
                    'localField' => $local_key,
                    'foreignField' => $foreign_key,
                    'as' => $collection,
                ],
            ],
        ];
        return $this->aggregate($pipeline);
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
     * @param array $data
     * @return \MongoDB\Driver\WriteResult
     */
    public function bulkInsert(array $data)
    {
        $bulkWrite = array_map(function ($item) {
            return [
                'insertOne' => [
                    'document' => $item,
                ],
            ];
        }, $data);
        
        return $this->manager->executeBulkWrite($this->collectionName, $bulkWrite);
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

    
    /**
     * @param string $column
     * @param array $pipeline
     * @return \MongoDB\Driver\Cursor
     */
    public function groupBy(string $column, array $pipeline)
    {
        $pipeline = array_merge([
            ['$group' => [
                '_id' => '$' . $column,
                'count' => ['$sum' => 1],
            ]],
        ], $pipeline);

        $cursor = $this->manager->executeAggregate($this->collectionName, $pipeline);
        return $cursor;
    }

    
    /**
     * @param string $column
     * @return $this
     */
    public function latest(string $column = 'created_at')
    {
        $this->options['sort'] = [
            $column => -1,
        ];
        return $this;
    }
}
