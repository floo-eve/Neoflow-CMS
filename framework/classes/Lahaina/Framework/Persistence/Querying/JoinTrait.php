<?php

namespace Lahaina\Framework\Persistence\Querying;

trait JoinTrait
{

    /**
     * Add LEFT JOIN statement.
     *
     * @param string $table
     * @param string  $condition
     *
     * @return AbstractQuery
     */
    public function leftJoin($table, $condition)
    {
        return $this->addStatement('LEFT JOIN', $table . ' ON ' . $condition);
    }

    /**
     * Add INNER JOIN statement.
     *
     * @param string $table
     * @param string  $condition
     *
     * @return AbstractQuery
     */
    public function innerJoin($table, $condition)
    {
        return $this->addStatement('INNER JOIN', $table . ' ON ' . $condition);
    }
}
