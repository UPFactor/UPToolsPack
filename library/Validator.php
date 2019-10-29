<?php

namespace UPTools;

use Exception;
use UPTools\Components\Validator\ArrayKeysRule;
use UPTools\Components\Validator\ArrayOfRule;
use UPTools\Components\Validator\ArrayRule;
use UPTools\Components\Validator\ArrayValuesRule;
use UPTools\Components\Validator\BetweenRule;
use UPTools\Components\Validator\BooleanRule;
use UPTools\Components\Validator\CallableRule;
use UPTools\Components\Validator\DateFormatRule;
use UPTools\Components\Validator\DateRule;
use UPTools\Components\Validator\DifferentRule;
use UPTools\Components\Validator\EmailRule;
use UPTools\Components\Validator\FilledRule;
use UPTools\Components\Validator\FloatRule;
use UPTools\Components\Validator\InRule;
use UPTools\Components\Validator\IntegerRule;
use UPTools\Components\Validator\IPRule;
use UPTools\Components\Validator\JSONRule;
use UPTools\Components\Validator\MaxRule;
use UPTools\Components\Validator\MinRule;
use UPTools\Components\Validator\NotInRule;
use UPTools\Components\Validator\NotRegexRule;
use UPTools\Components\Validator\NumericRule;
use UPTools\Components\Validator\RegexRule;
use UPTools\Components\Validator\RequiredIfRule;
use UPTools\Components\Validator\RequiredRule;
use UPTools\Components\Validator\RequiredWithAllRule;
use UPTools\Components\Validator\RequiredWithoutAllRule;
use UPTools\Components\Validator\RequiredWithoutRule;
use UPTools\Components\Validator\RequiredWithRule;
use UPTools\Components\Validator\Rule;
use UPTools\Components\Validator\SameRule;
use UPTools\Components\Validator\SerializeRule;
use UPTools\Components\Validator\SizeRule;
use UPTools\Components\Validator\StringRule;
use UPTools\Components\Validator\URLRule;
use UPTools\Exceptions\ValidatorException;

/**
 * Class Validator
 *
 * @property-read array $data
 * @property-read array $midKeys
 * @property-read array $rules
 * @property-read array $failed
 * @property-read array $fails
 *
 * @package UPTools
 */
class Validator
{
    protected static $handlers = [
        'array' => ArrayRule::class,
        'array_of' => ArrayOfRule::class,
        'array_keys' => ArrayKeysRule::class,
        'array_values' => ArrayValuesRule::class,
        'boolean' => BooleanRule::class,
        'string' => StringRule::class,
        'integer' => IntegerRule::class,
        'float' => FloatRule::class,
        'numeric' => NumericRule::class,
        'url' => URLRule::class,
        'ip' => IPRule::class,
        'email' => EmailRule::class,
        'in' => InRule::class,
        'not_in' => NotInRule::class,
        'size' => SizeRule::class,
        'max' => MaxRule::class,
        'min' => MinRule::class,
        'between' => BetweenRule::class,
        'date' => DateRule::class,
        'date_format' => DateFormatRule::class,
        'regex' => RegexRule::class,
        'not_regex' => NotRegexRule::class,
        'json' => JSONRule::class,
        'serialize' => SerializeRule::class,
        'filled' => FilledRule::class,
        'required' => RequiredRule::class,
        'required_with' => RequiredWithRule::class,
        'required_with_all' => RequiredWithAllRule::class,
        'required_without' => RequiredWithoutRule::class,
        'required_without_all' => RequiredWithoutAllRule::class,
        'required_if' => RequiredIfRule::class,
        'same' => SameRule::class,
        'different' => DifferentRule::class
    ];

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $midKeys = [];

    /**
     * @var array
     */
    protected $rules = [];

    /**
     * @var bool
     */
    protected $fails = false;

    /**
     * @var array
     */
    protected $failed = [];

    /**
     * @var ArrayRule|null
     */
    private $arrayRule = null;

