<?php

namespace UPTools;

use Exception;

/**
 * Class FileInfo
 *
 * @property string $path
 * @property string $realPath
 * @property string $name
 * @property string $extension
 * @property string $type
 * @property Dir $directory
 * @property string $permissions
 * @property integer $size
 * @property array $owner
 * @property array $group
 * @property integer $accessTime
 * @property integer $modifiedTime
 *
 * @package UPTools
 */
class FileInfo
{
    /**
     * The instance methods that can be proxied.
     *
     * @var array
     */
    protected static $arrProxyMethods = [
        'path', 'realPath', 'name', 'extension', 'type', 'directory', 'permissions',
        'size', 'owner', 'group', 'accessTime', 'modifiedTime'
    ];

    /**
     * File is valid.
     *
     * @var boolean
     */
    protected $valid = false;

    /**
     * Path to file.
     *
     * @var string
     */
    protected $path;

    /**
     * Create a new file instance for the given path.
     *
     * @param string $path
     * @return FileInfo|File|Dir
     */
    public static function bind($path){
        return new static($path);
    }

    /**
     * Create a new file instance.
     *
     * @param string|resource $path
     */
    public function __construct($path){
        $this->initialize($path);
    }

    /**
     * Call dynamic properties
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name){
        if (in_array($name, static::$arrProxyMethods)){
            return $this->{$name}();
        }

        throw new Exception("Property [{$name}] does not exist on [".static::class."] instance.");
    }

    /**
     * Convert the object to its string representation.
     *
     * @return string
     */
    public function __toString(){
        return $this->path ?? '';
    }

    /**
     * Gets absolute path to file.
     *
     * @return string
     */
    public function path(){
        if (!$this->valid){
            throw new Exception("The path [{$this->path}] does not exist");
        }

        return $this->path;
    }

    /**
     * Gets absolute path to file.
     *
     * @return string
     */
    public function realPath(){
        if (!$this->valid){
            throw new Exception("The path [{$this->path}] does not exist");
        }

        return realpath($this->path);
    }

    /**
     * Check whether the file exists.
     *
     * @param callable|null $callable
     * @return bool
     */
    public function exists(callable $callable = null){

        if (file_exists($this->path)){
            if (!is_null($callable)){
                $callable($this);
            }
            return $this->valid = true;
        }

        return $this->valid = false;
    }

    /**
     * Check that the file does not exist.
     *
     * @param callable|null $callable
     * @return bool
     */
    public function missing(callable $callable = null){

        if (!$this->exists()){
            if (!is_null($callable)){
                $callable($this);
            }
            return true;
        }

        return false;
    }

    /**
     * Gets the filename.
     *
     * @return string
     */
    public function name(){
        return basename($this->path());
    }

    /**
     * Gets the file extension.
     *
     * @return string
     */
    public function extension(){
        return pathinfo($this->path(), PATHINFO_EXTENSION);
    }

    /**
     * Get the file's mimetype.
     *
     * @return string
     */
    public function type(){
        return mime_content_type($this->path());
    }

    /**
     * Get the absolute path of the parent directory.
     *
     * @return Dir
     */
    public function directory(){
        return new Dir(dirname($this->path()));
    }

    /**
     * Gets file permissions.
     *
     * @return string
     */
    public function permissions(){
        return substr(sprintf('%o', fileperms($this->path())), -4);
    }

    /**
     * Gets file size.
     *
     * @return int
     */
    public function size(){
        if ($this->isFile()){
            $file = fopen($this->path(), "rb");
            fseek($file, 0, SEEK_END);
            $size = ftell($file);
            fclose($file);
            return $size;
        }

        return filesize($this->path());
    }

    /**
     * Gets the owner of the file.
     *
     * @return array
     */
    public function owner(){
        if ($owner = fileowner($this->path())){
            return posix_getpwuid($owner);
        }

        throw new Exception("Failed to get the owner for the path [{$this->path}].");
    }

    /**
     * Gets the file group.
     *
     * @return array
     */
    public function group(){
        if ($group = filegroup($this->path())){
            return posix_getgrgid($group);
        }

        throw new Exception("Failed to get the group for the path [{$this->path}].");
    }

    /**
     * Gets last access time of the file.
     *
     * @param bool $from
     * @return int
     */
    public function accessTime($from = false){
        if ($from){
            return time() - (integer) fileatime($this->path());
        }
        return (integer) fileatime($this->path());
    }

    /**
     * Gets the last modified time.
     *
     * @param bool $from
     * @return int
     */
    public function modifiedTime($from = false){
        if ($from){
            return time() - (integer) filemtime($this->path());
        }
        return (integer) filemtime($this->path());
    }

    /**
     * Tells if the file is writable.
     *
     * @param callable|null $callable
     * @return bool
     */
    public function isWritable(callable $callable = null){
        if (is_writable($this->path())){
            if (!is_null($callable)){
                $callable($this);
            }
            return true;
        }

        return false;
    }

    /**
     * Tells if file is readable.
     *
     * @param callable|null $callable
     * @return bool
     */
    public function isReadable(callable $callable = null){
        if (is_readable($this->path())){
            if (!is_null($callable)){
                $callable($this);
            }
            return true;
        }

        return false;
    }

    /**
     * Tells if the file is executable.
     *
     * @param callable|null $callable
     * @return bool
     */
    public function isExecutable(callable $callable = null){
        if (is_executable($this->path())){
            if (!is_null($callable)){
                $callable($this);
            }
            return true;
        }

        return false;
    }

    /**
     * Tells if the object references a regular file.
     *
     * @param callable|null $callable
     * @return bool
     */
    public function isFile(callable $callable = null){
        if (is_file($this->path())){
            if (!is_null($callable)){
                $callable($this);
            }
            return true;
        }

        return false;
    }

    /**
     * Tells if the file is a directory.
     *
     * @param callable|null $callable
     * @return bool
     */
    public function isDirectory(callable $callable = null){
        if (is_dir($this->path())){
            if (!is_null($callable)){
                $callable($this);
            }
            return true;
        }

        return false;
    }

    /**
     * Tells if the file is a link.
     *
     * @param callable|null $callable
     * @return bool
     */
    public function isLink(callable $callable = null){
        $path = $this->path();

        if (is_link($path)){
            if (!is_null($callable)){
                $callable($this, new FileInfo(readlink($path)));
            }
            return true;
        }

        return false;
    }

    /**
     * Object instance initialization.
     *
     * @param $path
     * @return bool
     */
    protected function initialize($path){
        if (is_string($path)){
            $this->path = $path;
            $this->valid = file_exists($path);
            return true;
        }
        return false;
    }
}