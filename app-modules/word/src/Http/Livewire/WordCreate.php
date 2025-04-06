<?php

namespace Modules\Word\Http\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Str;
use Modules\Word\Models\Word;

class WordCreate extends Component
{
    public bool $showModal = false;
    public bool $showSuccessMessage = false;
    public ?int $createdWordId = null;
    public ?string $createdWordName = null;
    
    // No event listeners needed - we'll use direct method calls
    
    // Form fields
    public string $word = '';
    public string $slug = '';
    public string $phonetic = '';
    public ?string $part_of_speech = null;
    public array $pronunciation = [
        'bn' => '',
        'hi' => ''
    ];
    
    protected $rules = [
        'word' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:words,slug',
        'phonetic' => 'nullable|string|max:255',
        'part_of_speech' => 'nullable|string|max:255',
        'pronunciation.bn' => 'nullable|string|max:255',
        'pronunciation.hi' => 'nullable|string|max:255',
    ];
    
    public function getPartsOfSpeech()
    {
        return Word::getPartsOfSpeech();
    }
    
    public function updatedWord($value)
    {
        $this->slug = Str::slug($value);
    }
    
    public function openModal()
    {
        // Add debugging
        logger('WordCreate::openModal called');
        
        $this->reset(['word', 'slug', 'phonetic', 'part_of_speech', 'pronunciation', 'showSuccessMessage', 'createdWordId', 'createdWordName']);
        $this->showModal = true;
        
        // Force a refresh of the component
        $this->dispatchBrowserEvent('modal-opened');
    }
    
    public function closeModal()
    {
        $this->showModal = false;
    }
    
    public function createWord()
    {
        $this->validate();
        
        // Filter out empty pronunciation values
        $pronunciationData = array_filter($this->pronunciation, fn($value) => !empty($value));
        
        // Create the word
        $word = Word::create([
            'word' => $this->word,
            'slug' => $this->slug,
            'phonetic' => $this->phonetic,
            'part_of_speech' => $this->part_of_speech,
        ]);
        
        // Set pronunciations using Spatie's translatable
        foreach ($pronunciationData as $locale => $value) {
            $word->setTranslation('pronunciation', $locale, $value);
        }
        $word->save();
        
        // Show success message
        $this->createdWordId = $word->id;
        $this->createdWordName = $word->word;
        $this->showSuccessMessage = true;
    }
    
    public function addAnotherWord()
    {
        $this->reset(['word', 'slug', 'phonetic', 'part_of_speech', 'pronunciation', 'showSuccessMessage']);
    }
    
    public function render()
    {
        return view('word::livewire.word-create');
    }
}
