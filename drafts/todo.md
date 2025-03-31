ui-frontend module is for frontend layout. 
```
<x-ui-frontend::layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold">{{ __('Home') }}</h1>
    </x-slot>
    Hello world from content block
</x-ui-frontend::layout>

```

now I want to add a layout for admin panel. made a modules name ui-backend
want to access using <x-ui-backend::layout></x-ui-backend::layout>


what I expect. sidebar menu toggleable. use flowbite like ui-frontend. please keep 
top of the sidebar user profile link, avatar/initials, logout
then sidebar menu item. now keep just # link. keep fixed layout. please use alpinejs, tailwindjs. only lighttheme. no need to be added darktheme in dashboard. you can mimic example image






remove all fillable property from all models and add 
`protected $guarded = []`. easy to add more field 


- setup internachi
- setup homepage 


its a laravel project. using internaci modular package. now I made a module names ui-frontend.

all layout will be like <x-ui-frontend::layout> </x-ui-frontend::layout>

what I expect is a layout file using
- header partials
- scripts partials
- nav partials
- footer partials


nav style. can be choose theme dark and light theme for tailwind. can change website language [2 type of language choice, website language and content language] - normally all website having only single language choice. use flag emoji for that. currently website language is english, bangla, hindi and espanol. 

flowbite could be an option. user avatar. when click upon avatar show dropdown menu like login, register, logout. profile dashboard

```




[
    {
        "id": 1,
        "title": "title",
        "slug": "slug",
        "display_order": 1,
        "section": "",
        "series": "",
        "is_current": true
    },
]
```







generate a onepage theme for integrating in laravel project

nav style: can be choose theme dark and light theme for tailwind. can change website language [2 type of language choice, website language and content language] - normally all website having only single language choice. use flag emoji for that. currently website language is english, bangla, hindi and espanol. 

flowbite could be an option. user avatar. when click upon avatar show dropdown menu like login, register, logout. profile dashboard

