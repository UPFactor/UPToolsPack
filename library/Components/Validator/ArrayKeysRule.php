<?php

namespace UPTools\Components\Validator;

/**
 * Class ArrayKeysRule
 *
 * @package UPTools\Components\Validator
 */
class ArrayKeysRule extends Rule
{
    protected function check($value, array $parameters): bool
    {
        if (is_null($value)){
            return true;
        }

        if (!is_array($value) or empty($parameters)){
            return false;
        }

        foreach (array_keys($value) as $item){
            if (!in_array($item, $parameters)){
                return false;
            }
        }

        return true;
    }

    protected function message(): string
    {
        return 'The keys of the #attribute# attribute must be equal to one of the given values #parameters#';
    }
}