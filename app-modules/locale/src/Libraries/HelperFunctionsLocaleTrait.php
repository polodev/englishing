<?php

namespace Modules\Locale\Libraries;

use Illuminate\Support\Facades\URL;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Helpers;


trait HelperFunctionsLocaleTrait
{
  static function getUserLocales()
  {
    $locales = [
      app()->getLocale(),
      // auth()?->user()?->locale,
      // auth()?->user()?->target_locale,
      'en'
    ];

    $locales = array_filter($locales, 'strlen');

    return array_unique($locales);
  }

  static function localeRoute($route_name, $route_params=[], $locale=null)
  {
    if (! $locale) {
      $locale = app()->getLocale();
    }
    $hrefUrl = route($route_name, $route_params);
    return LaravelLocalization::localizeURL($hrefUrl, $locale);
  }
  static function localeUrlCurrent($locale)
  {
    if (! $locale) {
      $locale = app()->getLocale();
    }
    $url = URL::full(); // Get the current URL
    return LaravelLocalization::localizeURL($url, $locale);
  }
  static function changeLocaleInUrl($newLocale)
  {
      $url = URL::current(); // Get the current URL
      $segments = explode('/', $url); // Split the URL into segments

      $mainLocale = 'en'; // Your main/fallback locale

      // Check if the URL already contains the main locale
      if (count($segments) > 3 && $segments[3] === $mainLocale) {
          $segments[3] = $newLocale; // Change the language code
          $url = implode('/', $segments); // Reconstruct the URL
      } else {
          // Add the new locale to the URL
          array_splice($segments, 3, 0, $newLocale);
          $url = implode('/', $segments); // Reconstruct the URL
      }

      return $url;
  }
  static function getAvailableLocales()
  {
    $availableLocales = ['en' , 'bn', 'es' , 'hi' ];
    // $availableLocales = ['en', 'bn',];
    return $availableLocales;
  }
  static function getAvailableLocaleUrlsWithFlag()
  {
    $availableLocalesUrl = [];
    foreach (Helpers::getAvailableLocales() as $locale) {
      $availableLocalesUrl[] = [
        'locale' => $locale,
        'title' => Helpers::getTitleOfLocale($locale),
        'href' =>  Helpers::localeUrlCurrent(locale: $locale) ,
      ];
    }
    return $availableLocalesUrl;;
  }

  static function getTitleOfLocale($locale)
  {
    if (array_key_exists($locale, Helpers::getAllLocales())) {
      return Helpers::getAllLocales()[$locale]['title'];
    }
    return $locale;
  }
  static function getTitleOfLocaleWithSuffix($locale, $suffix)
  {
    return Helpers::getTitleOfLocale($locale) . " " . $suffix;
  }



  static function filterRespectiveLanguage($locale='en', $text='')
  {
    if (!$text) {
      return '';
    }
    if ($locale == app()->getLocale()) {
      return '';
    }
    $text = trim($text);
    return  " ($text)";
  }
  static function getAllLocales()
  {
    // <https://emojipedia.org/flags>
    return [
      'en' => [
        'title' => '🇺🇸 ' .  __('locales.english'). Helpers::filterRespectiveLanguage('en',  "English"),
      ],
      'bn' => [
        'title' => '🇧🇩 ' .  __('locales.bangla') . Helpers::filterRespectiveLanguage('bn',  "বাংলা"),
      ],
      'hi' => [
        'title' => '🇮🇳 ' .  __('locales.hindi') . Helpers::filterRespectiveLanguage('hi',  "हिन्दी"),
      ],
      'es' => [
        'title' => '🇪🇸 ' .  __('locales.spanish') . Helpers::filterRespectiveLanguage('es',  "Español"),
      ],
     
    ];
  }

  static function getAllLocalesInRespectiveLanguage2()
  {
    return [
      'english' => 'English',
      'bangla' => 'বাংলা',
      'spanish' => 'Español',
      'bengali' => 'বাংলা',
      'hindi' => 'हिन्दी',

    ];
  }




  // following line end class
}
