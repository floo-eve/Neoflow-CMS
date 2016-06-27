<?php

namespace Lahaina\Framework\Persistence\Querying;

/**
 * @method InsertQuery where(string $condition, array $parameters) Add WHERE condition
 */
class InsertQuery extends AbstractQuery
{

    /**
     * @var array
     */
    protected $clauses = array(
        'INSERT INTO' => false,
        'VALUES' => ', '
    );

    /**
     * @var bool
     */
    protected $ignore = false;

    /**
     * Constructor.
     *
     * @param string $table
     */
    public function __construct($table)
    {
        parent::__construct($table);

        $this->addStatement('INSERT INTO', $table);

        $this->clauses['VALUES'] = function($clauseStatement) {
            $query = ' (' . implode(', ', $clauseStatement) . ') VALUES (' . str_repeat('?, ', count($clauseStatement) - 1) . '?) ';
            return $query;
        };
    }

    /**
     * Execute query.
     * @param string $lastInsertColumn
     * @return mixed
     */
    public function execute($lastInsertColumn = '')
    {
        $result = parent::execute();
        if ($result) {
            $this->getCache()->deleteByTag('_query');
            return $this->getDatabase()->lastInsertId($lastInsertColumn);
        }

        return false;
    }

    /**
     * Add values to INSERT statement.
     *
     * @param array $values
     *
     * @return InsertQuery
     */
    public function values(array $values = array())
    {
        foreach ($values as $column => $value) {
            $this->statements['VALUES'][] = $column;
            $this->parameters['VALUES'][] = $value;
        }
        return $this;
    }

    /**
     * Query fails silently.
     *
     * @return InserQuery
     */
    public function ignore()
    {
        $this->ignore = true;

        return $this;
    }
}
