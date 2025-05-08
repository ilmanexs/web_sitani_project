<?php

namespace App\View\Components\ui\button;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Route;
use Illuminate\View\Component;

class EditButton extends Component
{
    public string $color;
    public string $icon;
    public string $style;
    public string $title;
    public mixed $route;
    public string $extraClassOption;
    public string $permission;

    /**
     * @param string $color warna button
     * @param string $icon icon button(opsional)
     * @param string $style style button
     * @param string $title title button
     * @param ?Route $route route
     * @param string $extraClassOption class tailwind tambahan untuk custom style button
     * @param string $permission permission
     */
    public function __construct(string $color = 'btn-warning',
                                string $icon = 'icon-[hugeicons--file-edit]',
                                string $style = 'btn-soft',
                                string $title = 'Perbarui',
                                mixed $route = null,
                                string $extraClassOption = '',
                                string $permission = ''
    )
    {
        $this->color = $color;
        $this->icon = $icon;
        $this->style = $style;
        $this->title = $title;
        $this->route = $route;
        $this->extraClassOption = $extraClassOption;
        $this->permission = $permission;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.ui.button.edit-button');
    }
}
