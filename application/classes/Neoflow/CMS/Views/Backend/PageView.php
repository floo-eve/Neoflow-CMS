<?php

namespace Neoflow\CMS\Views\Backend;

use Neoflow\Framework\ORM\EntityCollection;

class PageView extends NavitemView
{

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
                } else if ($page->is_restricted) {
                    $output .= ' <i class="fa fa-fw fa-eye-slash"></i>';
                } else {
                    $output .= ' <i class="fa fa-fw fa-eye"></i>';
                }
                $output .= '</li>
                                <li>
                                    <a href="' . generate_url('section_index', array('id' => $navitem->page_id)) . '">
                                        ' . $page->title . '
                                    </a>
                                </li>
                                <li class="small text-muted">
                                    ID: ' . $navitem->id() . '
                                </li>
                            </ul>
                            <span class="pull-right">
                                    <a href="' . generate_url('section_index', array('id' => $page->id())) . '" class="btn btn-default btn-xs hidden-xs" title="' . translate('Sections') . '">
                                        <i class="fa fa-fw fa-th-list"></i>
                                    </a>
                                    <a href="' . generate_url('page_edit', array('id' => $page->id())) . '" class="btn btn-default btn-xs hidden-xs" title="' . translate('Settings') . '">
                                        <i class="fa fa-fw fa-cog"></i>
                                    </a>';

                if ($page->is_active) {
                    $output .= ' <a href="' . generate_url('page_toggle_activation', array('id' => $page->id())) . '" class="btn btn-default btn-xs confirm" data-message="' . translate('Are you sure you want to disable it?') . '" title="' . translate('Disable') . '">
                                    <i class="fa fa-fw fa-ban"></i>
                                </a>';
                } else {
                    $output .= ' <a href="' . generate_url('page_toggle_activation', array('id' => $page->id())) . '" class="btn btn-default btn-xs confirm" data-message="' . translate('Are you sure you want to activate it?') . '" title="' . translate('Enable') . '">
                                    <i class="fa fa-fw fa-eye"></i>
                                </a>';
                }

                $output .= ' <a href="' . generate_url('page_delete', array('id' => $page->id())) . '" class="btn btn-primary btn-xs confirm" data-message="' . translate('Are you sure you want to delete this page and all of its subpage?') . '" title="' . translate('Delete') . '">
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
