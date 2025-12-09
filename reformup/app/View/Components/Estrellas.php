<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Estrellas extends Component
{
    public float $valor;

    public function __construct($valor = 0)
    {
        $this->valor = (float) $valor;
    }

    public function render()
    {
        return view('components.estrellas');
    }
}

