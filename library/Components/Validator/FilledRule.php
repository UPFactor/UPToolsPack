<?php

namespace UPTools\Components\Validator;

class FilledRule extends Rule
{
    protected function check($value, array $parameters): bool
    {
        if (is_null($value)){
            return true;
        }

        return !empty($value);
    }

    protected function message(): string
    {
        return '#attribute# must not be empty';
    }
}