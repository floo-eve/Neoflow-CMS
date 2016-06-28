<?php

namespace Neoflow\Framework\Persistence;

use Exception;
use InvalidArgumentException;
use Neoflow\Framework\Core\AbstractModel;
use Neoflow\Framework\Persistence\Querying\SelectQuery;

class ORM
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
     * @var SelectQuery
     */
    protected $query;

    /**
     * Set ORM for model.
     *
     * @param string $modelClassName
     * @param int    $id
     *
     * @return ORM
     *
     * @throws InvalidArgumentException
     */
    public function forModel($modelClassName)
    {
        $this->reset();

        if (class_exists($modelClassName)) {
            $this->modelClassName = $modelClassName;

            $queryBuilder = new QueryBuilder();

            $this->query = $queryBuilder
                ->selectFrom($this->getTableName())
                ->setPrimaryKey($this->getPrimaryKey())
                ->asObject($modelClassName);

            return $this;
        }

        throw new InvalidArgumentException('Model class not found: ' . $modelClassName);
    }

    /**
     * Get select query.
     *
     * @return SelectQuery
     */
    public function getSelectQuery()
    {
        return $this->query;
    }

    /**
     * Add GROUP BY for column.
     *
     * @param string $column
     *
     * @return ORM
     */
    public function groupBy($column)
    {
        $this->query->groupBy($column);

        return $this;
    }

    /**
     * Add HAVING for column.
     *
     * @param string $column
     *
     * @return ORM
     */
    public function having($column)
    {
        $this->query->having($column);

        return $this;
    }

    /**
     * Add ORDER BY ASC for column.
     *
     * @param string $column
     *
     * @return ORM
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
     * @return ORM
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
     * @return ORM
     */
    public function orderByRaw($statement)
    {
        $this->query->orderByRaw($statement);

        return $this;
    }

    /**
     * Add LIMIT.
     *
     * @param string $limit
     *
     * @return ORM
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
     * @return ORM
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
     * @return ORM
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
     * @return ORM
     */
    public function where($property, $operator, $parameter)
    {
        $this->query->where($property, $operator, $parameter);

        return $this;
    }

    /**
     * Find many model entities.
     *
     * @return array|bool
     */
    public function fetchAll()
    {
        // Execute query
        $result = $this->query->fetchAll();

        // Reset ORM
        $this->reset();

        // Return result
        return $result;
    }

    /**
     * Find one model enity.
     *
     * @param int|string $id
     *
     * @return AbstractModel
     */
    public function fetch($id = false)
    {
        // Execute query
        $result = $this->query->fetch($id);

        // Reset ORM
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

        // Reset ORM
        $this->reset();

        // Return result
        return $result;
    }

    /**
     * Manage one-to-one and one-to-many relations where the foreign key
     * is on the base model entity.
     *
     * @param Model  $entity
     * @param string $associatedModelClassName
     * @param string $foreignKeyName
     *
     * @return ORM
     */
    public function belongsTo($entity, $associatedModelClassName, $foreignKeyName)
    {
        // Get primary key
        $associatedPrimaryKey = $this->getPrimaryKey($associatedModelClassName);

        //Set ORM for associated model
        if ($this->modelClassName != $associatedModelClassName) {
            $this->forModel($associatedModelClassName);
        }

        //Create where statement
        $this->where($associatedPrimaryKey, '=', $entity->$foreignKeyName);

        // Return ORM
        return $this;
    }

    /**
     * Manage one-to-many relations where the foreign key
     * is on the associated model entity.
     *
     * @param Model  $entity
     * @param string $associatedModelClassName
     * @param string $foreignKeyName
     *
     * @return ORM
     */
    public function hasMany($entity, $associatedModelClassName, $foreignKeyName)
    {
        return $this->hasOneOrMany($entity, $associatedModelClassName, $foreignKeyName);
    }

    /**
     * Manage one-to-one relation where the foreign key
     * is on the associated model entity.
     *
     * @param Model  $entity
     * @param string $associatedModelClassName
     * @param string $foreignKeyName
     *
     * @return ORM
     */
    public function hasOne($entity, $associatedModelClassName, $foreignKeyName)
    {
        return $this->hasOneOrMany($entity, $associatedModelClassName, $foreignKeyName);
    }

    /**
     * Manage one-to-one and one-to-many relations.
     *
     * @param Model  $entity
     * @param string $associatedModelClassName
     * @param string $foreignKeyName
     *
     * @return ORM
     */
    protected function hasOneOrMany($entity, $associatedModelClassName, $foreignKeyName)
    {
        //Set ORM for associated model, create where statement and return ORM
        return $this->forModel($associatedModelClassName)->where($foreignKeyName, '=', $entity->id());
    }

    /**
     * Manage many-to-many relations trought join model.
     *
     * @param Model  $entity
     * @param string $associatedModelClassName
     * @param string $joinModelClassName
     * @param string $foreignKeyToBaseModel
     * @param string $foreignKeyToAssociatedModel
     *
     * @return ORM
     */
    public function hasManyThrough($entity, $associatedModelClassName, $joinModelClassName, $foreignKeyToBaseModel, $foreignKeyToAssociatedModel)
    {
        // Get table names for each class
        $associatedTableName = $this->getTableName($associatedModelClassName);

        $joinTableName = $this->getTableName($joinModelClassName);

        // Get primary key
        $associatedPrimaryKey = $this->getPrimaryKey($associatedModelClassName);

        //Set ORM for associated model
        if ($this->modelClassName != $associatedModelClassName) {
            $this->forModel($associatedModelClassName);
        }

        // Prepare join statment for query
        $this->query
            ->innerJoin($joinTableName, $associatedTableName . '.' . $associatedPrimaryKey . ' = ' . $joinTableName . '.' . $foreignKeyToAssociatedModel)
            ->where($joinTableName . '.' . $foreignKeyToBaseModel, '=', $entity->id());

        // Return ORM
        return $this;
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
     * Reset ORM properties.
     */
    protected function reset()
    {
        $this->modelClassName = null;
    }
}
