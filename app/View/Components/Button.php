<?php

namespace App\View\Components;

class Button
{

    const btnPrimary = 'btn-outline-primary';
    const btnDanger = 'btn-outline-danger';
    const btnSuccess = 'btn-outline-success';
    const btnInfo = 'btn-info';

    const btnIconEdit = '<i class="fa fa-edit"></i>';
    const btnIconDelete = '<i class="fa fa-trash"></i>';
    const btnIconInfo = '<i class="fa fa-info-circle"></i>';
    const btnIconPrint = '<i class="fa fa-print"></i>';

    public $onclick;

    public $classname;

    public $icon;

    public $label;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($onclick, $classname, $icon = null)
    {
        $this->onclick = $onclick;
        $this->classname = $classname;
        $this->icon = $icon;
    }

    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.button', [
            'onclick' => $this->onclick,
            'classname' => $this->classname,
            'icon' => $this->icon,
            'label' => $this->label,
        ]);
    }
}
