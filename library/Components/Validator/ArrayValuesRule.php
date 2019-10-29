<?php

namespace UPTools\Components\Validator;

class ArrayValuesRule extends Rule
{
    protected function check($value, array $parameters): bool
    {
        if (is_null($value)){
            return true;
        }

        if (!is_array($value) or empty($parameters)){
            return false;
        }

        foreach ($value as $item){
            if (!in_array($item, $parameters)){
                return false;
            }
        }

        return true;
    }

    protected function message(): string
    {
        return 'The values of the #attribute# attribute must be equal to one of the given values #parameters#';
    }
}