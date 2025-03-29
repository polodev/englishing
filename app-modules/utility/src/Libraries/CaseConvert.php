<?php
namespace Modules\Utility\Libraries;

class CaseConvert {

  public $text = '';
  public $parts = [];
  public $output = '';
  public function __construct($text)
  {
    $this->text = $text;
    $this->generate_parts();
  }
  public function remove_punctuation($text)
  {
    return preg_replace('/\p{P}/', '', $text);
  }
  public function add_hypen_to_camel_case() {
    $pattern = '/(?<=\d)(?=[A-Za-z])|(?<=[A-Za-z])(?=\d)|(?<=[a-z])(?=[A-Z])/';
    $replace = "_";
    $this->text = preg_replace($pattern, $replace, $this->text);
  }
  public function generate_parts()
  {
    $this->add_hypen_to_camel_case();
    $parts = preg_split('/[-\s:.-]/', $this->text);
    $this->parts = array_map(function ($each)  {
      return $this->remove_punctuation($each);
    }, $parts);
  }
  public function output()
  {
    return $this->output;
  }

  public function title_case()
  {
    $title_case_parts = array_map(function ($each) {
      return ucfirst( strtolower( $each ) );
    }, $this->parts);
    $this->output = implode(' ', $title_case_parts);
    return $this;
  }
  public function upper_case()
  {
    $parts_text = implode(' ', $this->parts);
    $this->output = strtoupper($parts_text);
    return $this;
  }
  public function lower_case()
  {
    $parts_text = implode(' ', $this->parts);
    $this->output = strtolower($parts_text);
    return $this;
  }
  public function swap_case()
  {
    return $this;
  }
  public function snake_case()
  {
    $snake_case = implode('_', $this->parts);
    $this->output = strtolower( $snake_case );
    return $this;
  }
  public function screaming_snake_case()
  {
    $this->snake_case();
    $this->output = strtoupper($this->output);
    return $this;
  }
  public function camel_case()
  {
    $case_parts = array_map(function ($each) {
      return ucfirst( strtolower( $each ) );
    }, $this->parts);
    $this->output = lcfirst( implode('', $case_parts) );
    return $this;
  }
  public function pascal_case()
  {
    $case_parts = array_map(function ($each) {
      return ucfirst( strtolower( $each ) );
    }, $this->parts);
    $this->output = implode('', $case_parts);
    return $this;
  }
  public function separate_words($glue)
  {
    $parts = array_map(function ($each) {
      return strtolower( $each );
    }, $this->parts);
    $this->output = implode($glue, $parts);
    return $this;
  }
  public function dot_case()
  {
    $this->separate_words('.');
    return $this;
  }
  public function dash_case()
  {
    $this->separate_words('-');
    return $this;
  }
  public function separate_with_forward_slashes()
  {
    $this->separate_words('/');
    return $this;
  }
  public function separate_with_back_slashes()
  {
    $this->separate_words('\\');
    return $this;
  }
  /**
   * all_static
   */
  static function remove_punctuation_static($text)
  {
    return preg_replace('/\p{P}/', '', $text);
  }
  static function add_hypen_to_camel_case_static($text) {
    $pattern = '/(?<=\d)(?=[A-Za-z])|(?<=[A-Za-z])(?=\d)|(?<=[a-z])(?=[A-Z])/';
    $replace = "_";
    return preg_replace($pattern, $replace, $text);
  }
  static function generate_parts_static($text)
  {
    $hypen_text = self::add_hypen_to_camel_case_static($text);
    return preg_split('/[\s.,_-]/', $hypen_text);
  }

  static function to_title_case($text)
  {
    $parts = self::generate_parts_static($text);
    $title_case_parts = array_map(function ($each) {
      return ucfirst( strtolower( $each ) );
    }, $parts);
    return implode(' ', $title_case_parts);
  }
  static function to_lower_case($text)
  {
    $parts = self::generate_parts_static($text);
    $parts_text = implode(' ', $parts);
    return strtolower($parts_text);
  }
  static function to_upper_case($text)
  {
    $parts = self::generate_parts_static($text);
    $parts_text = implode(' ', $parts);
    return strtoupper($parts_text);
  }
  static function to_snake_case($text)
  {
    $parts = self::generate_parts_static($text);
    $snake_case = implode('_', $parts);
    return strtolower( $snake_case );
  }
  static function to_screaming_snake_case($text)
  {
    $parts = self::generate_parts_static($text);
    $snake_case = implode('_', $parts);
    return strtoupper($snake_case);
  }
  static function to_camel_case($text)
  {
    $parts = self::generate_parts_static($text);
    $case_parts = array_map(function ($each) {
      return ucfirst( strtolower( $each ) );
    }, $parts);
    return lcfirst( implode('', $case_parts) );
  }
  static function to_pascal_case($text)
  {
    $parts = self::generate_parts_static($text);
    $case_parts = array_map(function ($each) {
      return ucfirst( strtolower( $each ) );
    }, $parts);
    return implode('', $case_parts);
  }
  static function to_separate_words($text, $glue)
  {
    $parts = self::generate_parts_static($text);
    $separate_parts = array_map(function ($each) {
      return $each;
    }, $parts);
    return implode($glue, $separate_parts);
  }
  static function to_separate_words_lower_case($text, $glue)
  {
    $parts = self::generate_parts_static($text);
    $separate_parts = array_map(function ($each) {
      return strtolower( $each );
    }, $parts);
    return implode($glue, $separate_parts);
  }
  static function to_dot_case($text)
  {
    return self::to_separate_words_lower_case($text, '.');
  }
  static function to_dash_case($text)
  {
    return self::to_separate_words_lower_case($text, '-');
  }
  static function to_separate_with_forward_slashes($text)
  {
    return self::to_separate_words_lower_case($text, '/');
  }
  static function to_separate_with_back_slashes($text)
  {
    return self::to_separate_words_lower_case($text, '\\');
  }


}
