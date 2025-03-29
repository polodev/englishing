<?php

namespace Modules\Utility\Libraries\Types;

use ReflectionClass;


class SentenceConnectionType
{
    const SYNONYM = 'synonym';


    static function all() {
        $reflection = new ReflectionClass(static::class);
        return $reflection->getConstants();
    }
}
