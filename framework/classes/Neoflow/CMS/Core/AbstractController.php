<?php

namespace Neoflow\CMS\Core;

use Neoflow\CMS\Support\Alert\DangerAlert;
use Neoflow\CMS\Support\Alert\InfoAlert;
use Neoflow\CMS\Support\Alert\SuccessAlert;
use Neoflow\CMS\Support\Alert\WarningAlert;

abstract class AbstractController extends \Neoflow\Framework\Core\AbstractController
{

    /**
     * Create danger alert and set as session flash.
     *
     * @param string $message
     *
     * @return self
     */
    protected function setDangerAlert($message)
    {
        $this->setFlash('alert', new DangerAlert($message));

        return $this;
    }

    /**
     * Create info alert and set as session flash.
     *
     * @param string $message
     *
     * @return self
     */
    protected function setInfoAlert($message)
    {
        $this->setFlash('alert', new InfoAlert($message));

        return $this;
    }

    /**
     * Create success alert and set as session flash.
     *
     * @param string $message
     *
     * @return self
     */
    protected function setSuccessAlert($message)
    {
        $this->setFlash('alert', new SuccessAlert($message));

        return $this;
    }

    /**
     * Create warning alert and set as session flash.
     *
     * @param string $message
     *
     * @return self
     */
    protected function setWarningAlert($message)
    {
        $this->setFlash('alert', new WarningAlert($message));

        return $this;
    }
}
