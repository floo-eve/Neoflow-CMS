<?php

namespace Lahaina\CMS\Views\Backend;

class NavigationView extends \Lahaina\CMS\Views\BackendView
{

    public function renderNavitemNestable(array $navitems)
    {
        $output = '';
        if ($navitems) {
            $output .= '<ol class="nestable-list list-group">';

            foreach ($navitems as $navitem) {
                $output .= '<li class="nestable-item list-group-item" data-id="' . $navitem->id() . '">
                                <span class="nestable-handle"><i class="fa fa-arrows"></i></span>
                                <a href="' . $this->generateUrl('navigation_edit', array('id' => $navitem->id())) . '">' . $navitem->title . '</a>
                                <span class="pull-right">
                                    <a href="' . $this->generateUrl('navigation_edit', array('id' => $navitem->id())) . '" class="btn btn-default btn-xs">Bearbeiten</a>
                                    <a href="' . $this->generateUrl('navigation_delete', array('id' => $navitem->id())) . '" class="btn btn-primary btn-xs">Löschen</a>
                                </span>';

                $childNavitems = $navitem->childNavitems()
                    ->orderByAsc('position')
                    ->fetchAll();

                $output .= $this->renderNestableList($childNavitems);

                $output .= '</li>';
            }
            $output .= '</ol>';
        }

        return $output;
    }

    public function renderSelectOption(array $navitems, $level = 0)
    {
        $output = '';
        foreach ($navitems as $navitem) {
            $output .= '<option data-level="' . $level . '" value="' . $navitem->page_id . '">' . $navitem->title . '</option>';

            $childNavitems = $navitem->childNavitems()
                ->orderByAsc('position')
                ->fetchAll();

            $output .= $this->renderSelectOption($childNavitems, $level + 1);
        }

        return $output;
    }
}
