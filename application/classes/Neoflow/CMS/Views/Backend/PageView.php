<?php

namespace Neoflow\CMS\Views\Backend;

class PageView extends NavigationView
{

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
                            <span class="nestable-handle"><i class="fa fa-fw fa-arrows"></i></span>

                            <ul class="list-inline">
                                <li>';

                if (!$section->is_active) {
                    $output .= ' <i class="fa fa-fw fa-ban"></i>';
                } else {
                    $output .= ' <i class="fa fa-fw fa-eye"></i>';
                }

                $output .= ' <a href="' . $this->generateUrl($module->route) . '">' . $module->title . '</a></li>
                                <li class="small">ID: ' . $section->id() . '</li>
                            </ul>
                            <span class="pull-right">
                                <a href="#" class="btn btn-default btn-xs hidden-xs btn-icon"><i class="fa fa-fw fa-pencil"></i>' . $this->translate('Edit') . '</a>
                                <a href="' . $this->generateUrl('page_delete_section', array('id' => $section->id())) . '" class="btn btn-primary btn-xs btn-icon confirm" data-message="' . $this->translate('Are you sure you want to delete this section?') . '"><i class="fa fa-fw fa-trash-o"></i><span class="hidden-xs">' . $this->translate('Delete') . '</span></a>
                            </span>
                        </li>';
            }
            $output .= '</ol>';
        }
        return $output;
    }
}
