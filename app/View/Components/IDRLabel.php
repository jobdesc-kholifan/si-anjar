<?php


namespace App\View\Components;


class IDRLabel
{

    public $value;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($value, $symbol = 'Rp. ', $decimal = 0, $dec_point = ".", $thousands_sep = ",")
    {
        $this->value = IDR($value, $symbol, $decimal, $dec_point, $thousands_sep);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.idrlabel', [
            'value' => $this->value,
        ]);
    }

}
