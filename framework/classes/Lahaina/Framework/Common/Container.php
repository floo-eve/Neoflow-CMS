<?php

namespace Lahaina\Framework\Common;

use ArrayIterator;
use Exception;

class Container implements \IteratorAggregate, \Countable, \ArrayAccess
{

    /**
     * @var array container data
     */
    protected $data = array();

    /**
     * @var bool wether the container is read-only
     */
    protected $isReadOnly = false;

    /**
     * @var bool wether the container data is modified
     */
    protected $isModified = false;

    /**
     * @var bool wether the container data is multi-dimensional
     */
    protected $isMultiDimensional = false;

    /**
     * Constructor.
     *
     * @param array $data
     * @param bool  $isReadOnly
     * @param bool $isMultiDimensional
     */
    public function __construct(array $data = array(), $isReadOnly = false, $isMultiDimensional = false)
    {
        $this->isMultiDimensional = $isMultiDimensional;

        if (is_array($data) && is_assoc($data)) {
            if ($this->isMultiDimensional) {
                foreach ($data as $key => $value) {
                    $this->set($key, $value);
                }
            } else {
                $this->data = $data;
            }
        }

        $this->isReadOnly = $isReadOnly;
    }

    /**
     * Get container data as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Check wether the data container is read-only.
     *
     * @return bool
     */
    public function isReadOnly()
    {
        return $this->isReadOnly;
    }

    /**
     * Check wether data has changed.
     *
     * @return bool
     */
    public function isModified()
    {
        return $this->isModified;
    }

    /**
     * Set data value.
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return Container
     *
     * @throws Exception
     */
    public function set($key, $value = null)
    {
        if ($this->isReadOnly()) {
            throw new Exception('Container is read only and cannot set data value');
        }
        if ($this->isMultiDimensional && is_array($value) && is_assoc($value)) {
            $this->data[$key] = new self($value, $this->isReadOnly(), true);
        } else {
            $this->data[$key] = $value;
        }
        $this->isModified = true;

        return $this;
    }

    /**
     * Check wether data value exists.
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function exists($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Delete data value.
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function delete($key)
    {
        if ($this->isReadOnly()) {
            throw new Exception('Container is read only and cannot remove data value');
        }

        if ($this->exists($key)) {
            unset($this->data[$key]);
            $this->data = array_values($this->data);
            $this->isModified = true;

            return true;
        }

        return false;
    }

    /**
     * Get data value.
     *
     * @param mixed $key
     *                   $param mixed $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if ($this->exists($key)) {
            return $this->data[$key];
        }

        return $default;
    }

    /**
     * Number of data values, implements Countable.
     *
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * Get an iterator, implements IteratorAggregate.
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->data);
    }

    /**
     * Get data value.
     *
     * @param mixed $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Set data value.
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return Container
     */
    public function __set($key, $value)
    {
        return $this->set($key, $value, false);
    }

    /**
     * Remove data value.
     *
     * @param mixed $key
     */
    public function __unset($key)
    {
        $this->delete($key);
    }

    /**
     * Check if named data value exists.
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return $this->exists($key);
    }

    /**
     * Call for named get and set methods.
     */
    public function __call($key, $arguments)
    {
        if (strpos($key, 'set') === 0) {
            if (!isset($arguments[0])) {
                $arguments[] = array();
            }
            $key = strtolower(str_replace(array('set', 'set_'), '', $key));

            return $this->set($key, $arguments[0]);
        } elseif (strpos($key, 'get') === 0) {
            $key = strtolower(str_replace(array('get', 'get_'), '', $key));

            return $this->get($key);
        }
        throw new Exception('Method not found');
    }

    /**
     * Check wether data value exists, implements ArrayAccess.
     *
     * @param int|string $offset
     *
     * @return mixed
     */
    public function offsetExists($offset)
    {
        return $this->exists($offset);
    }

    /**
     * Get data value, implements ArrayAccess.
     *
     * @param int|string $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Set data value, implements ArrayAccess.
     *
     * @param int|string $offset
     *
     * @return mixed
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * Delete data value, implements ArrayAccess.
     *
     * @param int|string $offset
     *
     * @return mixed
     */
    public function offsetUnset($offset)
    {
        $this->delete($offset);
    }
}
