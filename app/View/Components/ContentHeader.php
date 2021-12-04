<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ContentHeader extends Component
{

    public $title;

    public $subTitle;

    public $breadcrumbs = array();

    /**
     * Create a new component instance.
     *
     * @param $title
     */
    public function __construct($title, $subTitle = '', $breadcrumbs = array())
    {
        $this->title = $title;
        $this->subTitle = $subTitle;
        $this->breadcrumbs = $breadcrumbs;
    }

    public function isBreadcrumbActive($breadcrumb)
    {
        return !empty($breadcrumb['active']) && $breadcrumb['active'] ? ' active' : '';
    }

    public function breadcrumbLabel($breadcrumb)
    {
        return !empty($breadcrumb['label']) ?  $breadcrumb['label'] : '-';
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.content-header');
    }
}
