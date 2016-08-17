<?php

namespace Neoflow\CMS\Core;

use Neoflow\CMS\Support\Alert\DangerAlert;
use Neoflow\CMS\Support\Alert\InfoAlert;
use Neoflow\CMS\Support\Alert\SuccessAlert;
use Neoflow\CMS\Support\Alert\WarningAlert;
use Neoflow\Framework\Core\AbstractController;

abstract class AbstractController extends AbstractController
{

    /**
     * Create danger alert and set as session flash
     * @param string $message
     * @param array $values
     *
     * @return self
     */
    protected function setDangerAlert($message, array $values = array())
    {
        $this->setFlash('alert', new DangerAlert($message, $values));
        return $this;
    }

    /**
     * Create info alert and set as session flash
     * @param string $message
     * @param array $values
     *
     * @return self
     */
    protected function setInfoAlert($message, array $values = array())
    {
        $this->setFlash('alert', new InfoAlert($message, $values));
        return $this;
    }

    /**
     * Create success alert and set as session flash
     * @param string $message
     * @param array $values
     *
     * @return self
     */
    protected function setSuccessAlert($message, array $values = array())
    {
        $this->setFlash('alert', new SuccessAlert($message, $values));
        return $this;
    }

    /**
     * Create success alert and set as session flash
     * @param string $message
     * @param array $values
     *
     * @return self
     */
    protected function setWarningAlert($message, array $values = array())
    {
        $this->setFlash('alert', new WarningAlert($message, $values));
        return $this;
    }
}
