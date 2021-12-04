<?php

namespace App\View\Components;

class Checkbox
{

    protected $id;

    protected $name;

    protected $label;

    protected $value;

    protected $fullName;

    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.checkbox', [
            'id' => $this->id,
            'name' => $this->name,
            'label' => $this->label,
            'value' => $this->value,
            'fullName' => $this->fullName,
        ]);
    }
}
