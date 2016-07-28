<?php

namespace Neoflow\Module\HelloWorld\Model;

use \Neoflow\Framework\Core\AbstractModel;

class MessageModel extends AbstractModel
{

    /**
     * @var string
     */
    public static $tableName = 'mod_hello_world_messages';

    /**
     * @var string
     */
    public static $primaryKey = 'message_id';

    /**
     * @var array
     */
    public static $properties = ['message_id', 'message'];

}
