<?php

use Livewire\Volt\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Modules\ArticleWord\Models\ArticleWordSetList;
use Modules\ArticleWord\Models\ArticleWordTranslation;
use Modules\ArticleWord\ArticleWordHelpers;

new class extends Component {
    public $articleWordSet;
    public $articleWordSetId;
    public $jsonData = '';
    public $showModal = false;
    public $processing = false;
    public $errors = [];
    public $updateSuccess = false;
    public $debugInfo = [];

    // Supported locales for translations
    protected $supportedLocales = ['bn', 'hi'];

    public function mount($articleWordSet)
    {
        $this->articleWordSet = $articleWordSet;
        $this->articleWordSetId = $articleWordSet->id;
        $this->generateJsonData();
    }

    // Generate JSON representation of the word set list items
    private function generateJsonData()
    {
        // Use the ArticleWordHelpers static method to generate JSON data
        $data = ArticleWordHelpers::generateJsonData($this->articleWordSet, $this->supportedLocales);

        // Convert the data array to JSON with pretty printing and unicode support
        $this->jsonData = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $this->debugInfo[] = 'Generated JSON data using ArticleWordHelpers';
    }

    // Open the modal
    public function openModal()
    {
        $this->showModal = true;
        $this->reset(['errors', 'updateSuccess', 'debugInfo']);
        $this->generateJsonData(); // Refresh data when opening modal
    }

    // Close the modal
    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['errors', 'updateSuccess', 'debugInfo']);
    }

    // Process JSON data and update word set list items
    public function processJson()
    {
        $this->reset(['errors', 'updateSuccess', 'debugInfo']);
        $this->processing = true;

        try {
            // Verify article_word_set_id is valid
            if (empty($this->articleWordSetId)) {
                throw new \Exception("Invalid article_word_set_id: Cannot save words without a parent set");
            }

            DB::beginTransaction();
            $this->debugInfo[] = "Started database transaction";

            // Use the ArticleWordHelpers static method to process the JSON data
            // The method now returns the ArticleWordSet instance (existing or new)
            $processedWordSet = ArticleWordHelpers::processJsonData($this->articleWordSet, $this->jsonData, $this->debugInfo);

            // Update the local articleWordSet reference if it was modified
            if ($processedWordSet && $processedWordSet->id) {
                $this->articleWordSet = $processedWordSet;
                $this->articleWordSetId = $processedWordSet->id;
                $this->debugInfo[] = "Updated local reference to ArticleWordSet ID: {$this->articleWordSetId}";
            }

            DB::commit();
            $this->debugInfo[] = "Database transaction committed successfully";
            $this->updateSuccess = true;

            // Refresh the JSON data to reflect the updated state
            $this->generateJsonData();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->debugInfo[] = "ERROR: " . $e->getMessage() . " at line " . $e->getLine();
            $this->errors[] = "Error updating word set list: " . $e->getMessage();
        }

        $this->processing = false;
    }
}; ?>

<div>
    <!-- Edit Button -->
    <button
        wire:click="openModal"
        class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 dark:hover:bg-blue-600 active:bg-blue-700 dark:active:bg-blue-800 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition"
    >
        Edit JSON
    </button>

    <!-- Modal -->
    @if($showModal)
    <div class="fixed z-10 inset-0 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
            </div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <!-- Modal Header -->
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                        Edit Word Set & Word Set List JSON
                    </h3>
                    <button
                        wire:click="closeModal"
                        class="text-gray-400 hover:text-gray-500 focus:outline-none"
                    >
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 dark:bg-gray-800">
                    <!-- Errors -->
                    @if(count($errors) > 0)
                    <div class="mb-4 p-4 bg-red-50 dark:bg-red-900 border-l-4 border-red-500 text-red-700 dark:text-red-200">
                        <h4 class="font-bold mb-2">Errors:</h4>
                        <ul class="list-disc pl-5">
                            @foreach($errors as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- Success Message -->
                    @if($updateSuccess)
                    <div class="mb-4 p-4 bg-green-50 dark:bg-green-900 border-l-4 border-green-500 text-green-700 dark:text-green-200">
                        <p>Word set list updated successfully!</p>
                    </div>
                    @endif

                    <!-- Debug Info -->
                    @if(count($debugInfo) > 0)
                    <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-900 border-l-4 border-gray-500 text-gray-700 dark:text-gray-200">
                        <h4 class="font-bold mb-2">Debug Info:</h4>
                        <ul class="list-disc pl-5">
                            @foreach($debugInfo as $info)
                            <li>{{ $info }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- Sample JSON Link -->
                    <div class="mb-4">
                        <div class="flex justify-between items-center">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">JSON Data</label>
                            <a href="/sample-data/article-word/article-word-set-list-sample.json" target="_blank" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm">
                                <span class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    View Sample JSON Format
                                </span>
                            </a>
                        </div>
                    </div>

                    <!-- JSON Editor -->
                    <div class="mb-4">
                        <div wire:ignore>
                            <x-json-editor
                                label=""
                                wire:model.live="jsonData"
                                :content="$jsonData"
                                placeholder='[{"word": "example", "translations": [...]}]'
                                model-name="jsonData"
                            />
                        </div>
                    </div>


                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button
                            wire:click.prevent="processJson"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 dark:hover:bg-blue-600 active:bg-blue-700 dark:active:bg-blue-800 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition"
                        >
                            <span wire:loading wire:target="processJson" class="mr-2">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                            Update Word Set List
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif


</div>