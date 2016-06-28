<?php

namespace Neoflow\Framework\Core;

use \DomainException;
use \Neoflow\Framework\Persistence\ORM;
use \Neoflow\Framework\Persistence\QueryBuilder;

abstract class AbstractModel
{

    /**
     * App trait.
     */
    use \Neoflow\Framework\AppTrait;

    /**
     * @var string
     */
    public static $tableName;

    /**
     * @var string
     */
    public static $primaryKey;

    /**
     * @var array
     */
    public static $properties;

    /**
     * @var array
     */
    protected $data = array();

    /**
     * @var array
     */
    protected $modifiedProperties = array();

    /**
     * @var bool
     */
    protected $isReadOnly = false;

    /**
     * @var bool
     */
    protected $isModified = false;

    /**
     * @var ORM
     */
    protected $orm;

    /**
     * Constructor.
     *
     * @param array $data
     * @param bool  $isReadOnly
     */
    public function __construct(array $data = array(), $isReadOnly = false)
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value, true);
        }

        $this->isReadOnly = $isReadOnly;

        $this->modifiedProperties = array();
        $this->isModified = false;

        $this->orm = new ORM();
    }

    /**
     * Get table name of model entity.
     *
     * @return string
     */
    protected function getTableName()
    {
        $modelClassName = get_class($this);

        return $modelClassName::$tableName;
    }

    /**
     * Get primary key of model entity.
     *
     * @return string
     */
    protected function getPrimaryKey()
    {
        $modelClassName = get_class($this);

        return $modelClassName::$primaryKey;
    }

    /**
     * Get properties of model entity
     *
     * @return string
     */
    protected function getProperties()
    {
        $modelClassName = get_class($this);

        return $modelClassName::$properties;
    }

    /**
     * Get data of model entity as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Return id of model entity.
     *
     * @return mixed
     */
    public function id()
    {
        $primaryKey = $this->getPrimaryKey();
        return $this->{$primaryKey};
    }

    /**
     * Check wether the model entity is read-only.
     *
     * @return bool
     */
    public function isReadOnly()
    {
        return $this->isReadOnly;
    }

    /**
     * Check wether model entity has changed.
     *
     * @return bool
     */
    public function isModified()
    {
        return $this->isModified;
    }

    /**
     * Set model entity read-only.
     */
    public function setReadOnly()
    {
        $this->isReadOnly = true;
    }

    /**
     * Set model entity value.
     *
     * @param string $key
     * @param mixed  $value
     * @param bool $silent
     *
     * @return Model
     *
     * @throws DomainException
     */
    public function set($key, $value = null, $silent = false)
    {
        if ($this->isReadOnly()) {
            throw new DomainException('Model entity is read only and cannot set value');
        }

        if (in_array($key, $this->getProperties())) {
            $this->data[$key] = $value;

            if (!$silent) {
                $this->modifiedProperties[] = $key;
                $this->isModified = true;
            }
        } else {
            $this->$key = $value;
        }

        return $this;
    }

    /**
     * Check wether model entity property exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function exists($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Get model entity value.
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if ($this->exists($key)) {
            return $this->data[$key];
        }

        return $default;
    }

    /**
     * Getter
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * Setter
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * Save model entity.
     *
     * @return int|bool
     */
    public function save()
    {
        if ($this->id()) {
            return $this->update();
        }

        $queryBuilder = new QueryBuilder();

        $id = $queryBuilder
            ->insertInto($this->getTableName())
            ->values($this->getModifiedData())
            ->execute();

        if ($id) {
            $primaryKey = $this->getPrimaryKey();
            $this->set($primaryKey, $id);
        }

        return $id;
    }

    /**
     * Get modified data.
     *
     * @return array
     */
    public function getModifiedData()
    {
        return array_intersect_key($this->data, array_flip($this->modifiedProperties));
    }

    /**
     * Update model entity.
     *
     * @return int|bool
     */
    public function update()
    {
        $queryBuilder = new QueryBuilder();

        return $queryBuilder
                ->update($this->getTableName())
                ->setPrimaryKey($this->getPrimaryKey())
                ->set($this->getModifiedData())
                ->execute($this->id());
    }

    /**
     * Delete model entity.
     *
     * @return bool
     */
    public function delete()
    {
        $queryBuilder = new QueryBuilder();

        return $queryBuilder
                ->deleteFrom($this->getTableName())
                ->setPrimaryKey($this->getPrimaryKey())
                ->execute($this->id());
    }

    /**
     * Manage one-to-one and one-to-many relations where the foreign key
     * is on the base model entity.
     *
     * @param string $associatedModelClassName
     * @param string $foreignKeyName
     *
     * @return ORM
     */
    protected function belongsTo($associatedModelClassName, $foreignKeyName)
    {
        return $this->getOrm($associatedModelClassName)
                ->belongsTo($this, $associatedModelClassName, $foreignKeyName);
    }

    /**
     * Manage one-to-one relation where the foreign key
     * is on the associated model entity.
     *
     * @param string $associatedModelClassName
     * @param string $foreignKeyName
     *
     * @return ORM
     */
    protected function hasOne($associatedModelClassName, $foreignKeyName)
    {
        return $this->getOrm($associatedModelClassName)
                ->hasOne($this, $associatedModelClassName, $foreignKeyName);
    }

    /**
     * Manage one-to-many relations where the foreign key
     * is on the associated model entity.
     *
     * @param string $associatedModelClassName
     * @param string $foreignKeyName
     *
     * @return ORM
     */
    protected function hasMany($associatedModelClassName, $foreignKeyName)
    {
        return $this->getOrm($associatedModelClassName)
                ->hasMany($this, $associatedModelClassName, $foreignKeyName);
    }

    /**
     * Manage many-to-many relations trought join model.
     *
     * @param string $associatedModelClassName
     * @param string $joinModelClassName
     * @param string $foreignKeyToBaseModel
     * @param string $foreignKeyToAssociatedModel
     *
     * @return ORM
     */
    protected function hasManyThrough($associatedModelClassName, $joinModelClassName, $foreignKeyToBaseModel, $foreignKeyToAssociatedModel)
    {
        return $this->getOrm($associatedModelClassName)
                ->hasManyThrough($this, $associatedModelClassName, $joinModelClassName, $foreignKeyToBaseModel, $foreignKeyToAssociatedModel);
    }

    /**
     * Add additional property to model entity
     *
     * @param string $key
     * @return self
     */
    public function addProperty($key)
    {
        $modelClassName = get_class($this);

        $modelClassName::$properties[] = $key;

        return $this;
    }

    /**
     * Get ORM for model of current mapper.
     *
     * @param string $modelClassName
     *
     * @return ORM
     */
    protected function getOrm($modelClassName = '')
    {
        if (!$modelClassName) {
            $modelClassName = get_class($this);
        }

        return $this->orm->forModel($modelClassName);
    }
}
