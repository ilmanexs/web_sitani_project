<?php

namespace App\View\Components\ui;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;


class DropdownAction extends Component
{
    public array $items;
    public string $title;
    public string $color;
    public string $style;

    /**
     * Dropdown action button component
     * @param array $items item dropdown(elemen/component)
     * @param string $title title dropdown button, default 'Dropdown'
     * @param string $color warna dropdown component, default 'btn-accent'
     * @param string $style style dropdown component, default 'btn-soft'
     */
    public function __construct(
        array $items = [],
        string $title = 'Dropdown',
        string $color = 'btn-accent',
        string $style = 'btn-accent'
    )
    {
        $this->items = $items;
        $this->title = $title;
        $this->color = $color;
        $this->style = $style;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components..ui.dropdown-action');
    }
}
