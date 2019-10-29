<?php

namespace UPTools\Components\Validator;

class SizeRule extends Rule
{
    protected $messageDictionary = [
        'size' => '0'
    ];

    protected function compareSize($value = null, $operator = null, $size = null):bool
    {
        if (is_null($size) or ($size = filter_var($size, FILTER_VALIDATE_INT)) === false){
            return false;
        }

        if (is_numeric($value)){
            $a = (float) $value;
            $b = (float) $size;
        } elseif (is_string($value)){
            $a = mb_strlen($value);
            $b = $size;
        } elseif (is_array($value)){
            $a = count($value);
            $b = $size;
        } else {
            return false;
        }

        switch ($operator){
            case '=':
            case '==': return $a === $b;
            case '>=': return $a >= $b;
            case '<=': return $a <= $b;
        }

        return false;
    }

    protected function check($value, array $parameters): bool
    {
        if (is_null($value)){
            return true;
        }

        $this->messageDictionary['size'] = $size = $parameters[0] ?? null;
        return $this->compareSize($value, '=', $size);
    }

    protected function message(): string
    {
        return 'Size of the attribute #attribute# must be equal to #size#';
    }
}