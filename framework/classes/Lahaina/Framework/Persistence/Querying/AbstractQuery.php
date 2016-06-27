<?php

namespace Lahaina\Framework\Persistence\Querying;

use \DateTime;
use \Lahaina\Framework\Persistence\Caching\AbstractCache;
use \Lahaina\Framework\Persistence\Database;
use \PDO;
use \PDOStatement;
use \ReflectionFunction;

abstract class AbstractQuery
{

    /**
     * Load app
     */
    use \Lahaina\Framework\AppTrait;

    /**
     * @var bool
     */
    protected $asObject = false;

    /**
     * @var array
     */
    protected $statements = array();

    /**
     * @var array
     */
    protected $parameters = array();

    /**
     * @var array
     */
    protected $clauses = array();

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * Constructor.
     *
     * @param string $table
     */
    public function __construct($table)
    {
        foreach ($this->clauses as $clause => $seperator) {
            $this->statements[$clause] = array();
            $this->parameters[$clause] = array();
        }
    }

    /**
     * Set primary key column
     *
     * @param string $primaryKey
     *
     * @return AbstractQuery
     */
    public function setPrimaryKey($primaryKey)
    {
        $this->primaryKey = $primaryKey;
        return $this;
    }

    /**
     * Add statement.
     *
     * @param string $clause
     * @param string $statement
     * @param array  $parameters
     *
     * @return AbstractQuery
     */
    protected function addStatement($clause, $statement, array $parameters = array())
    {
        $this->statements[$clause][] = $statement;
        $this->parameters[$clause][] = $parameters;
        return $this;
    }

    /**
     * Execute query.
     *
     * @return PDOStatement
     */
    public function execute()
    {
        $query = $this->buildQuery();
        $parameters = $this->buildParameters();

        $statement = $this->getDatabase()->prepare($query);

        if ($this->asObject) {
            if (class_exists($this->asObject)) {
                $statement->setFetchMode(PDO::FETCH_CLASS, $this->asObject);
            } else {
                $statement->setFetchMode(PDO::FETCH_OBJ);
            }
        } elseif ($this->getDatabase()->getAttribute(PDO::ATTR_DEFAULT_FETCH_MODE) == PDO::FETCH_BOTH) {
            $statement->setFetchMode(PDO::FETCH_ASSOC);
        }

        $this->logQueryData($query, $parameters, $statement->rowCount());
        $statement->execute($parameters);
        $this->getLogger()->info('Query executed');
//        $this->logQueryData($query, $parameters, $statement->rowCount());

        return $statement;
    }

    /**
     * Log query data
     * @param string $query
     * @param array $parameters
     * @param int $affectedRows
     */
    protected function logQueryData($query, array $parameters = array(), $affectedRows = 0)
    {
        $this->getLogger()->debug('      String: ' . $query);

        $parameters = array_map(array($this, 'quote'), $parameters);

        if (count($parameters) > 0) {
            $this->getLogger()->debug('      Params: ' . implode(', ', array_map(function ($value, $key) {
                        return (is_string($key) ? $key : '?') . ' => ' . $value;
                    }, $parameters, array_keys($parameters))));
        }
        $this->getLogger()->debug('      Result: ' . $affectedRows . ' row(s) affected');
    }

    /**
     * Get parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->buildParameters();
    }

    /**
     * Get query string.
     *
     * @return string
     */
    public function getQuery()
    {
        $query = $this->buildQuery();

        return $query;
    }

    /**
     * Get formatted and readable query string.
     *
     * @return string
     */
    public function getFormatedQuery()
    {
        $query = $this->getQuery();

        return $this->formatQuery($query);
    }

    /**
     * Format to readable query.
     *
     * @param string $query
     *
     * @return string
     */
    protected function formatQuery($query)
    {
        // Add line break
        $query = preg_replace('/WHERE|FROM|GROUP BY|HAVING|ORDER BY|LIMIT|OFFSET|UNION|ON DUPLICATE KEY UPDATE|VALUES/', "\n$0", $query);

        // Add line break and spaces
        $query = preg_replace('/INNER|LEFT|RIGHT|CASE|WHEN|END|ELSE|AND/', "\n    $0", $query);

        // remove trailing spaces
        $query = preg_replace("/\s+\n/", "\n", $query);

        return $query;
    }

    /**
     * Build query with statements.
     *
     * @return string
     */
    protected function buildQuery()
    {
        $query = '';
        foreach ($this->clauses as $clause => $separator) {
            $clauseStatements = $this->statements[$clause];
            if (count($clauseStatements) > 0) {
                if (is_callable($separator)) {
                    $reflection = new ReflectionFunction($separator);
                    $args = array($clauseStatements);
                    $query .= $reflection->invokeArgs($args);
                } else {
                    $query .= ' ' . $clause . ' ' . implode($separator, $clauseStatements);
                }
            }
        }

        return trim($query);
    }

    /**
     * Build parameters.
     *
     * @return array
     */
    protected function buildParameters()
    {
        $parameters = array();
        foreach ($this->parameters as $parameter) {
            if (is_array($parameter)) {
                foreach ($parameter as $value) {
                    if (is_array($value)) {
                        $parameters = array_merge($parameters, $value);
                    } else {
                        $parameters[] = $value;
                    }
                }
            } else {
                $parameters[] = $parameter;
            }
        }

        return array_map(function($parameter) {

            // Fix boolean behavior when using PDO and prepared statements
            // @link https://evertpot.com/mysql-bool-behavior-and-php/
            if (is_bool($parameter)) {
                $parameter = (int) $parameter;
            }
            return $parameter;
        }, $parameters);
    }

    /**
     * Quote a value for use in a query.
     *
     * @param mixed $value
     *
     * @return string
     */
    protected function quote($value)
    {
        if (!isset($value)) {
            return 'NULL';
        }
        if (is_array($value)) { // (a, b) IN ((1, 2), (3, 4))
            return '(' . implode(', ', array_map(array($this, 'quote'), $value)) . ')';
        }
        if ($value instanceof DateTime) {
            return $value->format('Y-m-d H:i:s'); //! may be driver specific
        }
        if (is_float($value)) {
            return sprintf('%F', $value); // otherwise depends on setlocale()
        }
        if ($value === false) {
            return '0';
        }
        if (is_int($value)) {
            return (string) $value;
        }

        return $this->getDatabase()->quote($value);
    }

    /**
     * Get cache
     *
     * @return AbstractCache
     */
    protected function getCache()
    {
        return $this->app()->get('cache');
    }

    /**
     * Get database
     *
     * @return Database
     */
    protected function getDatabase()
    {
        return $this->app()->get('database');
    }
}
