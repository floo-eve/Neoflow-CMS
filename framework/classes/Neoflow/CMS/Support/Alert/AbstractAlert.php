<?php

namespace Neoflow\CMS\Support\Alert;

abstract class AbstractAlert
{

    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $messages = array();

    /**
     * Constructor.
     *
     * @param string|array $messages
     */
    public function __construct($messages)
    {
        if (is_string($messages)) {
            $this->addMessage($messages);
        } else {
            $this->setMessages($messages);
        }
    }

    /**
     * Set messages.
     *
     * @param array $messages
     */
    public function setMessages(array $messages)
    {
        $this->messages = $messages;
    }

    /**
     * Add message.
     *
     * @param string $message
     */
    public function addMessage($message)
    {
        $this->messages[] = $message;
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
     * Get messages.
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
