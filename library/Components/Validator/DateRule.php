<?php

namespace UPTools\Components\Validator;

class DateRule extends Rule
{
    protected $messageDictionary = [
        'equal' => null
    ];

    protected function check($value, array $parameters): bool
    {
        if (is_null($value)){
            return true;
        }

        if (($value = strtotime($value)) === false){
            return false;
        }

        if (is_null($this->messageDictionary['equal'] = $equal = $parameters[0] ?? null)){
            return true;
        }

        return $value === strtotime($equal);
    }

    protected function message(): string
    {
        if (is_null($this->messageDictionary['equal'])){
            return 'The #attribute# attribute must contain an valid timestamp';
        }

        return 'The #attribute# attribute must contain an valid timestamp equal to #equal#';
    }
}