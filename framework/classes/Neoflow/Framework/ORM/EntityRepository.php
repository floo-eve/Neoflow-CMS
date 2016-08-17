<?php

namespace Neoflow\Framework\ORM;

use Exception;
use InvalidArgumentException;
use Neoflow\Framework\Common\Collection;
use Neoflow\Framework\Persistence\QueryBuilder;
use Neoflow\Framework\Persistence\Querying\DeleteQuery;
use Neoflow\Framework\Persistence\Querying\InsertQuery;
use Neoflow\Framework\Persistence\Querying\SelectQuery;
use Neoflow\Framework\Persistence\Querying\UpdateQuery;

class EntityRepository
{

    /**
     * Load app.
     */
    use \Neoflow\Framework\AppTrait;

    /**
     * @var string
     */
    protected $modelClassName;

    /**
     * @var QueryBuilder|SelectQuery|DeleteQuery|UpdateQuery|InsertQuery
     */
    protected $query;

    /**
     * Get query of query builder
     *
     * @return SelectQuery
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set repository for model entity.
     *
     * @param string $modelClassName
     * @param bool $asSelect
     *
     * @return self
     *
     * @throws InvalidArgumentException
     */
    public function forModel($modelClassName, $asSelect = true)
    {
        $this->reset();

        if (class_exists($modelClassName)) {
            $this->modelClassName = $modelClassName;

            $this->query = new QueryBuilder();

            if ($asSelect) {
                $this->query = $this->query
                    ->selectFrom($this->getTableName())
                    ->setPrimaryKey($this->getPrimaryKey())
                    ->asObject($modelClassName);

                $caching = $this->config()->get('orm')->get('caching');
                $this->caching($caching);
            }

            return $this;
        }
        throw new InvalidArgumentException('Model class not found');
    }

    /**
     * Delete model entity
     *
     * @param AbstractEntityModel $entity
     *
     * @return int|bool
     */
    public function delete(AbstractEntityModel $entity)
    {
        $this->forModel(get_class($entity), false);

        return $this->query
                ->deleteFrom($this->getTableName())
                ->setPrimaryKey($this->getPrimaryKey())
                ->execute($entity->id());
    }

    /**
     * Update model entity
     *
     * @param AbstractEntityModel $entity
     *
     * @return int|bool
     */
    public function update(AbstractEntityModel $entity)
    {
        $this->forModel(get_class($entity), false);

        return $this->query
                ->update($this->getTableName())
                ->setPrimaryKey($this->getPrimaryKey())
                ->set($entity->getModifiedData())
                ->execute($entity->id());
    }

    /**
     * Save model entity.
     *
     * @return int|bool|string
     */
    public function save(AbstractEntityModel $entity)
    {
        if ($entity->id()) {
            return $this->update($entity);
        }

        $this->forModel(get_class($entity), false);

        return $this->query
                ->insertInto($this->getTableName())
                ->values($entity->getData())
                ->execute();
    }

    /**
     * Persist model entity.
     *
     * @return int|bool|string
     */
    public function persist(AbstractEntityModel $entity)
    {
        return $this->save($entity);
    }

    /**
     * Add ORDER BY ASC for column.
     *
     * @param string $column
     *
     * @return self
     */
    public function orderByAsc($column)
    {
        $this->query->orderByAsc($column);

        return $this;
    }

    /**
     * Add ORDER BY ASC for column.
     *
     * @param string $column
     *
     * @return self
     */
    public function orderByDesc($column)
    {
        $this->query->orderByDesc($column);

        return $this;
    }

    /**
     * Add raw ORDER BY statement.
     *
     * @param string $statement
     *
     * @return self
     */
    public function orderByRaw($statement)
    {
        $this->query->orderByRaw($statement);

        return $this;
    }

    /**
     * Enable/disable caching.
     *
     * @return self
     */
    public function caching($caching = true)
    {
        $this->query->caching($caching);

        return $this;
    }

    /**
     * Add LIMIT.
     *
     * @param string $limit
     *
     * @return self
     */
    public function limit($limit)
    {
        $this->query->limit($limit);

        return $this;
    }

    /**
     * Add OFFSET.
     *
     * @param string $offset
     *
     * @return self
     */
    public function offset($offset)
    {
        $this->query->offset($offset);

        return $this;
    }

    /**
     * Add raw WHERE condition.
     *
     * @param string $condition
     * @param array  $parameters
     *
     * @return self
     */
    public function whereRaw($condition, array $parameters = array())
    {
        $this->query->whereRaw($condition, $parameters);

        return $this;
    }

    /**
     * Add where condition.
     *
     * @param string $property
     * @param string $operator
     * @param mixed  $parameter
     *
     * @return self
     */
    public function where($property, $operator, $parameter)
    {
        $this->query->where($property, $operator, $parameter);

        return $this;
    }

    /**
     * Find many model entities.
     *
     * @return EntityCollection|bool
     */
    public function fetchAll()
    {
        // Execute query
        $result = $this->query->fetchAll();

        // Reset entity repository
        $this->reset();

        // Create entity collection
        if ($result instanceof Collection) {
            $result = new EntityCollection($result->toArray());
        }

        // Return result
        return $result;
    }

    /**
     * Find one model enity.
     *
     * @param int|string $id
     *
     * @return AbstractEntityModel
     */
    public function fetch($id = false)
    {
        // Execute query
        $result = $this->query->fetch($id);

        // Reset entity repository
        $this->reset();

        // Return result
        return $result;
    }

    /**
     * Count model entities.
     *
     * @return int
     */
    public function count()
    {
        // Execute query
        $result = $this->query->count();

        // Reset entity repository
        $this->reset();

        // Return result
        return $result;
    }

    /**
     * Get table name of model.
     *
     * @param string $modelClassName
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    protected function getTableName($modelClassName = null)
    {
        if (!$modelClassName) {
            $modelClassName = $this->modelClassName;
        }

        if (class_exists($modelClassName)) {
            return $modelClassName::$tableName;
        }

        throw new Exception('Model class not found: ' . $modelClassName);
    }

    /**
     * Get primary key of model.
     *
     * @param string $modelClassName
     *
     * @return string
     *
     * @throws Exception
     */
    protected function getPrimaryKey($modelClassName = null)
    {
        if (!$modelClassName) {
            $modelClassName = $this->modelClassName;
        }

        if (class_exists($modelClassName)) {
            return $modelClassName::$primaryKey;
        }

        throw new Exception('Model class not found: ' . $modelClassName);
    }

    /**
     * Reset entity repository
     */
    protected function reset()
    {
        $this->modelClassName = null;
        $this->query = new QueryBuilder();
    }
}
