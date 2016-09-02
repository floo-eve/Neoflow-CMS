<?php

namespace Neoflow\CMS\Controller;

use Neoflow\CMS\Mapper\PageMapper;
use Neoflow\CMS\Views\FrontendView;
use Neoflow\Framework\Core\AbstractController;
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


        $pageMapper = new PageMapper();

        $pageOrm = $pageMapper->getOrm();


        if (isset($args['slug'])) {

            $slugParts = array_values(array_filter(explode('/', $args['slug'])));

            foreach ($slugParts as $slugPart) {

                if ($page) {

                    $page->setReadonly();

                    $parentPages[] = $page;

                    $pageOrm = $page->childPages();
                }

                $page = $pageOrm->where('slug', '=', $slugPart)->fetch();
            }
        } else {
            $page = $pageOrm->fetch();

            $page->setReadonly();
        }

        if (!$page) {
            return $this->notFoundAction($args);
        }

        $this->app()
            ->set('page', $page)
            ->set('parentPages', $parentPages);

        $this->view = $page->renderToView($this->view);

        return $this->render('index');
    }

    /**
     * Not found action.
     *
     * @return Response
     */
    public function notFoundAction($args)
    {
        return $this->render('error/notFound')
                ->setStatusCode(404);
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
