<?php

namespace Neoflow\Framework\Persistence\Querying;

use InvalidArgumentException;

trait WhereTrait
{

    /**
     * Add raw WHERE condition.
     *
     * @param string $condition
     * @param array  $parameters
     *
     * @return AbstractQuery
     */
    public function whereRaw($condition, array $parameters = array())
    {
        return $this->addStatement('WHERE', $condition, $parameters);
    }

    /**
     * Add where condition.
     *
     * @param string $property
     * @param string $operator
     * @param mixed  $parameter
     *
     * @return AbstractQuery
     *
     * @throws InvalidArgumentException
     */
    public function where($property, $operator, $parameter)
    {
        if (in_array($operator, array('<', '>', '=', '!=', 'BETWEEN', 'LIKE', 'IS'))) {
            if (is_null($parameter)) {
                $operator = 'IS';
            }
            return $this->addStatement('WHERE', $property . ' ' . $operator . ' ?', array($parameter));
        }
        throw new InvalidArgumentException('WHERE condition operator is not valid: ' . $operator);
    }
}
