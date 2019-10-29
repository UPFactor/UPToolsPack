<?php

namespace UPTools\Components\Validator;

use Exception;
use UPTools\Arr;
use UPTools\Exceptions\ValidatorException;
use UPTools\Str;
use UPTools\Validator;

/**
 * Class Rule
 *
 * @property-read bool $fails
 * @property-read string|null $message
 * @property-read Validator|null $validator
 * @property-read array $parameters
 * @property-read string|null $attribute
 * @property-read mixed|null $value
 *
 * @package UPTools\Components\Validator
 */
abstract class Rule
{
    /**
     * @var bool
     */
    protected $fails = false;

    /**
     * @var string|null
     */
    protected $message = null;

    /**
     * @var array
     */
    protected $messageDictionary = [];

    /**
     * @var Validator|null
     */
    protected $validator = null;

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @var string|null
     */
    protected $attribute = null;

    /**
     * @var mixed|null
     */
    protected $value = null;

    /**
     * Rule constructor.
     *
     * @param Validator $validator
     * @param mixed ...$parameters
     * @return static
     */
    public static function make(Validator $validator, ...$parameters){
        return new static($validator, ...$parameters);
    }

    /**
     * Rule constructor.
     *
     * @param Validator $validator
     * @param mixed ...$parameters
     */
    public function __construct(Validator $validator, ...$parameters)
    {
        $this->validator = $validator;
        $this->parameters = $parameters;
    }

    /**
     * Overload object properties.
     *
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        try {
            return $this->{$name};
        } catch (Exception $error) {
            throw new ValidatorException("Property [{$name}] does not exist on [".static::class."] instance.");
        }
    }

    /**
     * Checks the value for a given attribute
     * against the current rule.
     *
     * @param string $attribute
     * @return $this
     */
    public function passes(string $attribute)
    {
        $this->value = Arr::get($this->validator->data, $attribute);
        $this->attribute = $attribute;
        $this->fails = !$this->check($this->value, $this->parameters);
        $this->message = $this->fails ? $this->getMessage() : null;

        return $this;
    }

    /**
     * Returns the object to its original state.
     *
     * @return $this
     */
    public function reset()
    {
        $this->value = null;
        $this->attribute = null;
        $this->fails = false;
        $this->message = null;

        return $this;
    }

    /**
     * Generate a message that the attribute value
     * does not match the current rule.
     *
     * @return string
     */
    protected function getMessage(): string
    {
        $message = $this->message();
        $dictionary = array_merge(
            $this->messageDictionary,
            ['attribute' => $this->attribute, 'parameters' => $this->parameters]
        );

        foreach ($dictionary as $token => $word){
            if (is_array($word)) $word = implode(', ', $word);
            $message = str_replace("#{$token}#", "[{$word}]", $message);
        }

        return preg_replace('/#.[a-z0-9_]+#/', '', $message);
    }

    /**
     * Returns an intermediate value for the attribute.
     *
     * @param $attribute
     * @return mixed|null
     */
    protected function getMidValue($attribute)
    {
        if (is_null($this->validator)){
            return null;
        }

        if (!is_string($attribute)){
            return null;
        }

        $attribute = Str::replaceSequence('*', $this->validator->midKeys, $attribute);

        if (strpos($attribute, '*') !== false){
            return null;
        }

        return Arr::get($this->validator->data, $attribute);
    }

    /**
     * Implements logic of the current rule.
     *
     * @param $value
     * @param array $parameters
     * @return bool
     */
    abstract protected function check($value, array $parameters): bool;

    /**
     * Returns a message template stating that the attribute
     * value does not match the current rule.
     *
     * @return string
     */
    abstract protected function message(): string;
}