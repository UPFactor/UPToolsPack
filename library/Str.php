<?php

namespace UPTools;

use Closure;

/**
 * Class Str
 *
 * @package UPTools
 */
class Str {

    /**
     * Convert all applicable characters to HTML entities.
     *
     * @param $string
     * @return string
     */
    static public function escape($string){
        return htmlentities($string, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Strip HTML and PHP tags from a string.
     *
     * @param $string
     * @return string
     */
    static public function striptags($string){
        return strip_tags($string);
    }

    /**
     * Determine if the given string matches one of the elements in the given array.
     *
     * @param $value
     * @param array $stack
     * @param Closure|null $callable
     * @return bool
     */
    static public function in($value, array $stack, Closure $callable = null){
        if (in_array($value, $stack)){
            if (!is_null($callable)){
                $callable($value, $stack);
            }
            return true;
        }
        return false;
    }

    /**
     * Determine if the given string matches the given pattern.
     *
     * @param string $value
     * @param string $pattern
     * @param Closure|null $callable
     * @return bool
     */
    static public function is($value, string $pattern, Closure $callable = null){
        if ($value === $pattern or preg_match('/^'.$pattern.'$/su', $value) === 1){
            if (!is_null($callable)){
                $callable($value);
            }
            return true;
        }
        return false;
    }

    /**
     * Determine if a given string is JSON.
     *
     * @param string $value
     * @param Closure|null $callable
     * @return bool
     */
    static public function isJSON($value, Closure $callable = null){
        if (!is_string($value)){
            return false;
        }

        $decoded = json_decode($value, true);

        if (json_last_error() === 0){
            if (!is_null($callable)){
                $callable($value, $decoded);
            }
            return true;
        }
        return false;
    }

    /**
     * Determine if a given string is serialize.
     *
     * @param string $value
     * @param Closure|null $callable
     * @return bool
     */
    static public function isSerialize($value, Closure $callable = null){
        if (!is_string($value)){
            return false;
        }

        if ($value === 'b:0;'){
            if (!is_null($callable)){
                $callable($value, false);
            }
            return true;
        }

        $decoded = @unserialize($value);

        if ($decoded !== false){
            if (!is_null($callable)){
                $callable($value, $decoded);
            }
            return true;
        }
        return false;
    }

    /**
     * Determine if a given string is local path.
     *
     * @param string $value
     * @param Closure|null $callable
     * @return bool
     */
    static public function isPath($value, Closure $callable = null){
        if (!is_string($value)){
            return false;
        }

        $value = realpath($value);

        if ($value !== false){
            if (!is_null($callable)){
                $callable($value);
            }
            return true;
        }
        return false;
    }

    /**
     * Determine if a given string is a valid URL.
     *
     * @param string $value
     * @param Closure|null $callable
     * @return bool
     */
    static public function isURL($value, Closure $callable = null){
        if (!is_string($value)){
            return false;
        }

        $value = filter_var($value, FILTER_VALIDATE_URL);

        if ($value !== false){
            if (!is_null($callable)){
                $callable($value);
            }
            return true;
        }
        return false;
    }

    /**
     * Determine if the string is a valid controller. (Notation: class @ method)
     *
     * @param string $value
     * @param Closure|null $callable
     * @return bool
     */
    static public function isController($value, Closure $callable = null){
        if (!is_string($value)){
            return false;
        }

        if (preg_match('/^([\w\\\]+)@(\w+)$/is', $value, $segments) === 1){
            if (!is_null($callable)){
                $callable($segments[1], $segments[2]);
            }
            return true;
        }
        return false;
    }

    /**
     * Determine whether the given substring is in at least one given string.
     *
     * @param string $substring
     * @param string|array $strings
     * @return bool
     */
    static public function contains($substring, $strings){
        foreach (Arr::wrap($strings) as $string) {
            if ($substring !== '' && mb_strpos($string, $substring) !== false){
                return true;
            }
        }
        return false;
    }

    /**
     * Get the portion of a string before a given value.
     *
     * @param string $string
     * @param string $search
     * @return string
     */
    static public function before($string, $search){
        return $string === '' ? $string : explode($search, $string, 2)[0];
    }

    /**
     * Return the remainder of a string after a given value.
     *
     * @param string $string
     * @param string $search
     * @return string
     */
    static public function after($string, $search){
        return $string === '' ? $string : array_reverse(explode($search, $string, 2))[0];
    }

    /**
     * Begin a string with a single instance of a given value.
     *
     * @param string $string
     * @param string $prefix
     * @return string
     */
    static public function begin($string, $prefix){
        return $prefix.preg_replace('/^(?:'.preg_quote($prefix, '/').')+/u', '', $string);
    }

    /**
     * Cap a string with a single instance of a given value.
     *
     * @param string $string
     * @param string $postfix
     * @return string
     */
    static public function end($string, $postfix){
        $quoted = preg_quote($postfix, '/');
        return preg_replace('/(?:'.$quoted.')+$/u', '', $string).$postfix;
    }

    /**
     * Determine if a given string starts with a given substring.
     *
     * @param string $string
     * @param string|array $substrings
     * @return bool
     */
    static public function beginWith($string, $substrings){
        foreach ((array) $substrings as $substring) {
            if ($substring !== '' && mb_substr($string, 0, mb_strlen($substring)) === (string) $substring) {
                return true;
            }
        }
        return false;
    }

    /**
     * Determine if a given string ends with a given substring.
     *
     * @param string $string
     * @param string|array $substrings
     * @return bool
     */
    static public function endWith($string, $substrings){
        foreach ((array) $substrings as $substring) {
            if (mb_substr($string, -mb_strlen($substring)) === (string) $substring) {
                return true;
            }
        }
        return false;
    }

    /**
     * Return the length of the given string.
     *
     * @param string $string
     * @return int
     */
    static public function length($string){
        return mb_strlen($string);
    }

    /**
     * Limit the number of characters in a string.
     *
     * @param string $string
     * @param int $limit
     * @param string $end
     * @return string
     */
    static public function charLimit($string, $limit = 100, $end = '...'){
        if (mb_strlen($string) <= $limit) {
            return (string) $string;
        }
        return mb_substr($string, 0, $limit).$end;
    }

    /**
     * Limit the number of words in a string.
     *
     * @param string $string
     * @param int $words
     * @param string $end
     * @return string
     */
    static public function wordLimit($string, $words = 100, $end = '...'){
        if (mb_strlen($string) === 0){
            return '';
        }

        if ($words === 0){
            return ''.$end;
        }

        if (preg_match('/^\s*+(?:\S++\s*+){1,'.$words.'}/u', $string, $matches) === 1){
            if ($string === $matches[0]){
                return $string;
            }
            return rtrim($matches[0]).$end;
        }
        return $string;
    }

    /**
     * Convert the given string to upper-case.
     *
     * @param string $string
     * @return string
     */
    static public function upper($string){
        return mb_strtoupper($string);
    }

    /**
     * Convert the given string to lower-case.
     *
     * @param string $string
     * @return string
     */
    static public function lower($string){
        return mb_strtolower($string);
    }

    /**
     * Convert the given string to title case.
     *
     * @param $string
     * @return string
     */
    static public function title($string){
        return mb_convert_case($string, MB_CASE_TITLE);
    }

    /**
     * Make a string's first character upper-case.
     *
     * @param string $string
     * @return string
     */
    static public function capitalize($string){
        return mb_strtoupper(mb_substr($string, 0, 1)).mb_substr($string, 1);
    }

    /**
     * Generate a more truly "random" alpha-numeric string.
     *
     * @param  int $length
     * @return string
     */
    static public function random($length = 16){
        $string = '';
        while (($len = strlen($string)) < $length) {
            $size = $length - $len;
            $bytes = random_bytes($size);
            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }
        return $string;
    }

    /**
     * Replace the first occurrence of a given value in the string.
     *
     * @param string $search
     * @param string $replace
     * @param string $string
     * @return string
     */
    static public function replaceFirst($search, $replace, $string){
        if ($search === ''){
            return (string) $string;
        }
        return implode($replace, explode($search, $string, 2));
    }

    /**
     * Replace the last occurrence of a given value in the string.
     *
     * @param string $search
     * @param string $replace
     * @param string $string
     * @return string
     */
    static public function replaceLast($search, $replace, $string){
        if (($length = mb_strlen($search)) === 0){
            return (string) $string;
        }

        if (($position = mb_strrpos($string, $search)) === false){
            return (string) $string;
        }

        return mb_substr($string, 0, $position).
            $replace.
            mb_substr($string, $position+$length);
    }

    /**
     * Replace a given value in the string sequentially with an array.
     *
     * @param string $search
     * @param array $replace
     * @param string $string
     * @return string
     */
    static public function replaceSequence($search, array $replace, $string){
        foreach ($replace as $item) {
            $string = static::replaceFirst($search, $item, $string);
        }
        return $string;
    }

    /**
     * Convert relative links to resources in an html document, by setting the transferred root.
     * Links in the following html tags will be converted:
     * <link href="" ... >
     * <area href="" ... >
     * <img src="" ... >
     * <script src="" ... >
     * <frame src="" ... >
     * <iframe src="" ... >
     * <source src="" ... >
     * <img src="" ... >
     * <audio src="" ... >
     * <video src="" ... >
     * <embed src="" ... >
     * <track src="" ... >
     * <input src="" ... >
     * <... style="... url('')" ... >
     *
     * @example <img src="img.jpg"> converted to <img src="http://site.com/img.jpg"> for $root = "http://site.com"
     * @example <img src="img.jpg"> converted to <img src="/dir/subdir/img.jpg"> for $root = "/dir/subdir"
     *
     * @param string $root
     * @param string $string
     * @return string
     */
    static public function replaceLinks($root, $string){
        return preg_replace([
            '/(<\s*(?:link|area)[^>]*href\s*=\s*[\"\'])((?!http)(?!ftp)(?!\/\/).*?)([\"\'][^<]*>)/is',
            '/(<\s*(?:img|script|i?frame|source|audio|video|embed|track|input)[^>]*src\s*=\s*[\"\'])((?!http)(?!ftp)(?!\/\/).*?)([\"\'][^<]*>)/is',
            '/(<\s*[^>]*style\s*=\s*[\"\'].*?url\([\'\"]?)((?!http)(?!ftp)(?!\/\/)[0-9a-z\.\-\_\\\?\=\[\]\&]+)([\'\"]?\).*?[\"\'][^<]*>)/is',
        ], '${1}' . static::end($root, '/') . '${2}${3}', $string);
    }
}