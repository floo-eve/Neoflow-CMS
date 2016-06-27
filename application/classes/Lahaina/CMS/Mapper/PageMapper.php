<?php

namespace Lahaina\CMS\Mapper;

use \Lahaina\Framework\Core\AbstractMapper;

class PageMapper extends AbstractMapper
{

    /**
     * @var string
     */
    public static $modelClassName = '\\Lahaina\\CMS\\Model\\PageModel';

    /**
     * Find all pages by language id.
     *
     * @param int $language_id
     *
     * @return array
     */
    public function findAllByLanguageId($language_id)
    {
        return $this->getOrm()
                ->forModel(self::$modelClassName)
                ->where('language_id', '=', $language_id)
                ->fetchAll();
    }
}
