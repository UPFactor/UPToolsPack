<?php

namespace UPTools\Components\Validator;

class BetweenRule extends SizeRule
{
    protected $messageDictionary = [
        'range' => '0'
    ];

    protected function check($value, array $parameters): bool
    {
        if (is_null($value)){
            return true;
        }

        $min = $parameters[0] ?? null;
        $max = $parameters[1] ?? null;
        $this->messageDictionary['range'] = "{$min} - {$max}";
        return $this->compareSize($value, '>=', $min) and $this->compareSize($value, '<=', $max);
    }

    protected function message(): string
    {
        return 'Size of the #attribute# attribute must match the range of values #range#';
    }
}