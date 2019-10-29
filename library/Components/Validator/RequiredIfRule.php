<?php

namespace UPTools\Components\Validator;

class RequiredIfRule extends Rule
{
    protected $equal = null;
    protected $field = null;

    protected $messageDictionary = [
        'equal' => null,
        'field' => null
    ];

    protected function check($value, array $parameters): bool
    {
        if (empty($value)){
            $this->messageDictionary['equal'] = $equal = $parameters[1] ?? null;
            $this->messageDictionary['field'] = $field = $parameters[0] ?? null;

            if (is_null($equal) or is_null($field)){
                return false;
            }

            return $equal == $this->getMidValue($field);
        }
        return true;
    }

    protected function message(): string
    {
        return '#attribute# is required, if the value of the attribute #field# is equal #equal#';
    }
}