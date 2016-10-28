<?php

namespace Neoflow\Module\HelloWorld2\Model;

use Neoflow\Framework\ORM\AbstractEntityModel;

class MessageModel extends AbstractEntityModel
{

    /**
     * @var string
     */
    public static $tableName = 'hello_world_messages';

    /**
     * @var string
     */
    public static $primaryKey = 'message_id';

    /**
     * @var array
     */
    public static $properties = ['message_id', 'message', 'section_id'];

}
