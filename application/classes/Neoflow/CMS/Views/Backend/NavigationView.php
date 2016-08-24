<?php

namespace Neoflow\CMS\Views\Backend;

use Neoflow\CMS\Views\BackendView;
use Neoflow\Framework\ORM\EntityCollection;

class NavigationView extends BackendView
{

    public function renderNavitemOptions(EntityCollection $navitems, $level = 0, $selectedNavitemId = null, array $disabledNavitemIds = array())
    {
        $output = '';
        foreach ($navitems as $navitem) {
            $output .= '<option ' . (in_array($navitem->id(), $disabledNavitemIds) ? 'disabled' : '') . ' ' . ($selectedNavitemId === $navitem->id() ? 'selected' : '') . ' data-level="' . $level . '" value="' . $navitem->id() . '">' . $navitem->title . '</option>';

            $childNavitems = $navitem->childNavitems()
                ->orderByAsc('position')
                ->fetchAll();

            if (in_array($navitem->id(), $disabledNavitemIds)) {
                $disabledNavitemIds = $childNavitems->map(function ($navitem) {
                        return $navitem->id();
                    })->toArray();
            }

            $output .= $this->renderNavitemOptions($childNavitems, $level + 1, $selectedNavitemId, $disabledNavitemIds);
        }

        return $output;
    }

    /**
     * Render navitems.
     *
     * @param EntityCollection $navitems
     *
     * @return string
     */
    public function renderNavitemNestable(EntityCollection $navitems)
    {
        $output = '';
        if ($navitems->count()) {
            $output .= '<ol class="nestable-list list-group">';

            foreach ($navitems as $navitem) {
                $page = $navitem->page()->fetch();

                $output .= '<li class="nestable-item list-group-item ' . (!$page->is_active ? 'list-groupd-item-disabled' : '') . '" data-collapsed="' . $this->app()->get('request')->getCookies()->exists($navitem->id()) . '" data-id="' . $navitem->id() . '">
                            <span class="nestable-handle">
                                <i class="fa fa-fw fa-arrows"></i>
                            </span>
                            <span class="nestable-content">
                                <ul class="list-inline">
                                    <li>';

                if (!$page->is_active) {
                    $output .= ' <i class="fa fa-fw fa-ban"></i>';
                }
                $output .= '</li>
                                <li>';

                if ($navitem->navigation_id == 1) {
                    $output .= $navitem->title;
                } else {
                    $output .= '<a href="' . generate_url('navigation_edit_navitem', array('id' => $navitem->id())) . '">
                                        ' . $navitem->title . '
                                    </a>';
                }
                $output .= '</li>
                                <li class="small">
                                    ID: ' . $navitem->id() . '
                                </li>
                            </ul>
                            <span class="pull-right">';

                $output .= '<a href="' . generate_url('page_sections', array('id' => $page->id())) . '" class="btn btn-default btn-xs hidden-xs" title="' . translate('Page') . '">
                                <i class="fa fa-fw fa-columns"></i>
                            </a>
                            <a ' . ($navitem->navigation_id == 1 ? 'disabled' : '') . ' href="' . generate_url('navigation_delete_navitem', array('id' => $navitem->id())) . '" class="btn btn-primary btn-xs confirm" data-message="' . translate('Are you sure you want to delete this navigation item?') . '" title="' . translate('Delete') . '">
                                <i class="fa fa-fw fa-trash-o"></i>
                            </a>
                            </span>
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
}
