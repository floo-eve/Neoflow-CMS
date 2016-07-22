<?php

namespace Neoflow\CMS\Views\Backend;

use \Neoflow\CMS\Views\BackendView;
use \Neoflow\Framework\Common\Container;

class NavigationView extends BackendView
{

    /**
     * @var Container
     */
    protected $cookies;

    public function __construct()
    {
        $this->cookies = $this->app()->get('request')->getCookies();
        parent::__construct();
    }

    /**
     * Render navitems.
     *
     * @param array $navitems
     *
     * @return string
     */
    public function renderNavitemNestable(array $navitems)
    {
        $output = '';
        if ($navitems) {
            $output .= '<ol class="nestable-list list-group">';

            foreach ($navitems as $navitem) {
                $page = $navitem->page()->fetch();

                $output .= '<li class="nestable-item list-group-item ' . (!$page->is_active ? 'list-groupd-item-disabled' : '') . '" data-collapsed="' . $this->cookies->exists($navitem->id()) . '" data-id="' . $navitem->id() . '">';

                if (!$page->is_active) {
                    $output .= ' <i class="fa fa-fw fa-ban"></i>';
                } elseif ($page->visibility === 'restricted') {
                    $output .= ' <i class="fa fa-fw fa-eye-slash"></i>';
                } elseif ($page->visibility === 'hidden') {
                    $output .= ' <i class="fa fa-fw fa-eye-slash"></i>';
                } else {
                    $output .= ' <i class="fa fa-fw fa-eye"></i>';
                }

                $output .= '<span class="nestable-handle"><i class="fa fa-fw fa-arrows"></i></span>
                                <a href="' . $this->generateUrl('page_sections', array('id' => $navitem->page_id)) . '">' . $navitem->title . '</a>';

                $output .= '<span class="pull-right">
                                    <a href="' . $this->generateUrl('page_sections', array('id' => $navitem->page_id)) . '" class="btn btn-default btn-xs hidden-xs btn-icon"><i class="fa fa-fw fa-pencil"></i>' . $this->translate('Edit') . '</a>
                                    <a href="' . $this->generateUrl('page_delete', array('id' => $navitem->page_id)) . '" class="btn btn-primary btn-xs btn-icon confirm" data-message="' . $this->translate('Are you sure you want to delete this page and all of its subpage?') . '"><i class="fa fa-fw fa-trash-o"></i>' . $this->translate('Delete') . '</a>
                                </span>';

                $childNavitems = $navitem->childNavitems()
                    ->orderByAsc('position')
                    ->fetchAll();

                $output .= $this->renderNavitemNestable($childNavitems);

                $output .= '</li>';
            }
            $output .= '</ol>';
        }
        return $output;
    }

    public function renderNavitemOptions(array $navitems, $level = 0, $selectedNavitemId = 0)
    {
        $output = '';
        foreach ($navitems as $navitem) {
            $output .= '<option ' . ($selectedNavitemId === $navitem->id() ? 'selected' : '') . ' data-level="' . $level . '" value="' . $navitem->id() . '">' . $navitem->title . '</option>';

            $childNavitems = $navitem->childNavitems()
                ->orderByAsc('position')
                ->fetchAll();

            $output .= $this->renderNavitemOptions($childNavitems, $level + 1, $selectedNavitemId);
        }

        return $output;
    }
}
