<?php
 
use function Livewire\Volt\{state, layout};
 
state(['count' => 0]);
layout('layouts.end_user_layout');

$increment = fn () => $this->count++;
 
?>

<x-container>
    <x-page-h1>Hello world from shibu</x-page-h1>
</x-container>