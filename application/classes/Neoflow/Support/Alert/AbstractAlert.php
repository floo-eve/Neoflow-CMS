<?php

namespace Neoflow\Support\Alert;

abstract class AbstractAlert
{

    /**
     * App trait
     */
    use \Neoflow\Framework\AppTrait;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $message = array();

    /**
     * Constructor.
     *
     * @param string $message
     * @param array  $values
     */
    public function __construct($message, array $values = array())
    {
        $translator = $this->app()->get('translator');

        if (is_array($message)) {
            array_map(function ($messageItem) use ($translator, $values) {
                $this->message[] = $translator->translate($messageItem, $values, '');
            }, $message);
        } else {
            $this->message[] = $translator->translate($message, $values, '');
        }
    }

    /**
     * Get alert type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get alert message.
     *
     * @return array
     */
    public function getMessage()
    {
        return $this->message;
    }
}
