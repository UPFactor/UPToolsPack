<?php

namespace UPTools\Components\Validator;

class NotRegexRule extends Rule
{
    protected function check($value, array $parameters): bool
    {
        if (is_null($value)){
            return true;
        }

        if (!is_string($regex = $parameters[0] ?? null)){
            return false;
        }

        return !((bool) preg_match($regex, $value));
    }

    protected function message(): string
    {
        return 'The value of the #attribute# attribute must not match the given regex #parameters#';
    }
}