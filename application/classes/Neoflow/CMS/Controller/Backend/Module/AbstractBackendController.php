<?php

namespace Neoflow\CMS\Controller\Backend\Module;

use Exception;
use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\Model\ModuleModel;
use Neoflow\CMS\Model\PageModel;
use Neoflow\CMS\Model\SectionModel;
use Neoflow\Framework\HTTP\Responsing\Response;

abstract class AbstractBackendController extends BackendController
{

    /**
     * @var SectionModel
     */
    protected $section;

    /**
     * @var PageModel
     */
    protected $page;

    /**
     * @var ModuleModel
     */
    protected $module;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->view
            ->setSubtitle('Content')
            ->setTitle('Pages');

        $section_id = $this->request()->getGet('section_id');
        if (!$section_id) {
            $section_id = $this->request()->getPost('section_id');
        }

        // Get section, module and page
        $this->section = SectionModel::findById($section_id);

        if ($this->section) {
            $this->module = $this->section->module()->fetch();
            $this->page = $this->section->page()->fetch();
        } else {
            throw new Exception('Section not found');
        }

        // Set back url
        $this->view->setBackRoute('section_index', array('id' => $this->page->id()));
    }

    /**
     * Render module view as block of the section view and content of response.

     *

     * @param string   $viewFile
     * @param array    $parameters
     * @param Response $response
     *
     * @return Response
     */
    protected function render($viewFile, array $parameters = array(), Response $response = null)
    {
        $this->view->startBlock('module');
        echo $this->view->renderView($viewFile, $parameters);
        $this->view->stopBlock();

        return parent::render('backend/section/edit', array(
                'section' => $this->section,
                'page' => $this->page,
                'module' => $this->module,
                ), $response);
    }
}
