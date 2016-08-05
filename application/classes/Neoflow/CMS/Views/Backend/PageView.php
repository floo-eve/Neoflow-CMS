<?php

namespace Neoflow\CMS\Views\Backend;

use Neoflow\Framework\Common\Collection;
use Neoflow\Framework\ORM\EntityCollection;

class PageView extends NavigationView
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

                $output .= '<li class="nestable-item list-group-item ' . (!$page->is_active ? 'list-groupd-item-disabled' : '') . '" data-collapsed="' . $this->cookies->exists($navitem->id()) . '" data-id="' . $navitem->id() . '">
                            <span class="nestable-handle">
                                <i class="fa fa-fw fa-arrows"></i>
                            </span>
                            <span class="nestable-content">
                                <ul class="list-inline">
                                    <li>';

                if (!$page->is_active) {
                    $output .= ' <i class="fa fa-fw fa-ban"></i>';
                } elseif ($page->visibility === 'restricted') {
                    $output .= ' <i class="fa fa-fw fa-eye-slash"></i>';
                } elseif ($page->visibility === 'hidden') {
                    $output .= ' <i class="fa fa-fw fa-eye-slash"></i>';
                } else {
                    $output .= ' <i class="fa fa-fw fa-eye"></i>';
                }
                $output .= '</li>
                                <li>
                                    <a href="' . $this->generateUrl('page_sections', array('id' => $navitem->page_id)) . '">
                                        ' . $navitem->title . '
                                    </a>
                                </li>
                                <li class="small">
                                    ID: ' . $navitem->id() . '
                                </li>
                            </ul>
                            <span class="pull-right">
                                    <a href="' . $this->generateUrl('page_sections', array('id' => $page->id())) . '" class="btn btn-default btn-xs hidden-xs" title="' . $this->translate('Sections') . '">
                                        <i class="fa fa-fw fa-th-list"></i>
                                    </a>
                                    <a href="' . $this->generateUrl('page_settings', array('id' => $page->id())) . '" class="btn btn-default btn-xs hidden-xs" title="' . $this->translate('Settings') . '">
                                        <i class="fa fa-fw fa-cog"></i>
                                    </a>';

                if ($page->is_active) {
                    $output .= ' <a href="' . $this->generateUrl('page_activate', array('id' => $page->id())) . '" class="btn btn-warning btn-xs confirm" data-message="' . $this->translate('Are you sure you want to disable it?') . '" title="' . $this->translate('Disable') . '">
                                    <i class="fa fa-fw fa-ban"></i>
                                </a>';
                } else {
                    $output .= ' <a href="' . $this->generateUrl('page_activate', array('id' => $page->id())) . '" class="btn btn-success btn-xs confirm" data-message="' . $this->translate('Are you sure you want to activate it?') . '" title="' . $this->translate('Activate') . '">
                                    <i class="fa fa-fw fa-eye"></i>
                                </a>';
                }

                $output .= ' <a href="' . $this->generateUrl('page_delete', array('id' => $page->id())) . '" class="btn btn-danger btn-xs confirm" data-message="' . $this->translate('Are you sure you want to delete this page and all of its subpage?') . '" title="' . $this->translate('Delete') . '">
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

    public function renderNavitemOptions(EntityCollection $navitems, $level = 0, $selectedNavitemId = null, array $disabledNavitemIds = array())
    {
        $output = '';
        foreach ($navitems as $navitem) {
            $output .= '<option ' . (in_array($navitem->id(), $disabledNavitemIds) ? 'disabled' : '') . ' ' . ($selectedNavitemId === $navitem->id() ? 'selected' : '') . ' data-level="' . $level . '" value="' . $navitem->id() . '">' . $navitem->title . '</option>';

            $childNavitems = $navitem->childNavitems()
                ->orderByAsc('position')
                ->fetchAll();

            if (in_array($navitem->id(), $disabledNavitemIds)) {
                $disabledNavitemIds = $childNavitems->map(function($navitem) {
                    return $navitem->id();
                });
            }

            $output .= $this->renderNavitemOptions($childNavitems, $level + 1, $selectedNavitemId, $disabledNavitemIds);
        }

        return $output;
    }

    /**
     * Render sections.
     *
     * @param array $sections
     *
     * @return string
     */
    public function renderSectionNestable(array $sections)
    {
        $output = '';
        if ($sections) {

            $output .= '<ol class="nestable-list list-group">';

            foreach ($sections as $section) {
                $module = $section->module()->fetch();

                $output .= '<li class="nestable-item list-group-item ' . (!$section->is_active ? 'list-groupd-item-disabled' : '') . '" data-collapsed="' . $this->cookies->exists($section->id()) . '" data-id="' . $section->id() . '">
                            <span class="nestable-handle">
                                <i class="fa fa-fw fa-arrows"></i>
                            </span>
                            <span class="nestable-content">
                                <ul class="list-inline">
                                    <li>';

                if (!$section->is_active) {
                    $output .= ' <i class="fa fa-fw fa-ban"></i>';
                } else {
                    $output .= ' <i class="fa fa-fw fa-eye"></i>';
                }

                $output .= '</li>
                                <li>
                                    <a href="' . $this->generateUrl($module->route, array('section_id' => $section->id())) . '">
                                        ' . $module->title . '
                                    </a>
                                </li>
                                <li class="small">
                                    ID: ' . $section->id() . '
                                </li>
                            </ul>
                            <span class="pull-right">
                                    <a href="' . $this->generateUrl($module->route, array('section_id' => $section->id())) . '" class="btn btn-default btn-xs hidden-xs btn-icon btn-icon-left" title="' . $this->translate('Edit') . '">
                                        <i class="fa fa-fw fa-pencil"></i>' . $this->translate('Edit') . '
                                    </a>';

                if ($section->is_active) {
                    $output .= ' <a href="' . $this->generateUrl('section_activate', array('id' => $section->id())) . '" class="btn btn-warning btn-xs confirm" data-message="' . $this->translate('Are you sure you want to disable it?') . '"" title="' . $this->translate('Disable') . '">
                                    <i class="fa fa-fw fa-ban"></i>
                                </a>';
                } else {
                    $output .= ' <a href="' . $this->generateUrl('section_activate', array('id' => $section->id())) . '" class="btn btn-success btn-xs confirm" data-message="' . $this->translate('Are you sure you want to activate it?') . '"" title="' . $this->translate('Activate') . '">
                                    <i class="fa fa-fw fa-eye"></i>
                                </a>';
                }

                $output .= ' <a href="' . $this->generateUrl('section_delete', array('id' => $section->id())) . '" class="btn btn-danger btn-xs confirm" data-message="' . $this->translate('Are you sure you want to delete this section and all of its content?') . '" title="' . $this->translate('Delete') . '">
                                        <i class="fa fa-fw fa-trash-o"></i>
                                    </a>
                            </span>
                        </span>
                        </li>';
            }
            $output .= '</ol>';
        }
        return $output;
    }
}
