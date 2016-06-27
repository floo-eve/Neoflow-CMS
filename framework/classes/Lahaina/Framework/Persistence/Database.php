<?php

namespace Lahaina\Framework\Persistence;

use PDOStatement;

class Database extends \PDO
{
    /**
     * Load app.
     */
    use \Lahaina\Framework\AppTrait;

    /**
     * @var int
     */
    protected $numberOfQueries = 0;

    /**
     * Constructor.
     * 
     * @param string $dsn
     * @param string $username
     * @param string $password
     * @param array  $options
     */
    public function __construct($dsn, $username = null, $password = null, array $options = null)
    {
        parent::__construct($dsn, $username, $password, $options);

        $this->getLogger()->info('Database connected');
    }

    /**
     * Prepares a statement for execution and returns a statement object.
     *
     * @param string $statement
     * @param array  $options
     *
     * @return PDOStatement
     */
    public function prepare($statement, $options = array())
    {
        $this->numberOfQueries += 1;

        return parent::prepare($statement, $options);
    }

    /**
     * Executes an SQL statement, returning a result set as a PDOStatement object.
     * 
     * @param string $query
     *
     * @return PDOStatement
     */
    public function query($query)
    {
        $this->numberOfQueries += 1;

        return parent::query($query);
    }

    /**
     * Execute an SQL statement and return the number of affected rows.
     * 
     * @param string $statement
     *
     * @return int
     */
    public function exec($statement)
    {
        $this->numberOfQueries += 1;

        return parent::exec($statement);
    }

    /**
     * Get number of executed queries.
     *
     * @return int
     */
    public function getNumberOfQueries()
    {
        return $this->numberOfQueries;
    }
}
