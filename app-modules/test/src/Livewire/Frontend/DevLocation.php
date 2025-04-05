<?php

namespace Modules\Test\Livewire\Frontend;

use Livewire\Component;

class DevLocation extends Component
{
    public $name = 'shibu';
    public function render()
    {
        return view('test::livewire.frontend.dev-location');
    }
}