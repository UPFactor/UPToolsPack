<?php

namespace UPTools\Components\Validator;

class ArrayRule extends Rule
{
    protected function check($value, array $parameters): bool
    {
        if (is_null($value)){
            return true;
        }

        return is_array($value);
    }

    protected function message(): string
    {
        return 'The #attribute# attribute must contain an array';
    }
}