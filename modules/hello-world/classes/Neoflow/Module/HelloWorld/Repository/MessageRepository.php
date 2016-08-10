<?php

namespace Neoflow\Module\HelloWorld\Repository;

use Neoflow\Framework\Core\AbstractRepository;

class MessageRepository extends AbstractRepository
{
    /**
     * @var string
     */
    public $modelClassName = '\\Neoflow\\Module\\HelloWorld\\Model\\MessageModel';
}
