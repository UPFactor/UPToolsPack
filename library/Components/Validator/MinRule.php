<?php

namespace UPTools\Components\Validator;

class MinRule extends SizeRule
{
    protected $messageDictionary = [
        'min' => '0'
    ];

    protected function check($value, array $parameters): bool
    {
        if (is_null($value)){
            return true;
        }

        $this->messageDictionary['min'] = $size = $parameters[0] ?? null;
        return $this->compareSize($value, '>=', $size);
    }

    protected function message(): string
    {
        return 'Minimum size of the #attribute# attribute must not be less than #min#';
    }
}