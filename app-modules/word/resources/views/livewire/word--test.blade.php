<?php

use Livewire\Volt\Component;

new class extends Component {
    public $hello = 'world';
    public $name = '';

    public function testClick() {
        dd($this);
    }
};
?>

<div>
    <!-- word--test Volt component -->
    <input type="text" wire:model="name" class="border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-gray-200">
    <button onclick="alert('test')" wire:click="testClick" type="button" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring focus:ring-green-300 disabled:opacity-25 transition">
        Test {{ $hello }}
    </button>
</div>