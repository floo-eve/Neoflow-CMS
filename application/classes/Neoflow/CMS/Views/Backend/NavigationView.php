<?php

namespace Neoflow\CMS\Views\Backend;

use Neoflow\CMS\Views\BackendView;
use Neoflow\Framework\ORM\EntityCollection;

class NavigationView extends BackendView
{

    public function renderNavitemOptions(EntityCollection $navitems, $level = 0, $disabledProperty = null, array $disabledProperties = array(), $valueProperty = 'navitem_id')
    {
        $output = '';
        foreach ($navitems as $navitem) {
            $output .= '<option ' . (in_array($navitem->$valueProperty, $disabledProperties) ? 'disabled' : '') . ' ' . ($disabledProperty === $navitem->$valueProperty ? 'selected' : '') . ' data-level="' . $level . '" value="' . $navitem->$valueProperty . '">' . $navitem->title . '</option>';

            $childNavitems = $navitem->childNavitems()
                ->orderByAsc('position')
                ->fetchAll();

            if (in_array($navitem->$valueProperty, $disabledProperties)) {
                $disabledProperties = $childNavitems->map(function ($navitem) use($valueProperty) {
                        return $navitem->$valueProperty;
                    })->toArray();
            }

            $output .= $this->renderNavitemOptions($childNavitems, $level + 1, $disabledProperty, $disabledProperties);
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

                $output .= '<li class="nestable-item list-group-item ' . (!$navitem->is_visible ? 'list-groupd-item-disabled' : '') . '" data-collapsed="' . $this->app()->get('request')->getCookies()->exists($navitem->id()) . '" data-id="' . $navitem->id() . '">
                            <span class="nestable-handle">
                                <i class="fa fa-fw fa-arrows"></i>
                            </span>
                            <span class="nestable-content">
                                <ul class="list-inline">
                                    <li>';

                if (!$navitem->is_visible) {
                    $output .= ' <i class="fa fa-fw fa-ban"></i>';
                } else {
                    $output .= ' <i class="fa fa-fw fa-eye"></i>';
                }
                $output .= '</li>
                                <li>
                                    <a href="' . generate_url('page_sections', array('id' => $navitem->page_id)) . '">
                                        ' . $navitem->title . '
                                    </a>
                                </li>
                                <li class="small text-muted">
                                    ID: ' . $navitem->id() . '
                                </li>
                                <li class="small text-muted">
                                    ' . translate('Page title') . ': ' . $page->title . '
                                </li>
                            </ul>
                            <span class="pull-right">';

                if ($navitem->is_visible) {
                    $output .= ' <a href="' . generate_url('navitem_toggle_visiblity', array('id' => $navitem->id())) . '" class="btn btn-default btn-xs confirm" data-message="' . translate('Are you sure you want to hide it?') . '"" title="' . translate('Hide') . '">
                                    <i class="fa fa-fw fa-ban"></i>
                                </a>';
                } else {
                    $output .= ' <a href="' . generate_url('navitem_toggle_visiblity', array('id' => $navitem->id())) . '" class="btn btn-default btn-xs confirm" data-message="' . translate('Are you sure you want to make it visible?') . '"" title="' . translate('Make visible') . '">
                                    <i class="fa fa-fw fa-eye"></i>
                                </a>';
                }

                $output .= ' <a ' . ($navitem->navigation_id == 1 ? 'disabled' : '') . ' href="' . generate_url('navitem_delete', array('id' => $navitem->id())) . '" class="btn btn-primary btn-xs confirm" data-message="' . translate('Are you sure you want to delete this and all of its subnavigation items?') . '" title="' . translate('Delete') . '">
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
