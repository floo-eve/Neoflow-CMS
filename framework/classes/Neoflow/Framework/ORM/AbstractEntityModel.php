<?php

namespace Neoflow\Framework\ORM;

use DomainException;
use Neoflow\Framework\ORM\EntityCollection;
use Neoflow\Framework\ORM\EntityRepository;
use Neoflow\Framework\Persistence\QueryBuilder;
use Neoflow\Framework\Persistence\Querying\SelectQuery;

abstract class AbstractEntityModel {

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
    public static $properties = array();

    /**
     * @var array
     */
    public static $hiddenProperties = array();

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
     * @var bool
     */
    protected $isNew = true;

    /**
     * Constructor.
     *
     * @param array $data
     * @param bool  $isReadOnly
     */
    public function __construct(array $data = array(), $isReadOnly = false) {
        foreach ($data as $key => $value) {
            $this->set($key, $value, true);
        }

        $this->mapper = new EntityMapper();

        $this->isReadOnly = $isReadOnly;

        $this->modifiedProperties = array();
        $this->isModified = false;

        if ($this->id()) {
            $this->isNew = false;
        }
    }

    /**
     * Get table name of model entity.
     *
     * @return string
     */
    protected function getTableName() {
        $modelClassName = get_class($this);

        return $modelClassName::$tableName;
    }

    /**
     * Get primary key of model entity.
     *
     * @return string
     */
    protected function getPrimaryKey() {
        $modelClassName = get_class($this);

        return $modelClassName::$primaryKey;
    }

    /**
     * Get properties of model entity.
     *
     * @return string
     */
    protected function getProperties() {
        $modelClassName = get_class($this);

        return $modelClassName::$properties;
    }

    /**
     * Get hidden properties of model entity.
     *
     * @return string
     */
    protected function getHiddenProperties() {
        $modelClassName = get_class($this);

        return $modelClassName::$hiddenProperties;
    }

    /**
     * Get data of model entity as an array.
     *
     * @return array
     */
    public function toArray() {
        return $this->data;
    }

    /**
     * Return id of model entity.
     *
     * @return mixed
     */
    public function id() {
        $primaryKey = $this->getPrimaryKey();
        $id = $this->{$primaryKey};
        if ($id) {
            return (int) $id;
        }

        return false;
    }

    /**
     * Check wether the model entity is read-only.
     *
     * @return bool
     */
    public function isReadOnly() {
        return $this->isReadOnly;
    }

    /**
     * Check wether model entity has changed.
     *
     * @return bool
     */
    public function isModified() {
        return $this->isModified;
    }

    /**
     * Set model entity read-only.
     */
    public function setReadOnly() {
        $this->isReadOnly = true;
    }

    /**
     * Get get translated value
     *
     * @param string $key
     * @return string
     */
    public function translated($key) {
        return $this->translator()->translate($this->$key);
    }

