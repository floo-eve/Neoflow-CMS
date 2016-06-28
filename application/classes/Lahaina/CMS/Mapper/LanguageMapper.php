<?php

namespace Neoflow\CMS\Mapper;

use \Neoflow\CMS\Model\LanguageModel;
use \Neoflow\Framework\Core\AbstractMapper;

class LanguageMapper extends AbstractMapper
{

    /**
     * @var string
     */
    public static $modelClassName = '\\Neoflow\\CMS\\Model\\LanguageModel';

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
