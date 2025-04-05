<?php

namespace Modules\UserUsageTimes\Livewire;

use Livewire\Component;

class TrackUsage extends Component
{
    public $name = 'shibu';
    public function render()
    {
        return view('user-usage-times::livewire.track-usage');
    }
}