<?php

namespace UPTools\Components\Validator;

class NumericRule extends Rule
{
    protected $messageDictionary = [
        'in' => null
    ];

    protected function check($value, array $parameters): bool
    {
        if (is_null($value)){
            return true;
        }

        if (!is_numeric($value)){
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

        return in_array(filter_var($value, FILTER_VALIDATE_FLOAT), $parameters, true);
    }

    protected function message(): string
    {
        if (is_null($this->messageDictionary['in'])) {
            return 'The #attribute# attribute must contain a numeric value';
        }

        return 'The #attribute# attribute must contain a numeric value and must be equal to one of the given values #in#';
    }
}