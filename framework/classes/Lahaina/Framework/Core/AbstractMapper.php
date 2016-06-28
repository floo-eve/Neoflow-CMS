<?php

namespace Neoflow\Framework\Core;

use Neoflow\Framework\Persistence\ORM;

abstract class AbstractMapper
{

    /**
     * @var string
     */
    public static $modelClassName;

    /**
     * App trait.
     */
    use \Neoflow\Framework\AppTrait;

    /**
     * @var ORM
     */
    protected $orm;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->orm = new ORM();
    }

    /**
     * Get model class name.
     *
     * @return string
     */
    protected function getModelClassName()
    {
        $mapperClassName = get_class($this);

        return $mapperClassName::$modelClassName;
    }

    /**
     * Get ORM for model of current mapper.
     *
     * @param string $modelClassName
     *
     * @return ORM
     */
    public function getOrm($modelClassName = '')
    {
        if (!$modelClassName) {
            $modelClassName = $this->getModelClassName();
        }

        return $this->orm->forModel($modelClassName);
    }

    /**
     * Find model entity by Id.
     *
     * @param mixed $id
     *
     * @return AbstractModel
     */
    public function findById($id)
    {
        return $this->getOrm()->fetch($id);
    }

    /**
     * Find all model entities.
     *
     * @param mixed $id
     *
     * @return AbstractModel
     */
    public function findAll()
    {
        return $this->getOrm()->fetchAll();
    }

    /**
     * Find all by where conditions
     *
     * @param array $whereConditions
     * @return array
     */
    public function findAllBy(array $whereConditions = array())
    {
        $orm = $this->getOrm();
        foreach ($whereConditions as $whereCondition) {
            $orm->where($whereCondition[0], $whereCondition[1], $whereCondition[2]);
        }
        return $orm->fetchAll();
    }
}
