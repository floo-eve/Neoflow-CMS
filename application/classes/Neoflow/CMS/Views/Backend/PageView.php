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

                $output .= ' <a href="' . $this->generateUrl($module->route, array('section_id' => $section->id())) . '">' . $module->title . '</a></li>
                                <li class="small">ID: ' . $section->id() . '</li>
                            </ul>
                            <span class="pull-right">
                                <a href="' . $this->generateUrl($module->route, array('section_id' => $section->id())) . '" class="btn btn-default btn-xs hidden-xs btn-icon" title="' . $this->translate('Edit') . '"><i class="fa fa-fw fa-pencil"></i>' . $this->translate('Edit') . '</a>';

                if ($section->is_active) {
                    $output .= ' <a href="' . $this->generateUrl('section_disable', array('id' => $section->id())) . '" class="btn btn-warning btn-xs" title="' . $this->translate('Disable') . '"><i class="fa fa-fw fa-ban"></i></a>';
                } else {
                    $output .= ' <a href="' . $this->generateUrl('section_activate', array('id' => $section->id())) . '" class="btn btn-success btn-xs" title="' . $this->translate('Activate') . '"><i class="fa fa-fw fa-eye"></i></a>';
                }





                $output .= ' <a href="' . $this->generateUrl('section_delete', array('id' => $section->id())) . '" class="btn btn-danger btn-xs confirm" data-message="' . $this->translate('Are you sure you want to delete this section?') . '" title="' . $this->translate('Delete') . '"><i class="fa fa-fw fa-trash-o"></i></a>
                            </span>
                        </li>';
            }
            $output .= '</ol>';
        }
        return $output;
    }
}
