<?php

namespace Modules\Utility\Libraries\Types;

use ReflectionClass;


class ExpressionType
{
    const IDIOMS = 'idioms';
    const PROVERBS = 'proverbs';
    const PHRASES = 'phrases';
    const CONTRACTION = 'contraction';
    const PHRASAL_VERB = 'phrasal_verb';
    const COLLOCATION = 'collocation';


    static function all() {
        $reflection = new ReflectionClass(static::class);
        return $reflection->getConstants();
    }
}

