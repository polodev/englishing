# article word list ArticleWordSetList
```
Schema::create('article_word_set_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_word_set_id');
            $table->integer('display_order')->default(0);
            $table->string('word');
            $table->string('slug');
            $table->string('phonetic')->nullable();
            $table->json('pronunciation')->nullable();
            $table->string('parts_of_speech')->nullable();
            $table->text('static_content_1')->nullable();
            $table->text('static_content_2')->nullable();
            $table->text('meaning')->nullable();
            $table->text('example_sentence')->nullable();
            $table->text('example_expression')->nullable();
            $table->text('example_expression_meaning')->nullable();
            $table->timestamps();
            
            $table->unique(['article_word_set_id', 'slug']);
        });
```
# article word translation ArticleWordTranslation
```
Schema::create('article_word_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_word_set_list_id');
            $table->text('word_translation');
            $table->text('word_transliteration')->nullable();
            $table->text('example_sentence_translation')->nullable();
            $table->text('example_sentence_transliteration')->nullable();
            $table->text('example_expression_translation')->nullable();
            $table->text('example_expression_transliteration')->nullable();
            $table->string('locale', 10);
            $table->string('source')->nullable();
            $table->timestamps();

            $table->unique(['article_word_set_list_id', 'locale']);
        });
```

smaple json file 
```
[
  {
    "word": "happiness",
    "phonetic": "ˈhæpinəs",
    "pronunciation": {
      "bn_pronunciation": "হ্যাপিনেস",
      "hi_pronunciation": "हैप्पीनेस"
    },
    "parts_of_speech": "noun",
    "static_content_1": "Happiness is a state of well-being characterized by emotions ranging from contentment to intense joy.",
    "static_content_2": "Happiness is often associated with positive psychology and mental health.",
    "meaning": "The state of being happy, feeling or showing pleasure or contentment.",
    "example_sentence": "She couldn't hide her happiness when she received the award.",
    "example_expression": "pursuit of happiness",
    "example_expression_meaning": "The ongoing effort to find satisfaction, enjoyment, and meaning in life.",
    "display_order": 1,
    "translations": [
      {
        "locale": "bn",
        "word_translation": "সুখ",
        "word_transliteration": "shukh",
        "example_sentence_translation": "তিনি পুরস্কার পাওয়ার সময় তার সুখ লুকাতে পারেননি।",
        "example_sentence_transliteration": "Tini puroshkar paowar somoy tar shukh lukate parenni.",
        "example_expression_translation": "সুখের অনুসরণ",
        "example_expression_transliteration": "shukher onushoron",
        "source": "oxford"
      },
      {
        "locale": "hi",
        "word_translation": "ख़ुशी",
        "word_transliteration": "khushi",
        "example_sentence_translation": "जब उन्हें पुरस्कार मिला तो वह अपनी ख़ुशी नहीं छिपा सकीं।",
        "example_sentence_transliteration": "Jab unhein puraskaar mila to wah apni khushi nahin chhipa sakeen.",
        "example_expression_translation": "ख़ुशी की खोज",
        "example_expression_transliteration": "khushi ki khoj",
        "source": "oxford"
      }
    ]
  },
  {
    "word": "freedom",
    "phonetic": "ˈfriːdəm",
    "pronunciation": {
      "bn_pronunciation": "ফ্রিডম",
      "hi_pronunciation": "फ्रीडम"
    },
    "parts_of_speech": "noun",
    "static_content_1": "Freedom is the power or right to act, speak, or think as one wants without hindrance or restraint.",
    "static_content_2": "Freedom is a fundamental human right and is the foundation of democratic societies.",
    "meaning": "The state of being free or at liberty rather than in confinement or under physical restraint.",
    "example_sentence": "The prisoners were granted their freedom after serving their sentences.",
    "example_expression": "freedom of speech",
    "example_expression_meaning": "The right to express any opinions without censorship or restraint.",
    "display_order": 2,
    "translations": [
      {
        "locale": "bn",
        "word_translation": "স্বাধীনতা",
        "word_transliteration": "shadhinota",
        "example_sentence_translation": "বন্দীরা তাদের বাক্য পরিবেশন করার পরে তাদের স্বাধীনতা পেয়েছিল।",
        "example_sentence_transliteration": "Bondira tader bakyo poribeshon korar pore tader shadhinota peyechhilo.",
        "example_expression_translation": "বাকস্বাধীনতা",
        "example_expression_transliteration": "bakshadhinota",
        "source": "oxford"
      },
      {
        "locale": "hi",
        "word_translation": "आज़ादी",
        "word_transliteration": "aazadi",
        "example_sentence_translation": "कैदियों को उनकी सजा पूरी करने के बाद आजादी दी गई थी।",
        "example_sentence_transliteration": "Kaidiyon ko unki saja poori karne ke baad aazadi di gayi thi.",
        "example_expression_translation": "बोलने की आजादी",
        "example_expression_transliteration": "bolne ki aazadi",
        "source": "oxford"
      }
    ]
  }
  
]
```


analyze app-modules/expression/resources/views/livewire/expression--expression-edit-using-json.blade.php. 

and complete the app-modules/article-word/resources/views/livewire/article-word--article-word-set-list-edit-using-json.blade.php


what to expect from sample json style json will be placed in json editor. upon clicking process button it will insert data to ArticleWordSetList and ArticleWordTranslation

article-word-set-list-edit-using-json will take article_word_set_id as parameter. initially it will load json data from ArticleWordSetList and ArticleWordTranslation in our sample form. 
if there is not data available in ArticleWordSetList and ArticleWordTranslation it will load stub data which will be easy to fill up. stub data like 

```
[
  {
    "word": "",
    "phonetic": "",
    "pronunciation": {
      "bn_pronunciation": "",
      "hi_pronunciation": ""
    },
    "parts_of_speech": "",
    "static_content_1": "",
    "static_content_2": "",
    "meaning": "",
    "example_sentence": "",
    "example_expression": "",
    "example_expression_meaning": "",
    "display_order": 1,
    "translations": [
      {
        "locale": "bn",
        "word_translation": "",
        "word_transliteration": "",
        "example_sentence_translation": "",
        "example_sentence_transliteration": "",
        "example_expression_translation": "",
        "example_expression_transliteration": "",
        "source": "oxford"
      },
      {
        "locale": "hi",
        "word_translation": "",
        "word_transliteration": "",
        "example_sentence_translation": "",
        "example_sentence_transliteration": "",
        "example_expression_translation": "",
        "example_expression_transliteration": "",
        "source": "oxford"
      }
    ]
  }
  
  
]
```

suppose in existing data if bn translation have data it will show empty hi translation. its easier for us to fill up data.


if empty data like word found it will create if word found it will update, if no word found just ignore. for searching and slug use Str::slug(word). create or update eloquent model 


modal will be like expression edit modal. please consider dark and light theme. 
