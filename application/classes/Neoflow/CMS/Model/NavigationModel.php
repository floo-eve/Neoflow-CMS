<?php

namespace Neoflow\CMS\Model;

use \Neoflow\Framework\ORM\AbstractEntityModel;
use \Neoflow\Framework\ORM\EntityRepository;

class NavigationModel extends AbstractEntityModel
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
     * @return EntityRepository
     */
    public function navitems()
    {
        return $this->hasMany('\\Neoflow\\CMS\\Model\\NavitemModel', 'navigation_id');
    }
}
