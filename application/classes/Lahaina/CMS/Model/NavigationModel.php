<?php

namespace Lahaina\CMS\Model;

use \Lahaina\Framework\Core\AbstractModel;
use \Lahaina\Framework\Persistence\ORM;

class NavigationModel extends AbstractModel
{

    /**
     * @var string
     */
    public static $tableName = 'navigations';

    /**
     * @var string
     */
    public static $primaryKey = 'navigation_id';

    /**
     * @var array
     */
    public static $properties = ['navigation_id', 'title', 'description'];

    /**
     * Get navitems.
     *
     * @return ORM
     */
    public function navitems()
    {
        return $this->hasMany('\\Lahaina\\CMS\\Model\\NavitemModel', 'navigation_id');
    }
}
