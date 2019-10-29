<?php

namespace UPTools\Components\Validator;

class SameRule extends Rule
{
    protected $messageDictionary = [
        'field' => null
    ];

    protected function check($value, array $parameters): bool
    {
        if (is_null($value)){
            return true;
        }

        if (is_null($this->messageDictionary['field'] = $field = $parameters[0] ?? null)){
            return false;
        }

        return $value === $this->getMidValue($field);
    }

    protected function message(): string
    {
        return 'The value of the attributes #attribute# and #field# must be equal';
    }
}