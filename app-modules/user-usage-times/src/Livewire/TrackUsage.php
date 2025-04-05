<?php

namespace Modules\UserUsageTimes\Livewire;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Modules\UserUsageTimes\Models\DailyUserUsageTime;

class TrackUsage extends Component
{
    public $seconds = 0;

    public function mount()
    {
        $this->seconds = 0;
    }

    public function incrementUsage()
    {
        $user = Auth::user();

        if (!$user) return;

        $today = now()->toDateString();

        DailyUserUsageTime::updateOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            ['total_seconds' => DB::raw('total_seconds + 60')] // every 60 seconds
        );
    }

    public function render()
    {
        return view('livewire.track-usage');
    }
}
