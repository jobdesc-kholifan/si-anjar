<?php

namespace App\View\Components;

class Button
{

    const btnPrimary = 'btn-outline-primary';
    const btnSecondary = 'btn-outline-secondary';
    const btnDanger = 'btn-outline-danger';
    const btnSuccess = 'btn-outline-success';
    const btnInfo = 'btn-info';
    const btnLinkPrimary = 'btn-link';

    const btnIconEdit = '<i class="fa fa-edit"></i>';
    const btnIconDelete = '<i class="fa fa-trash"></i>';
    const btnIconInfo = '<i class="fa fa-info-circle"></i>';
    const btnIconPrint = '<i class="fa fa-print"></i>';
    const btnIconFile = '<i class="fa fa-file-alt"></i>';
    const btnIconApproved = '<i class="fa fa-check"></i>';
    const btnIconFileDownload = '<i class="fa fa-file-download"></i>';
    const btnIconFileExcel = '<i class="fa fa-file-excel"></i>';

    public $onclick;

    public $classname;

    public $icon;

    public $label;

    public $size;

    public $align;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($onclick, $classname, $icon = null, $size = 'btn-xs')
    {
        $this->onclick = $onclick;
        $this->classname = $classname;
        $this->icon = $icon;
        $this->size = $size;
    }

    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    public function setAlign($align)
    {
        $this->align = $align;
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
            'size' => $this->size,
            'align' => $this->align,
        ]);
    }
}
