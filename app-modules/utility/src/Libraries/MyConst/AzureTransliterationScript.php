<?php

/**
 * All user should be email verified
 * for protecting spam from users
 */

namespace Modules\Utility\Libraries\MyConst;
class AzureTransliterationScript
{
  const BN='Beng';
  public static function getConst($constantName) {
      return defined("self::$constantName") ? constant("self::$constantName") : null;
  }
}
