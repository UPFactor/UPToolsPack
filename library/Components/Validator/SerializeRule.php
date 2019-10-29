<?php

namespace UPTools\Components\Validator;

use UPTools\Str;

class SerializeRule extends Rule
{
    protected function check($value, array $parameters): bool
    {
        if (is_null($value)){
            return true;
        }

        return Str::isSerialize($value);
    }

    protected function message(): string
    {
        return 'The #attribute# attribute must contain a valid serialized string';
    }
}