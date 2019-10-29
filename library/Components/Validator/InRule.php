<?php

namespace UPTools\Components\Validator;

class InRule extends Rule
{
    protected function check($value, array $parameters): bool
    {
        if (is_null($value)){
            return true;
        }

        return in_array($value, $parameters);
    }

    protected function message(): string
    {
        return 'The value of the #attribute# attribute must be equal to one of the given values #parameters#';
    }
}