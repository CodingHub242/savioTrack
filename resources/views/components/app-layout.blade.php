<?php

use Illuminate\View\Components\AppLayout;

class AppLayout extends AppLayout
{
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return function (array $data) {
            return view('layouts.app', array_merge($data, [
                'header' => $data['header'] ?? null,
            ]));
        };
    }
}
