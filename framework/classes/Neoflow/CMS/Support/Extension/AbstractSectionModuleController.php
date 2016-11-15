<?php

namespace Neoflow\CMS\Support\Extension;

use Neoflow\CMS\Controller\Backend\SectionController;
use Neoflow\CMS\Model\SectionModel;
use Neoflow\Framework\HTTP\Responsing\Response;
use RuntimeException;

abstract class AbstractSectionModuleController extends SectionController
{

    /**
     * @var SectionModel
     */
    protected $section;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        if (!$this->section) {
            throw new \Exception('No section ID found in request params (GET/POST)');
        }
    }

    /**
     * Render view as content of response.
     *
     * @param string   $viewFile
     * @param array    $parameters
     * @param Response $response
     *
     * @return Response
     *
     * @throws RuntimeException
     */
    protected function render($viewFile, array $parameters = array(), Response $response = null)
    {

        $module = $this->section->module()->fetch();
        $page = $this->section->page()->fetch();

        $parameters = array_merge(array(
            'section' => $this->section,
            'page' => $page,
            'module' => $module), $parameters);

        $this->view->startBlock('module');
        echo $this->view->renderView($viewFile, $parameters);
        $this->view->stopBlock();

        return parent::render('backend/section/edit', $parameters, $response);
    }
}
