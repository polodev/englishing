<?php
 
use function Livewire\Volt\{state, layout};
 
state(['count' => 0]);

$increment = fn () => $this->count++;
 
?>

<div>Hello world from volt</div>
