<?php

namespace Neoflow\Framework\Persistence\Querying;

use Neoflow\Framework\Common\Collection;
use PDOStatement;

/**
 * @method SelectQuery where(string $condition, string $operator, mixed $parameter) Add WHERE condition
 * @method SelectQuery whereRaw(string $condition, array $parameters) Add raw WHERE condition
 * @method SelectQuery leftJoin(string $table, array $condition) Add LEFT JOIN statement
 * @method SelectQuery innerJoin(string $table, array $condition) Add INNER JOIN statement
 */
class SelectQuery extends AbstractQuery
{

    /**
     * Use WHERE statements.
     */
    use \Neoflow\Framework\Persistence\Querying\WhereTrait;

/**
     * Use JOIN statements.
     */
    use \Neoflow\Framework\Persistence\Querying\JoinTrait;

    /**
     * @var array
     */
    protected $clauses = array(
        'SELECT' => ', ',
        'FROM' => false,
        'LEFT JOIN' => false,
        'INNER JOIN' => false,
        'WHERE' => ' AND ',
        'GROUP BY' => ', ',
        'HAVING' => ' AND ',
        'ORDER BY' => ', ',
        'LIMIT' => false,
        'OFFSET' => false,
    );

    /**
     * @var bool
     */
    protected $caching = false;

    /**
     * @var string
     */
    protected $cacheKey;

    /**
     * Constructor.
     *
     * @param string $table
     */
    public function __construct($table)
    {
        parent::__construct($table);

        $this->addStatement('FROM', $table);

        $this->caching = (bool) $this->config()->get('queryBuilder')->get('caching');
    }

    /**
     * Fetch single column.
     *
     * @param int|string $column
     *
     * @return mixed
     */
    public function fetchColumn($column = 0)
    {
        // Generate cache key
        $cacheKey = $this->generateCacheKey();

        // Fetch from cache
        $result = $this->fetchFromCache($cacheKey);
        if ($result === false) {

            // Fetch from database
            $statement = $this->asObject(false)->execute();
            if ($statement) {
                if (is_integer($column)) {
                    $result = $statement->fetchColumn($column);
                } elseif (is_string($column)) {
                    $result = $statement->fetch();
                    if ($result) {
                        $result = $result[$column];
                    }
                }
            }

            // Store to cache
            $this->storeToCache($cacheKey, $result);
        }

        return $result;
    }

    /**
     * Execute query.
     *
     * @return PDOStatement
     */
    public function execute()
    {
        if (count($this->statements['SELECT']) === 0) {
            $this->statements['SELECT'][] = '*';
        }

        return parent::execute();
    }

    /**
     * Add SELECT for column(s).
     *
     * @param string $column
     *
     * @return self
     */
    public function select($column)
    {
        return $this->addStatement('SELECT', $column);
    }

    /**
     * Add GROUP BY for column.
     *
     * @param string $column
     *
     * @return self
     */
    public function groupBy($column)
    {
        $this->addStatement('GROUP BY', $column);
    }

    /**
     * Add HAVING for column.
     *
     * @param string $column
     *
     * @return self
     */
    public function having($column)
    {
        $this->addStatement('HAVING', $column);
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
        $this->orderByRaw($column . ' ASC');
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
        $this->orderByRaw($column . ' DESC');
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
        $this->addStatement('ORDER BY', $statement);
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
        $this->addStatement('LIMIT', $limit);
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
        $this->addStatement('OFFSET', $offset);
    }

    /**
     * Fetch first row.
     *
     * @param int|string $id
     *
     * @return mixed
     */
    public function fetch($id = false)
    {
        if ($id) {
            $this->where($this->primaryKey, '=', $id);
        }

        // Generate cache key
        $cacheKey = $this->generateCacheKey();

        // Fetch from cache
        $result = $this->fetchFromCache($cacheKey);
        if ($result === false) {

            // Fetch from database
            $statement = $this->execute();
            if ($statement) {
                $result = $statement->fetch();
            }

            // Store to cache
            $this->storeToCache($cacheKey, $result);
        }

        return $result;
    }

    /**
     * Fetch all rows.
     *
     * @return Collection|bool
     */
    public function fetchAll()
    {
        // Generate cache key
        $cacheKey = $this->generateCacheKey();

        // Fetch from cache
        $result = $this->fetchFromCache($cacheKey);
        if ($result === false) {

            // Fetch from database
            $statement = $this->execute();
            if ($statement) {
                $result = $statement->fetchAll();

                if (is_array($result)) {
                    $result = new Collection($result);
                }
            }

            // Store to cache
            $this->storeToCache($cacheKey, $result);
        }

        return $result;
    }

    /**
     * Count rows.
     *
     * @return int
     */
    public function count()
    {
        return $this->select('COUNT(*)')->fetchColumn();
    }

    /**
     * Set fetch mode as object.
     *
     * @param bool|object $asObject
     *
     * @return self
     */
    public function asObject($asObject = true)
    {
        $this->asObject = $asObject;

        return $this;
    }

    /**
     * Enable/disable caching.
     *
     * @param bool $caching
     *
     * @return self
     */
    public function caching($caching = true)
    {
        $this->caching = $caching;

        return $this;
    }

    /**
     * Store result to cahce.
     *
     * @param string $cacheKey
     * @param mixed  $result
     *
     * @return bool
     */
    protected function storeToCache($cacheKey, $result)
    {
        if ($this->caching) {
            return $this->getCache()->store($cacheKey, $result, 0, array('_query'));
        }

        return false;
    }

    /**
     * Fetch result from cahce.
     *
     * @param string $cacheKey
     *
     * @return mixed
     */
    protected function fetchFromCache($cacheKey)
    {
        if ($this->caching) {
            if ($this->getCache()->exists($cacheKey)) {
                $result = $this->getCache()->fetch($cacheKey);
                $this->logQueryData('Cached query fetched', $this->getQuery(), $this->getParameters(), count($result));

                return $result;
            }
        }

        return false;
    }

    /**
     * Generate cache key.
     *
     * @param string $prefix
     *
     * @return string
     */
    protected function generateCacheKey($prefix = '')
    {
        if (!$this->cacheKey) {
            return $prefix . sha1($this->getQuery() . ':' . implode('|', array_map(function ($parameter) {
                            if (is_array($parameter)) {
                                return implode('|', $parameter);
                            }

                            return $parameter;
                        }, $this->getParameters())));
        }

        return $this->cacheKey;
    }
}
