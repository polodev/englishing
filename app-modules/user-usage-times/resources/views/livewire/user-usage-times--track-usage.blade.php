<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\UserUsageTimes\Models\DailyUserUsageTime;


new class extends Component {
    public $seconds = 0;

    public function mount()
    {
        $this->seconds = 10;
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
    
};
?>
{{-- <div wire:poll.60s="incrementUsage">
</div> --}}

<div>
    user-usage-times {{$seconds}}
</div>

