<?php

namespace UPTools;

use Exception;

/**
 * Class File
 *
 * @method static File bind(string $path)
 *
 * @package UPTools
 */
class File extends FileInfo
{
    /**
     * @var resource
     */
    protected $resource;

    /**
     * Creates a new instance for a temporary file.
     * This file is automatically deleted with the deletion of the object.
     *
     * @param string $content
     * @return File
     */
    public static function make($content = ''){
        $file = tmpfile();
        fwrite($file, $content);
        return new static($file);
    }

    /**
     * Check whether the file exists.
     *
     * @param callable|null $callable
     * @return bool
     */
    public function exists(callable $callable = null){

        if (is_file($this->path)){
            if (!is_null($callable)){
                $callable($this);
            }
            return $this->valid = true;
        }

        return $this->valid = false;
    }

    /**
     * Create a new file with the given content if it does not exist.
     *
     * @param string $content
     * @return $this
     */
    public function existsOrCreate($content = ''){
        if (!$this->exists()){
            $this->put($content);
        }

        return $this;
    }

    /**
     * Delete file if it exists
     *
     * @return $this
     */
    public function missingOrDelete(){
        if ($this->exists()){
            $this->delete();
        }

        return $this;
    }

    /**
     * Determine if the file is empty.
     *
     * @param callable|null $callable
     * @return bool
     */
    public function isEmpty(callable $callable = null){
        $content = $this->content();

        if (empty($content)){
            if (!is_null($callable)){
                $callable($this);
            }
            return true;
        }

        return false;
    }

    /**
     * Determine if the file is not empty.
     *
     * @param callable|null $callable
     * @return bool
     */
    public function isNotEmpty(callable $callable = null){
        if (!$this->isEmpty()){
            if (!is_null($callable)){
                $callable($this);
            }
            return true;
        }

        return false;
    }

    /**
     * Gets the file hash.
     *
     * @param string $salt
     * @return string
     */
    public function hash($salt = ''){
        return sha1($salt.$this->name().sha1_file($this->path()));
    }

    /**
     * Encode base64 file.
     *
     * @return string
     */
    public function base64(){
        return base64_encode($this->content());
    }

    /**
     * Open file.
     *
     * @param string $mode
     * @param null $context
     * @return $this
     */
    public function open($mode = 'r+', $context = null){

        if (is_null($context)){
            $resource = fopen($this->path(), $mode);
        } else {
            $resource = fopen($this->path(), $mode, false, $context);
        }

        if (is_resource($resource)){
            $this->resource = $resource;
            $this->valid = true;

            return $this;
        }

        throw new Exception("Failed open file [{$this->path}]");
    }

    /**
     * Close file.
     *
     * @return $this
     */
    public function close(){
        if(!is_null($this->resource)){
            fclose($this->resource);
            $this->resource = null;
        }

        return $this;
    }

    /**
     * Returns the current position of the file read/write pointer.
     *
     * @return int
     */
    public function cursor(){
        if (is_null($this->resource)){
            $this->open();
        }

        if (($cursor = ftell($this->resource)) === false){
            throw new Exception("Failed get cursor position for file [{$this->path}]");
        }

        return $cursor;
    }

    /**
     * Sets the offset for the cursor.
     *
     * @param integer $offset
     * @return $this
     */
    public function cursorOffset($offset){
        if (is_null($this->resource)){
            $this->open();
        }

        if ((fseek($this->resource, $offset, SEEK_CUR)) !== 0){
            throw new Exception("Failed change cursor position for file [{$this->path}]");
        }

        return $this;
    }

    /**
     * Move the cursor to the beginning of the file.
     *
     * @return $this
     */
    public function cursorToBegin(){
        if (is_null($this->resource)){
            $this->open();
        }

        if ((rewind($this->resource)) === false){
            throw new Exception("Failed change cursor position for file [{$this->path}]");
        }

        return $this;
    }

    /**
     * Move the cursor to the end of the file.
     *
     * @return $this
     */
    public function cursorToEnd(){
        if (is_null($this->resource)){
            $this->open();
        }

        if ((fseek($this->resource, 0, SEEK_END)) !== 0){
            throw new Exception("Failed change cursor position for file [{$this->path}]");
        }

        return $this;
    }

