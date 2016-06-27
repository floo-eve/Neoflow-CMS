<?php

namespace Lahaina\CMS\Mapper;

use \Lahaina\CMS\Model\LanguageModel;
use \Lahaina\Framework\Core\AbstractMapper;

class LanguageMapper extends AbstractMapper
{

    /**
     * @var string
     */
    public static $modelClassName = '\\Lahaina\\CMS\\Model\\LanguageModel';

    /**
     * Find language by code.
     *
     * @param string $code
     *
     * @return LanguageModel
     */
    public function findByCode($code)
    {
        return $this->getOrm()
                ->where('code', '=', $code)
                ->fetch();
    }
}
