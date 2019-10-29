<?php

namespace UPTools\Components\Validator;

use UPTools\Str;

class JSONRule extends Rule
{
    protected function check($value, array $parameters): bool
    {
        if (is_null($value)){
            return true;
        }

        return Str::isJSON($value);
    }

    protected function message(): string
    {
        return 'The #attribute# attribute must contain a valid JSON string';
    }
}