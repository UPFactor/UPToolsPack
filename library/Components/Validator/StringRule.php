<?php

namespace UPTools\Components\Validator;

class StringRule extends Rule
{
    protected $messageDictionary = [
        'in' => null
    ];

    protected function check($value, array $parameters): bool
    {
        if (is_null($value)){
            return true;
        }

        if (!is_string($value)){
            return false;
        }

        if (empty($parameters)){
            return true;
        }

        foreach ($parameters as $k => $v){
            if (!is_string($v)){
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
        if (is_null($this->messageDictionary['in'])) {
            return 'The #attribute# attribute must contain a string';
        }

        return 'The #attribute# attribute must contain a string and must be equal to one of the given values #in#';
    }
}