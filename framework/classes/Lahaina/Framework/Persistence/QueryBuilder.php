<?php

namespace Lahaina\Framework\Persistence;

use Lahaina\Framework\Persistence\Querying\DeleteQuery;
use Lahaina\Framework\Persistence\Querying\InsertQuery;
use Lahaina\Framework\Persistence\Querying\SelectQuery;
use Lahaina\Framework\Persistence\Querying\UpdateQuery;

class QueryBuilder {

    /**
     * Load app
     */
    use \Lahaina\Framework\AppTrait;

    /**
     * Create SELECT FROM query
     *
     * @param string $table
     * @param array $columns
     * @return SelectQuery
     */
    public function selectFrom($table, array $columns = array()) {
        $query = new SelectQuery($table);
        foreach ($columns as $column) {
            $query->select($column);
        }
        return $query;
    }

    /**
     * Create DELETE FROM query
     *
     * @param string $table
     * @return DeleteQuery
     */
    public function deleteFrom($table) {
        return new DeleteQuery($table);
    }

    /**
     * Create INSERT INTO query
     *
     * @param string $table
     * @param array $values
     * @return InsertQuery
     */
    public function insertInto($table, $values = array()) {
        $query = new InsertQuery($table);
        return $query->values($values);
    }

    /**
     * Create UPDATE query
     *
     * @param string $table
     * @param array $set
     * @return UpdateQuery
     */
    public function update($table, array $set = array()) {
        $query = new UpdateQuery($table);
        return $query->set($set);
    }

}
