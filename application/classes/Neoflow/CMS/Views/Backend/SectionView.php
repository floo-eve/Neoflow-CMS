<?php

namespace Neoflow\CMS\Views\Backend;

class SectionView extends \Neoflow\CMS\Views\BackendView
{

    /**
     * Render sections.
     *
     * @param EntityCollection $sections
     *
     * @return string
     */
    public function renderSectionNestable(\Neoflow\Framework\ORM\EntityCollection $sections)
    {
        $output = '';
        if ($sections->count()) {
            $output .= '<ol class="nestable-list list-group">';

            foreach ($sections as $section) {
                $module = $section->module()->fetch();

                $output .= '<li class="nestable-item list-group-item ' . (!$section->is_active ? 'list-groupd-item-disabled' : '') . '" data-collapsed="' . $this->app()->get('request')->getCookies()->exists($section->id()) . '" data-id="' . $section->id() . '">
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
                                    <a href="' . generate_url($module->backend_route, array('section_id' => $section->id())) . '" title="' . translate('Edit section') . '">
                                        ' . $module->title . '
                                    </a>
                                </li>
                                <li class="small hidden-xs">
                                    ID: ' . $section->id() . '
                                </li>
                            </ul>
                            <span class="pull-right">
                                    <a href="' . generate_url('section_edit', array('id' => $section->id())) . '" class="btn btn-default btn-xs hidden-xs" title="' . translate('Edit section') . '">
                                          <i class="fa fa-fw fa-pencil"></i>
                                    </a>';

                if ($section->is_active) {
                    $output .= ' <a href="' . generate_url('section_toggle_activation', array('id' => $section->id())) . '" class="btn btn-default btn-xs confirm" data-message="' . translate('Are you sure you want to disable it?') . '" title="' . translate('Disable section') . '">
                                    <i class="fa fa-fw fa-ban"></i>
                                </a>';
                } else {
                    $output .= ' <a href="' . generate_url('section_toggle_activation', array('id' => $section->id())) . '" class="btn btn-default btn-xs confirm" data-message="' . translate('Are you sure you want to enable it?') . '" title="' . translate('Enable section') . '">
                                    <i class="fa fa-fw fa-eye"></i>
                                </a>';
                }

                $output .= ' <a href="' . generate_url('section_delete', array('id' => $section->id())) . '" class="btn btn-primary btn-xs confirm" data-message="' . translate('Are you sure you want to delete this section and all of its content?') . '" title="' . translate('Delete section') . '">
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
