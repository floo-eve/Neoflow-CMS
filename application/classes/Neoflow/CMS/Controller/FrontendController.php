<?php

namespace Neoflow\CMS\Controller;

use InvalidArgumentException;
use Neoflow\CMS\Core\AbstractController;
use Neoflow\CMS\Model\PageModel;
use Neoflow\CMS\Views\FrontendView;
use Neoflow\Framework\HTTP\Responsing\Response;

class FrontendController extends AbstractController
{

    /**
     * Index action.
     *
     * @param array $args
     */
    public function indexAction($args)
    {
        $page = false;
        $parentPages = array();
        if (isset($args['slug']) && count($args['slug']) > 0) {

            $slugParts = array_values(array_filter(explode('/', $args['slug'])));

            foreach ($slugParts as $slugPart) {
                if ($page) {
                    $page->setReadonly();
                    $parentPages[] = $page;
                    $page = $page
                        ->childPages()
                        ->where('language_id', '=', $this->view->getActiveLanguage()->id())
                        ->where('slug', '=', $slugPart)
                        ->fetch();
                } else {
                    $page = PageModel::repo()
                        ->where('language_id', '=', $this->view->getActiveLanguage()->id())
                        ->where('slug', '=', $slugPart)
                        ->fetch();
                }
                if (!$page) {
                    break;
                }
            }
        } else {
            $page = PageModel::repo()
                ->where('language_id', '=', $this->view->getActiveLanguage()->id())
                ->fetch();
        }

        if (!$page) {
            return $this->notFoundAction($args);
        }

        return $this
                ->renderPage($page)
                ->render('index');
    }

    protected function renderPage($page)
    {
        $this->view->set('page_title', $page->title);

        $sections = $page->sections()
            ->orderByAsc('position')
            ->fetchAll();

        foreach ($sections as $section) {
            $module = $section->module()->fetch();
            if ($module) {
                // Execute frontend controller of module
                $view = new FrontendView();
                $view->set('section_id', $section->id());
                $this->router()->routeByKey($module->frontend_route, array('section_id' => $section->id()), $view);
                $this->view->addContentToBlock($section->block, $view->getBlock(0));
            } else {
                throw new InvalidArgumentException('Cannot find module with ID: ' . $this->module_id);
            }
        }

        return $this;
    }

    /**
     * Not found action.
     *
     * @return Response
     */
    public function notFoundAction($args)
    {
        return $this->render('error/notFound')->setStatusCode(404);
    }

    /**
     * Error action.
     *
     * @return Response
     */
    public function errorAction($args)
    {
        $message = '';
        $exception = false;

        if (isset($args[0])) {
            if (is_a($args[0], '\\Exception') || is_a($args[0], '\\Error')) {
                $message = $args[0]->getMessage();
                $exception = $args[0];
            } elseif (is_string($args[0])) {
                $message = $args[0];
            }
        }

        return $this->render('error/internalServerError', array(
                'code' => 500,
                'title' => 'Internal server error',
                'message' => $message,
                'exception' => $exception
            ))->setStatusCode(500);
    }

    /**
     * Initialize view
     */
    protected function initView()
    {
        $this->view = new FrontendView();
    }
}
