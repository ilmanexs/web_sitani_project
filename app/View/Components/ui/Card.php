<?php

namespace App\View\Components\ui;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Card extends Component
{
    public $extraClassOptions;

    /**
     * Membuat parameter $extraClassOptions opsional
     *
     * @param string $extraClassOptions
     */
    public function __construct($extraClassOptions = '') // Menambahkan nilai default pada $extraClassOptions
    {
        $this->extraClassOptions = $extraClassOptions;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.ui.card');
    }
}
