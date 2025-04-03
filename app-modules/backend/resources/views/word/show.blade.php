<x-ui-backend::layout>
<style>
    .word-container {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .word-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        padding: 20px;
        border-radius: 8px 8px 0 0;
    }
    .word-title {
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
        color: #333;
    }
    .word-body {
        padding: 20px;
    }
    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: #495057;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 0.5rem;
    }
    .meaning-item {
        margin-bottom: 1.5rem;
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 6px;
    }
    .meaning-text {
        font-weight: 600;
        font-size: 1.1rem;
        margin-bottom: 0.75rem;
    }
    .translation-container {
        margin-left: 1rem;
        padding: 10px;
        background-color: #fff;
        border-radius: 4px;
        border-left: 3px solid #007bff;
    }
    .transliteration-container {
        margin-left: 2rem;
        margin-top: 0.5rem;
        padding: 8px;
        background-color: #e9ecef;
        border-radius: 4px;
        font-style: italic;
    }
    .language-label {
        font-weight: bold;
        color: #007bff;
        display: inline-block;
        width: 40px;
    }
    .pronunciation-container {
        margin-bottom: 1.5rem;
        padding: 15px;
        background-color: #e9ecef;
        border-radius: 6px;
    }
    .related-words-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .related-word-badge {
        display: inline-block;
        padding: 5px 10px;
        background-color: #e9ecef;
        border-radius: 20px;
        font-size: 0.9rem;
        color: #495057;
        text-decoration: none;
        transition: all 0.2s;
    }
    .related-word-badge:hover {
        background-color: #007bff;
        color: #fff;
    }
    .back-button {
        margin-bottom: 20px;
    }
</style>

<div class="container py-4">
    <div class="back-button">
        <a href="{{ route('backend::words.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Words List
        </a>
    </div>

    <div class="word-container">
        <div class="word-header">
            <h1 class="word-title">{{ $word->word }}</h1>
            <div class="text-muted">
                <small>Created: {{ $word->created_at->format('Y-m-d H:i:s') }} | Updated: {{ $word->updated_at->format('Y-m-d H:i:s') }}</small>
            </div>
        </div>

        <div class="word-body">
            <!-- Pronunciation Section -->
            @if($word->pronunciation)
            <div class="mb-4">
                <h2 class="section-title">Pronunciation</h2>
                <div class="pronunciation-container">
                    <div><span class="language-label">BN:</span> {{ $word->pronunciation->bn_pronunciation ?? 'N/A' }}</div>
                    <div><span class="language-label">HI:</span> {{ $word->pronunciation->hi_pronunciation ?? 'N/A' }}</div>
                    <div><span class="language-label">ES:</span> {{ $word->pronunciation->es_pronunciation ?? 'N/A' }}</div>
                </div>
            </div>
            @endif

            <!-- Meanings Section -->
            <div class="mb-4">
                <h2 class="section-title">Meanings</h2>
                @forelse($word->meanings as $index => $meaning)
                    <div class="meaning-item">
                        <div class="meaning-text">{{ $index + 1 }}. {{ $meaning->meaning }}</div>
                        
                        @if($meaning->translation)
                            <div class="translation-container">
                                <div class="mb-1">Translations:</div>
                                <div><span class="language-label">BN:</span> {{ $meaning->translation->bn_meaning ?? 'N/A' }}</div>
                                <div><span class="language-label">HI:</span> {{ $meaning->translation->hi_meaning ?? 'N/A' }}</div>
                                <div><span class="language-label">ES:</span> {{ $meaning->translation->es_meaning ?? 'N/A' }}</div>
                                
                                @if($meaning->translation->transliteration)
                                    <div class="transliteration-container">
                                        <div class="mb-1">Transliterations:</div>
                                        <div><span class="language-label">BN:</span> {{ $meaning->translation->transliteration->bn_transliteration ?? 'N/A' }}</div>
                                        <div><span class="language-label">HI:</span> {{ $meaning->translation->transliteration->hi_transliteration ?? 'N/A' }}</div>
                                        <div><span class="language-label">ES:</span> {{ $meaning->translation->transliteration->es_transliteration ?? 'N/A' }}</div>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="translation-container">
                                <div>No translations available</div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="alert alert-info">No meanings available for this word.</div>
                @endforelse
            </div>

            <!-- Synonyms Section -->
            <div class="mb-4">
                <h2 class="section-title">Synonyms</h2>
                @if($synonyms->count() > 0)
                    <div class="related-words-container">
                        @foreach($synonyms as $synonym)
                            <a href="{{ route('backend::words.show', $synonym->id) }}" class="related-word-badge">
                                {{ $synonym->word }}
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info">No synonyms available for this word.</div>
                @endif
            </div>

            <!-- Antonyms Section -->
            <div class="mb-4">
                <h2 class="section-title">Antonyms</h2>
                @if($antonyms->count() > 0)
                    <div class="related-words-container">
                        @foreach($antonyms as $antonym)
                            <a href="{{ route('backend::words.show', $antonym->id) }}" class="related-word-badge">
                                {{ $antonym->word }}
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info">No antonyms available for this word.</div>
                @endif
            </div>
        </div>
    </div>
</div>
</x-ui-backend::layout>
