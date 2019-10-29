<?php

namespace UPTools\Components\Validator;

class FloatRule extends Rule
{
    protected $messageDictionary = [
        'in' => null
    ];

    protected function check($value, array $parameters): bool
    {
        if (is_null($value)){
            return true;
        }

        if (($value = filter_var($value, FILTER_VALIDATE_FLOAT)) === false){
            return false;
        }

        if (empty($parameters)){
            return true;
        }

        foreach ($parameters as $k => $v){
            if (($v = filter_var($v, FILTER_VALIDATE_FLOAT)) === false){
                unset($parameters[$k]);
                continue;
            }
            $parameters[$k] = $v;
        }

        $this->messageDictionary['in'] = $parameters;

        return in_array($value, $parameters, true);
    }

    protected function message(): string
    {
        if (is_null($this->messageDictionary['in'])){
            return 'The #attribute# attribute must contain a fractional number';
        }

        return 'The #attribute# attribute must contain a fractional number and must be equal to one of the given values #in#';
    }
}