<?php

namespace Lahaina\Framework\Persistence\Querying;

/**
 * @method UpdateQuery where(string $condition, string $operator, mixed $parameter) Add WHERE condition
 * @method UpdateQuery whereRaw(string $condition, array $parameters) Add raw WHERE condition
 */
class UpdateQuery extends AbstractQuery
{

    /**
     * Use WHERE statements
     */
    use \Lahaina\Framework\Persistence\Querying\WhereTrait;

    /**
     * @var array
     */
    protected $clauses = array(
        'UPDATE' => false,
        'SET' => ', ',
        'WHERE' => ' AND '
    );

    /**
     * Constructor.
     *
     * @param string $table
     */
    public function __construct($table)
    {
        parent::__construct($table);
        $this->addStatement('UPDATE', $table);
    }

    /**
     * Add values to UPDATE statement.
     *
     * @param array $set
     *
     * @return UpdateQuery
     */
    public function set(array $set = array())
    {
        foreach ($set as $column => $value) {
            $this->statements['SET'][] = $column . ' = ?';
            $this->parameters['SET'][] = $value;
        }

        return $this;
    }

    /**
     * Execute query.
     *
     * @param int|string $id
     *
     * @return int|bool
     */
    public function execute($id = false)
    {
        if ($id) {
            $this->where($this->primaryKey, '=', $id);
        }

        $result = parent::execute();
        if ($result) {
            $this->getCache()->deleteByTag('_query');
            return $result->rowCount();
        }

        return false;
    }
}
