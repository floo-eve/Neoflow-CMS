<?php

namespace Neoflow\CMS\Support\Extension;

interface ManagerInterface
{

    public function add($section);

    public function remove($section);

    public function install();

    public function uninstall();

    public function update();
}
