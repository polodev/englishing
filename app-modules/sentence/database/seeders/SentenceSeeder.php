<?php

namespace Modules\Sentence\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Sentence\Models\Sentence;
use Modules\Sentence\Models\SentencePronunciation;
use Modules\Sentence\Models\SentenceTranslation;
use Modules\Sentence\Models\SentenceTransliteration;

class SentenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample sentences
        $sentences = [
            [
                'sentence' => 'The quick brown fox jumps over the lazy dog.',
                'slug' => 'quick-brown-fox',
                'source' => 'English pangram',
                'pronunciation' => [
                    'bn_pronunciation' => 'দ্য কুইক ব্রাউন ফক্স জাম্পস ওভার দ্য লেজি ডগ',
                    'hi_pronunciation' => 'द क्विक ब्राउन फॉक्स जम्प्स ओवर द लेज़ी डॉग',
                    'es_pronunciation' => 'da kuik braun fox yamps over da leizi dog',
                ],
                'translation' => [
                    'bn_sentence' => 'দ্রুত বাদামী শিয়াল অলস কুকুরের উপর দিয়ে লাফিয়ে যায়।',
                    'hi_sentence' => 'तेज भूरी लोमड़ी आलसी कुत्ते के ऊपर से कूदती है।',
                    'es_sentence' => 'El rápido zorro marrón salta sobre el perro perezoso.',
                    'transliteration' => [
                        'bn_transliteration' => 'Druto badami shiyal olosh kukurer upor diye lafiye jay.',
                        'hi_transliteration' => 'Tej bhoori lomdi aalsi kutte ke upar se koodti hai.',
                        'es_transliteration' => 'El rapido zorro marron salta sobre el perro perezoso.',
                    ],
                ],
            ],
            [
                'sentence' => 'How are you today?',
                'slug' => 'how-are-you-today',
                'source' => 'Common greeting',
                'pronunciation' => [
                    'bn_pronunciation' => 'হাউ আর ইউ টুডে',
                    'hi_pronunciation' => 'हाउ आर यू टुडे',
                    'es_pronunciation' => 'jau ar yu tudei',
                ],
                'translation' => [
                    'bn_sentence' => 'আজ আপনি কেমন আছেন?',
                    'hi_sentence' => 'आज आप कैसे हैं?',
                    'es_sentence' => '¿Cómo estás hoy?',
                    'transliteration' => [
                        'bn_transliteration' => 'Aaj apni kemon achen?',
                        'hi_transliteration' => 'Aaj aap kaise hain?',
                        'es_transliteration' => 'Como estas hoy?',
                    ],
                ],
            ],
            [
                'sentence' => 'I love learning new languages.',
                'slug' => 'love-learning-languages',
                'source' => 'Language learning',
                'pronunciation' => [
                    'bn_pronunciation' => 'আই লাভ লার্নিং নিউ ল্যাঙ্গুয়েজেস',
                    'hi_pronunciation' => 'आई लव लर्निंग न्यू लैंग्वेजेज',
                    'es_pronunciation' => 'ai lov lerning niu languayes',
                ],
                'translation' => [
                    'bn_sentence' => 'আমি নতুন ভাষা শেখা পছন্দ করি।',
                    'hi_sentence' => 'मुझे नई भाषाएँ सीखना पसंद है।',
                    'es_sentence' => 'Me encanta aprender nuevos idiomas.',
                    'transliteration' => [
                        'bn_transliteration' => 'Ami notun bhasha shekha pochondo kori.',
                        'hi_transliteration' => 'Mujhe nayi bhashayen seekhna pasand hai.',
                        'es_transliteration' => 'Me encanta aprender nuevos idiomas.',
                    ],
                ],
            ],
        ];

        // Create sentences with their relationships
        foreach ($sentences as $sentenceData) {
            $sentence = Sentence::create([
                'sentence' => $sentenceData['sentence'],
                'slug' => $sentenceData['slug'],
                'source' => $sentenceData['source'],
            ]);

            // Create pronunciation (only one per sentence)
            if (!empty($sentenceData['pronunciation'])) {
                SentencePronunciation::create([
                    'sentence_id' => $sentence->id,
                    'bn_pronunciation' => $sentenceData['pronunciation']['bn_pronunciation'],
                    'hi_pronunciation' => $sentenceData['pronunciation']['hi_pronunciation'],
                    'es_pronunciation' => $sentenceData['pronunciation']['es_pronunciation'],
                ]);
            }

            // Create translation (only one per sentence)
            if (!empty($sentenceData['translation'])) {
                $translation = SentenceTranslation::create([
                    'sentence_id' => $sentence->id,
                    'bn_sentence' => $sentenceData['translation']['bn_sentence'],
                    'hi_sentence' => $sentenceData['translation']['hi_sentence'],
                    'es_sentence' => $sentenceData['translation']['es_sentence'],
                ]);

                // Create transliteration (only one per translation)
                if (!empty($sentenceData['translation']['transliteration'])) {
                    SentenceTransliteration::create([
                        'sentence_translation_id' => $translation->id,
                        'bn_transliteration' => $sentenceData['translation']['transliteration']['bn_transliteration'],
                        'hi_transliteration' => $sentenceData['translation']['transliteration']['hi_transliteration'],
                        'es_transliteration' => $sentenceData['translation']['transliteration']['es_transliteration'],
                    ]);
                }
            }
        }
    }
}
