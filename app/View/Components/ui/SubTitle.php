<?php

namespace App\View\Components\ui;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SubTitle extends Component
{
    public string $title;
    public string $customClass;

    /**
     * Komponen title
     *
     * @param string $title title komponen
     * @param string $customClass class untuk styling custom menggunakan tailwind css (optional)
     */
    public function __construct(string $title, string $customClass = '') // Menambahkan nilai default pada $customClass
    {
        $this->title = $title;
        $this->customClass = $customClass;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.ui.sub-title');
    }
}
