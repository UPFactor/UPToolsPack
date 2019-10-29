<?php

namespace UPTools\Components\Validator;

class NotInRule extends Rule
{
    protected function check($value, array $parameters): bool
    {
        if (is_null($value)){
            return true;
        }

        return !in_array($value, $parameters);
    }

    protected function message(): string
    {
        return 'The value of the #attribute# attribute should not be equal to any of the given values #parameters#';
    }
}