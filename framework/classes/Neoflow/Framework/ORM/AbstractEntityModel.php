<?php

namespace Neoflow\Framework\ORM;

use DomainException;
use Neoflow\Framework\ORM\EntityCollection;
use Neoflow\Framework\ORM\EntityRepository;
use Neoflow\Framework\Persistence\QueryBuilder;
use Neoflow\Framework\Persistence\Querying\SelectQuery;

abstract class AbstractEntityModel
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
     * @var EntityMapper
     */
    protected $mapper;

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

        $this->mapper = new EntityMapper();

        $this->isReadOnly = $isReadOnly;

        $this->modifiedProperties = array();
        $this->isModified = false;
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
     * Get properties of model entity.
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
        $id = $this->{$primaryKey};
        if ($id) {
            return $id;
        }

        return false;
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
     * @param bool   $silent
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
     * @param mixed  $default
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
     * Validate model entity.
     *
     * @return bool
     */
    public function validate()
    {
        return true;
    }

    /**
     * Getter.
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
     * Setter.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * Create and save model entity.
     *
     * @param array $data
     *
     * @return self|bool
     */
    public static function create($data)
    {
        $entity = new static($data);
        if ($entity->save()) {
            return $entity;
        }
        return false;
    }

    /**
     * Save model entity.
     *
     * @return int|bool
     *
     * @throws Exception
     */
    public function save($validate = true)
    {
        if ($this->id()) {
            return $this->update($validate);
        }

        if ($validate) {
            $this->validate();
        }

        $id = static::repo()->persist($this);

        if ($id) {
            $primaryKey = $this->getPrimaryKey();
            $this->set($primaryKey, $id);
            return true;
        }
        throw new Exception('Save model entity failed');
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
     *
     * @throws Exception
     */
    public function update($validate = true)
    {
        if ($validate) {
            $this->validate();
        }

        return static::repo()
                ->update($this);
    }

    /**
     * Delete model entity.
     *
     * @return bool
     */
    public function delete()
    {
        static::queryBuilder()
            ->deleteFrom($this->getTableName())
            ->setPrimaryKey($this->getPrimaryKey())
            ->execute($this->id());

        return true;
    }

    /**
     * Manage one-to-one and one-to-many relations where the foreign key
     * is on the base model entity.
     *
     * @param string $associatedModelClassName
     * @param string $foreignKeyName
     *
     * @return EntityRepository
     */
    protected function belongsTo($associatedModelClassName, $foreignKeyName)
    {
        return $this->mapper->belongsTo($this, $associatedModelClassName, $foreignKeyName);
    }

    /**
     * Manage one-to-one relation where the foreign key
     * is on the associated model entity.
     *
     * @param string $associatedModelClassName
     * @param string $foreignKeyName
     *
     * @return EntityRepository
     */
    protected function hasOne($associatedModelClassName, $foreignKeyName)
    {
        return $this->mapper->hasOne($this, $associatedModelClassName, $foreignKeyName);
    }

    /**
     * Manage one-to-many relations where the foreign key
     * is on the associated model entity.
     *
     * @param string $associatedModelClassName
     * @param string $foreignKeyName
     *
     * @return EntityRepository
     */
    protected function hasMany($associatedModelClassName, $foreignKeyName)
    {
        return $this->mapper->hasMany($this, $associatedModelClassName, $foreignKeyName);
    }

    /**
     * Manage many-to-many relations trought join model.
     *
     * @param string $associatedModelClassName
     * @param string $joinModelClassName
     * @param string $foreignKeyToBaseModel
     * @param string $foreignKeyToAssociatedModel
     *
     * @return EntityRepository
     */
    protected function hasManyThrough($associatedModelClassName, $joinModelClassName, $foreignKeyToBaseModel, $foreignKeyToAssociatedModel)
    {
        return $this->mapper->hasManyThrough($this, $associatedModelClassName, $joinModelClassName, $foreignKeyToBaseModel, $foreignKeyToAssociatedModel);
    }

    /**
     * Add additional property to model entity.
     *
     * @param string $key
     *
     * @return self
     */
    public function addProperty($key)
    {
        $modelClassName = get_class($this);

        $modelClassName::$properties[] = $key;

        return $this;
    }

    /**
     * Create repository for entity model
     *
     * @return EntityRepository
     */
    public static function repo()
    {
        $repo = new EntityRepository();
        return $repo->forModel(get_called_class());
    }

    /**
     * Create select query for entity model
     *
     * @param array $columns
     *
     * @return SelectQuery
     */
    protected static function selectQuery(array $columns = array())
    {
        return static::queryBuilder()->selectFrom(static::$tableName, $columns);
    }

    /**
     * Get query builder
     *
     * @return QueryBuilder
     */
    protected static function queryBuilder()
    {
        return new QueryBuilder();
    }

    /**
     * Find model entity by Id.
     *
     * @param mixed $id
     *
     * @return self
     */
    public static function findById($id)
    {
        return static::repo()->fetch($id);
    }

    /**
     * Find model entity by column
     * @param string $column
     * @param mixed $value
     *
     * @return self
     */
    public static function findByColumn($column, $value)
    {
        return static::repo()
                ->where($column, '=', $value)
                ->fetch();
    }

    /**
     * Find all model entities.
     *
     * @return EntityCollection
     */
    public static function findAll()
    {
        return static::repo()->fetchAll();
    }

    /**
     * Find all by where conditions
     *
     * @param string $column
     * @param mixed $value
     * @return EntityCollection
     */
    public static function findAllByColumn($column, $value)
    {
        return static::repo()
                ->where($column, '=', $value)
                ->fetchAll();
    }
}