<?php

namespace UPTools\Components\Validator;

class EmailRule extends Rule
{
    protected function check($value, array $parameters): bool
    {
        if (is_null($value)){
            return true;
        }

        return is_string($value) and filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function message(): string
    {
        return 'The #attribute# attribute must contain a valid Email';
    }
}