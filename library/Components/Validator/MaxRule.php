<?php

namespace UPTools\Components\Validator;

class MaxRule extends SizeRule
{
    protected $messageDictionary = [
        'max' => '0'
    ];

    protected function check($value, array $parameters): bool
    {
        if (is_null($value)){
            return true;
        }

        $this->messageDictionary['max'] = $size = $parameters[0] ?? null;
        return $this->compareSize($value, '<=', $size);
    }

    protected function message(): string
    {
        return 'Maximum size of the #attribute# attribute should not be more than #max#';
    }
}