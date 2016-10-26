<?php

namespace Neoflow\Module\HelloWorld;

use Neoflow\CMS\Support\Module\AbstractManager;
use Neoflow\Module\HelloWorld\Model\MessageModel;

class Manager extends AbstractManager
{

    public function add($section)
    {
        return (bool) MessageModel::create(array(
                'section_id' => $section->id(),
                'message' => 'Hello World'
            ))->save();
    }

    public function remove($section)
    {
        return (bool) MessageModel::deleteAllByColumn('section_id', $section->id());
    }

    public function install()
    {
        return true;
    }

    public function uninstall()
    {
        return true;
    }

    public function update()
    {
        return true;
    }
}