    /**
     * Set model entity value.
     *
     * @param string $key
     * @param mixed  $value
     * @param bool   $silent
     *
     * @return self
     *
     * @throws DomainException
     */
    protected function set($key, $value = null, $silent = false) {
        if ($this->isReadOnly()) {
            throw new DomainException('Model entity is read only and cannot set value');
        }

        if (in_array($key, $this->getProperties())) {
            $this->data[$key] = $value;

            if (!$silent) {
                $this->modifiedProperties[] = $key;
                $this->isModified = true;
            }
        } elseif (!in_array($key, $this->getHiddenProperties())) {
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
    protected function exists($key) {
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
    protected function get($key, $default = null) {
        if ($this->exists($key)) {
            return $this->data[$key];
        }

        return $default;
    }

    /**
     * Remove model entity value.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return self
     */
    protected function remove($key) {
        if ($this->exists($key)) {
            unset($this->data[$key]);
        }

        if (($index = array_search($key, $this->modifiedProperties)) !== false) {
            unset($this->modifiedProperties[$index]);
        }

        return $this;
    }

    /**
     * Validate model entity.
     *
     * @return bool
     */
    public function validate() {
        return true;
    }

    /**
     * Getter.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name) {
        return $this->get($name);
    }

    /**
     * Setter.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value) {
        $this->set($name, $value);
    }

    /**
     * Create model entity.
     *
     * @param array $data
     *
     * @return self
     */
    public static function create($data) {
        return new static($data);
    }

    /**
     * Update model entity.
     *
     * @param array $data
     * @param int|string $id
     *
     * @return self
     *
     * @throws Exception
     */
    public static function update($data, $id) {
        $entity = static::findById($id);
        if ($entity) {
            foreach ($data as $key => $value) {
                $entity->set($key, $value);
            }
            return $entity;
        }
        throw new Exception('Model entity not found');
    }

    /**
     * Save model entity.
     *
     * @return bool
     */
    public function save() {
        if ($this->id()) {
            return static::repo()->update($this);
        }

        $id = static::repo()->persist($this);

        if ($id) {
            $primaryKey = $this->getPrimaryKey();
            $this->set($primaryKey, $id);
            return true;
        }
        return false;
    }

    /**
     * Get data of model entity.
     *
     * @return array
     */
    public function getData() {
        return $this->toArray();
    }

    /**
     * Get modified data of model entity.
     *
     * @return array
     */
    public function getModifiedData() {
        return array_intersect_key($this->data, array_flip($this->modifiedProperties));
    }

    /**
     * Delete model entity.
     *
     * @return bool
     */
    public function delete() {
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
    protected function belongsTo($associatedModelClassName, $foreignKeyName) {
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
    protected function hasOne($associatedModelClassName, $foreignKeyName) {
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
    protected function hasMany($associatedModelClassName, $foreignKeyName) {
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
    protected function hasManyThrough($associatedModelClassName, $joinModelClassName, $foreignKeyToBaseModel, $foreignKeyToAssociatedModel) {
        return $this->mapper->hasManyThrough($this, $associatedModelClassName, $joinModelClassName, $foreignKeyToBaseModel, $foreignKeyToAssociatedModel);
    }

    /**
     * Add additional property to model entity.
     *
     * @param string $key
     *
     * @return self
     */
    public function addProperty($key) {
        $modelClassName = get_class($this);

        $modelClassName::$properties[] = $key;

        return $this;
    }

    /**
     * Remove property from model entity.
     *
     * @param string $key
     *
     * @return self
     */
    public function removeProperty($key) {
        $modelClassName = get_class($this);

        if (($index = array_search($key, $modelClassName::$properties)) !== false) {
            unset($modelClassName::$properties[$index]);
        }

        return $this->remove($key);
    }

    /**
     * Create repository for entity model
     *
     * @return EntityRepository
     */
    public static function repo() {
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
    protected static function selectQuery(array $columns = array()) {
        return static::queryBuilder()->selectFrom(static::$tableName, $columns);
    }

    /**
     * Get query builder
     *
     * @return QueryBuilder
     */
    protected static function queryBuilder() {
        return new QueryBuilder();
    }

    /**
     * Delete model entity by id
     * @param int|string $id
     * @return boolean
     */
    public static function deleteById($id) {
        $entity = static::findById($id);
        if ($entity) {
            return $entity->delete();
        }
        return false;
    }

    /**
     * Delete all model entities by column.
     *
     * @param string $column
     * @param mixed $value
     * @return EntityCollection
     */
    public static function deleteAllByColumn($column, $value) {
        return static::findAllByColumn($column, $value)->delete();
    }

    /**
     * Find model entity by id.
     *
     * @param int|string $id
     *
     * @return self
     */
    public static function findById($id) {

        return static::repo()->fetch($id);
    }

    /**
     * Find model entity by column
     * @param string $column
     * @param mixed $value
     *
     * @return self
     */
    public static function findByColumn($column, $value) {
        return static::repo()
                        ->where($column, '=', $value)
                        ->fetch();
    }

    /**
     * Find all model entities.
     *
     * @return EntityCollection
     */
    public static function findAll() {
        return static::repo()->fetchAll();
    }

    /**
     * Find all model entities by column.
     *
     * @param string $column
     * @param mixed $value
     * @return EntityCollection
     */
    public static function findAllByColumn($column, $value) {
        return static::repo()
                        ->where($column, '=', $value)
                        ->fetchAll();
    }

}
