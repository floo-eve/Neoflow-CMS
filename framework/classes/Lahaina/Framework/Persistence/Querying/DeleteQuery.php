<?php

namespace Lahaina\Framework\Persistence\Querying;

/**
 * @method DeleteQuery where(string $condition, string $operator, mixed $parameter) Add WHERE condition
 * @method DeleteQuery whereRaw(string $condition, array $parameters) Add raw WHERE condition
 */
class DeleteQuery extends AbstractQuery
{

    /**
     * Use WHERE statements
     */
    use WhereTrait;

    /**
     * @var array
     */
    protected $clauses = array(
        'DELETE FROM' => false,
        'FROM' => null,
        'WHERE' => ' AND ',
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
        $this->addStatement('DELETE FROM', $table);
    }

    /**
     * Query fails silently.
     *
     * @return DeleteQuery
     */
    public function ignore()
    {
        $this->ignore = true;

        return $this;
    }

    /**
     * Build query with statements.
     *
     * @return string
     */
    protected function buildQuery()
    {
        $query = parent::buildQuery();
        if ($this->ignore) {
            return str_replace('DELETE', 'DELETE IGNORE', $query);
        }
        return $query;
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
