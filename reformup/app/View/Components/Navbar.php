<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Navbar extends Component
{
    public $active;
    public $logo;

    public function __construct($active = '', $logo = '')
    {
        $this->active = $active;
        $this->logo = $logo ?: asset('img/logoPNGReformupNuevo.svg');
    }

    public function render()
    {
        return view('components.navbar');
    }
}

