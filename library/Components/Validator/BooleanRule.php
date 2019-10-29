<?php

namespace UPTools\Components\Validator;

class BooleanRule extends Rule
{
    protected function check($value, array $parameters): bool
    {
        if (is_null($value)){
            return true;
        }

        return in_array($value, [true,false,1,0,'1','0'], true);
    }

    protected function message(): string
    {
        return 'The #attribute# attribute must contain a boolean value';
    }
}