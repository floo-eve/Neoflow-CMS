<?php

namespace Lahaina\CMS\Views\Backend;

class PageView extends \Lahaina\CMS\Views\BackendView
{

    /**
     * Render navitems
     * @param array $navitems
     * @return string
     */
    public function renderNavitemNestable(array $navitems)
    {
        $output = '';
        if ($navitems) {
            $output .= '<ol class="nestable-list list-group">';

            foreach ($navitems as $navitem) {
                $output .= '<li class="nestable-item list-group-item" data-collapsed="' . $navitem->collapsed . '" data-id="' . $navitem->id() . '">
                                <span class="nestable-handle"><i class="fa fa-arrows"></i></span>
                                <a href="' . $this->generateUrl('page_edit', array('id' => $navitem->page_id)) . '">' . $navitem->title . '</a>
                                <span class="pull-right">
                                    <a href="' . $this->generateUrl('page_edit', array('id' => $navitem->page_id)) . '" class="btn btn-default btn-xs hidden-xs"><i class="fa fa-pencil"></i> ' . $this->translate('Edit') . '</a>
                                    <a href="' . $this->generateUrl('page_delete', array('id' => $navitem->page_id)) . '" class="btn btn-primary btn-xs"><i class="fa fa-trash-o"></i><span class="hidden-xs"> ' . $this->translate('Delete') . '</span></a>
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

    public function renderNavitemOptions(array $navitems, $level = 0)
    {
        $output = '';
        foreach ($navitems as $navitem) {
            $output .= '<option data-level="' . $level . '" value="' . $navitem->id() . '">' . $navitem->title . '</option>';

            $childNavitems = $navitem->childNavitems()
                ->orderByAsc('position')
                ->fetchAll();

            $output .= $this->renderNavitemOptions($childNavitems, $level + 1);
        }

        return $output;
    }
}