    /**
     * Validator constructor.
     *
     * @param array $data
     * @param array $rules
     * @return static
     */
    public static function make(array $data, array $rules)
    {
        return new static($data, $rules);
    }

    /**
     * Add new rule.
     *
     * @param string $ruleName
     * @param string $ruleClass
     */
    public static function setRule(string $ruleName, string $ruleClass)
    {
        static::$handlers[$ruleName] = $ruleClass;
    }

    /**
     * Delete rule by name.
     *
     * @param string $ruleName
     */
    public static function unsetRule(string $ruleName)
    {
        if (array_key_exists($ruleName, static::$handlers)){
            unset(static::$handlers[$ruleName]);
        }
    }

    /**
     * Validator constructor.
     *
     * @param array $data
     * @param array $rules
     */
    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $this->ruleNormalization($rules);
        $this->arrayRule = new ArrayRule($this);

        foreach ($this->rules as $attribute => $rules){
            $this->checkRules($attribute, $rules);
        }
    }

    /**
     * Overload object properties.
     *
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if ($name === 'arrayRule'){
            throw new ValidatorException("Property [{$name}] does not exist on [".static::class."] instance.");
        }

        try {
            return $this->{$name};
        } catch (Exception $error) {
            throw new ValidatorException("Property [{$name}] does not exist on [".static::class."] instance.");
        }
    }

    /**
     * Converting an array with rules to a standard view.
     *
     * @param array $rules
     * @return array
     */
    protected function ruleNormalization(array $rules): array
    {
        $normalized = [];

        foreach ($rules as $attribute => $ruleSet){
            if (is_string($ruleSet)) {
                $ruleSet = explode('|', $ruleSet);
            } elseif (is_callable($ruleSet)) {
                $normalized[$attribute][] = new CallableRule($this, $ruleSet);
                continue;
            } elseif (!is_array($ruleSet)) {
                continue;
            }

            foreach ($ruleSet as $ruleItem){
                if (is_string($ruleItem)){
                    $ruleItem = explode(':', $ruleItem, 2);

                    if (!array_key_exists($ruleItem[0], static::$handlers)){
                        throw new ValidatorException("Validation rule [{$ruleItem[0]}] does not exist.");
                    }

                    if (count($ruleItem) > 1){
                        $normalized[$attribute][] = new static::$handlers[$ruleItem[0]]($this, ...explode(',', $ruleItem[1]));
                    } else {
                        $normalized[$attribute][] = new static::$handlers[$ruleItem[0]]($this);
                    }
                    continue;
                }

                if (is_callable($ruleItem)){
                    $normalized[$attribute][] = new CallableRule($this, $ruleItem);
                    continue;
                }
            }
        }

        return $normalized;
    }

    /**
     * Validating array values against a given list of rules.
     *
     * @param string $attribute
     * @param array $rules
     * @throws ValidatorException
     */
    protected function checkRules(string $attribute, array $rules): void
    {
        $attribute = explode('.*', $attribute, 2);

        if (count($attribute) > 1){
            $leftKey = $attribute[0];
            $rightKey = $attribute[1];

            if ($this->arrayRule->passes($leftKey)->fails){
                $this->fails = true;
                $this->failed[$leftKey] = $this->arrayRule->message;
                return;
            }

            if (is_null($midArray = $this->arrayRule->value)){
                return;
            }

            $this->arrayRule->reset();

            foreach (array_keys($midArray) as $midKey){
                array_push($this->midKeys, $midKey);
                $this->checkRules($leftKey . '.' . $midKey . $rightKey, $rules);
                array_pop($this->midKeys);
            }

            return;
        }

        $attribute = $attribute[0];

        /**
         * @var Rule[] $rules
         * @var Rule $rule
         */
        foreach ($rules as $rule){
            if ($rule->passes($attribute)->fails){
                $this->fails = true;
                $this->failed[$attribute] = $rule->message;
                break;
            }
        }
    }
}