    /**
     * Binary-safe file read.
     *
     * @param integer|null $length
     * @return bool|string
     */
    public function read($length = null){
        if (is_null($this->resource)){
            $this->open();
        }

        if (is_null($length)){
            if (($length = $this->size()) === 0){
                return '';
            }
        }

        return fread($this->resource, $length);
    }

    /**
     * Gets line from file.
     * If there is no more data to read in the file, then FALSE is returned.
     *
     * @return bool|string
     */
    public function readRow(){
        if (is_null($this->resource)){
            $this->open();
        }

        return fgets($this->resource);
    }

    /**
     * Gets character from file.
     * If there is no more data to read in the file, then FALSE is returned.
     *
     * @return bool|string
     */
    public function readChar(){
        if (is_null($this->resource)){
            $this->open();
        }

        return fgetc($this->resource);
    }

    /**
     * Binary-safe file write.
     * If the [$length] argument is given, writing will stop after [$length] bytes
     * have been written
     *
     * @param string $content
     * @param integer|null $length
     * @return $this
     */
    public function write($content, $length = null){
        if (is_null($this->resource)){
            $this->open();
        }

        if (is_null($length)){
            fwrite($this->resource, $content);
        } else {
            fwrite($this->resource, $content, $length);
        }

        return $this;
    }

    /**
     * Reads entire file into a string.
     *
     * @return string
     */
    public function content(){
        $content = file_get_contents($this->path());
        if ($content === false){
            throw new Exception("Failed reading file [{$this->path}]");
        }

        $this->valid = true;

        return $content;
    }

    /**
     * Write contents to a file.
     * If file does not exist, the file is created. Otherwise, the existing file is overwritten.
     *
     * @param $content
     * @return $this
     */
    public function put($content){
        if (@file_put_contents($this->path, $content) === false){
            throw new Exception("Failed reading file [{$this->path}]");
        }

        $this->valid = true;

        return $this;
    }

    /**
     * Write the contents to the top of the file.
     * If file does not exist, the file is created.
     *
     * @param $content
     * @return $this
     */
    public function prepend($content){
        return $this->put($content . file_get_contents($this->path) ?? '');
    }

    /**
     * Write the content to the end of the file.
     * If file does not exist, the file is created.
     *
     * @param $content
     * @return $this
     */
    public function append($content){
        if (@file_put_contents($this->path, $content, FILE_APPEND) === false){
            throw new Exception("Failed reading file [{$this->path}]");
        }

        $this->valid = true;

        return $this;
    }

    /**
     * Rename the file.
     *
     * @param $name
     * @return $this
     */
    public function rename($name){
        $oldPath = $this->path();
        $newPath = Str::end(dirname($oldPath), DIRECTORY_SEPARATOR).$name;

        $this->close();

        if (@rename($oldPath, $newPath)){

            $this->path = $newPath;
            $this->valid = true;

            return $this;
        }

        throw new Exception("Failed to rename the file [{$oldPath}] to [{$newPath}].");
    }

    /**
     * Create the file.
     *
     * @param string $content
     * @return $this
     */
    public function create($content = ''){
        return $this->put($content);
    }

    /**
     * Save or copy the file.
     *
     * @param string $path
     * @return File
     */
    public function save($path){
        if (copy($this->path(), $path)){
            return new static($path);
        }

        throw new Exception("Failed to copy the file [{$this->path}] to [{$path}]");
    }

    /**
     * Delete the file.
     *
     * @return $this
     */
    public function delete(){
        $result = @unlink($this->path());

        $this->close();

        if ($result){
            $this->valid = false;
        }

        return $this;
    }

    /**
     * File instance initialization.
     *
     * @param $path
     * @return bool
     */
    protected function initialize($path){
        if (is_string($path)){

            $this->path = $path;
            $this->valid = is_file($path);

            return true;

        } elseif (is_resource($path)){

            $this->resource = $path;
            $this->path = stream_get_meta_data($path)['uri'];
            $this->valid = true;

            return true;
        }

        throw new Exception("Invalid file name. Expected [string], passed [" . gettype($path) . "].");
    }
}