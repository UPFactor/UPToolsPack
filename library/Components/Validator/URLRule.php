<?php

namespace UPTools\Components\Validator;

class URLRule extends Rule
{
    protected function check($value, array $parameters): bool
    {
        if (is_null($value)){
            return true;
        }

        return is_string($value) and filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    protected function message(): string
    {
        return 'The #attribute# attribute must contain a valid URL';
    }
}