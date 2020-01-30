<?php

namespace UPTools;

/**
 * Class ConsoleLauncher
 *
 * @package UPTools
 */
class ConsoleLauncher
{
    private $handlers = array();

    protected static function style($style = 'default'){
        $styles = array(
            'default' => "\033[0m",
            'black' => "\033[1;30m",
            'red' => "\033[1;31m",
            'green' => "\033[1;32m",
            'yellow' => "\033[1;33m",
            'blue' => "\033[1;34m",
            'purple' => "\033[1;35m",
            'cyan' => "\033[1;36m",
            'white' => "\033[1;37m"
        );
        $style = array_key_exists($style, $styles) ? $styles[$style] : $styles['default'];
        echo $style;
    }

    protected static function arrayToString($array, $padding = 1){
        $result = '';
        foreach ($array as $key => $value){
            $key = is_integer($key) ? ($key+1) : $key;

            if (is_array($value)){
                $result.= str_repeat(" ", $padding).$key.': '."\n";
                $result.= static::arrayToString($value, (3 + $padding));
            } else {
                if (is_bool($value)) $value = ($value) ? 'true' : 'false';
                $result.= str_repeat(" ", $padding).$key.': '.$value."\n";
            }
        }
        return $result;
    }

    public function getOptions(){
        global $argv;
        if (php_sapi_name() !== 'cli'){
            return [];
        }

        if (!$argv or !is_array($argv)){
            return [];
        }

        $result = [];
        foreach (array_slice($argv, 1) as $argument){
            if(preg_match('/^((?:-{1,2})?\w[\w\-]*)=(.*?)$/is', $argument, $matches)){
                $result[$matches[1]]=$matches[2];
            } else {
                $result[$argument] = '';
            }
        }

        return $result;
    }

    public function message($message, $style = 'default'){
        if (is_bool($message)) {
            $message = ($message ? 'true' : 'false');
        }

        if (is_array($message) or is_object($message)) {
            $message = static::arrayToString((array)$message);
        }

        static::style($style);
        fwrite(STDOUT, "{$message}\n");
        static::style();
    }

    public function error($message){
        static::message("Error: {$message}", 'red');
        die();
    }

    public function notice($message){
        static::message("Notice: {$message}", 'blue');
    }

    public function warning($message){
        static::message("Warning: {$message}", 'yellow');
    }

    public function success($message){
        static::message($message, 'green');
    }

    public function request($message, $required = false, callable $prepare = null){
        do {
            static::style('blue');
            fwrite(STDOUT, $message.' ');
            static::style();

            $value = trim(fgets(STDIN));
            if ($prepare) {
                $value = $prepare($value);
            }
        } while ($required and ($value === '' or $value === null));

        return $value;
    }

    public function process(array $dataset, callable $itemHandler)
    {
        $startTime = time();
        $size = 50;
        $done = 1;
        $total = count($dataset);
        $display = '';

        foreach ($dataset as $itemKey => $itemValue){

            if ($itemHandler($itemValue, $itemKey) === false){
                break;
            }

            $done++;
            $now = time();
            $elapsedTime = $now - $startTime;
            $rate = $elapsedTime / $done;
            $leftTime = round($rate * ($total - $done));

            $perc=(double)($done / $total);
            $barSize=floor($perc*$size);

            $display.= "[".str_repeat("▒", $barSize);
            $display.= $barSize < $size ? str_repeat(" ", $size - $barSize) : "▒";
            $display.= "] " . ($perc * 100) . "%  {$done}/{$total}";
            $display.= "  remaining: " . $leftTime > 60 ? (round($leftTime / 60, 1) . "min.") : "{$leftTime} sec.";
            $display.= "  elapsed: " . $elapsedTime > 60 ? (round($elapsedTime / 60, 1) . "min.") : "{$elapsedTime} sec.";

            fwrite(STDOUT, "\r{$display}");
        }

        fwrite(STDOUT, "\n");
    }

    public function bind($pattern, callable $handler){
        if (!is_string($pattern)) {
            return $this;
        }

        $pattern = '/^('.rtrim(ltrim(trim($pattern),'^'),'$').')$/is';
        $pattern = preg_replace('/\s+/',' ', $pattern);
        $this->handlers[$pattern] = $handler;

        return $this;
    }

    public function start(){
        if (empty($options = $this->getOptions())) {
            return $this;
        }

        $command = implode(' ', array_keys($options));
        foreach ($this->handlers as $pattern => $handler){
            if (preg_match($pattern, $command)) $handler($options);
        }

        return $this;
    }
}