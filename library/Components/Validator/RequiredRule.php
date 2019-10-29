<?php

namespace UPTools\Components\Validator;

class RequiredRule extends Rule
{
    protected function check($value, array $parameters): bool
    {
        return !empty($value);
    }

    protected function message(): string
    {
        return '#attribute# attribute is required';
    }
}