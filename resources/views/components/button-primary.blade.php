<button {{ $attributes->merge(['type' => 'submit', 'class' => 'py-3 px-4 inline-flex justify-center items-center gap-2 rounded-md bg-orange-500 dark:bg-zinc-500 border border-transparent font-semibold text-slate-100 hover:text-white hover:bg-orange-600 dark:hover:bg-zinc-600 focus:outline-none focus:ring-2 ring-offset-white focus:ring-orange-500 focus:ring-offset-2 transition-all text-sm dark:focus:ring-offset-gray-800']) }}> 
    {{ $slot }}
</button>
