<?php
namespace Neoflow\CMS\Support\Module;

use Neoflow\Framework\Common\Container;

abstract class AbstractManager implements ManagerInterface {

    /**
     * @var Container
     */
    protected $config;

    /**
     * Constructor. 
     * 
     * @param string $configFile
     * @throws Exception
     */
    public function __construct($configFile) {
        if (is_file($configFile)) {
            $config = parse_ini_file($configFile);
            $this->config = new Container($config);
        } else {
            throw new Exception('Module config file not found: ' . $configFile);
        }
    }

}
