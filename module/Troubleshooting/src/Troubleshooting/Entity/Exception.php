<?php
/**
 * Created by PhpStorm.
 * User: vikramraj
 * Date: 22/07/14
 * Time: 8:07 AM
 */

namespace Troubleshooting\Entity;


class Exception
{
    /** @var int|mixed */
    private $code;

    /** @var string */
    private $class;

    /** @var string */
    private $message;

    /** @var string */
    private $file;

    /** @var int */
    private $line;

    /** @var array */
    private $trace;

    /** @var Exception */
    private $previous;

    /**
     * @param int|mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return int|mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param int $line
     */
    public function setLine($line)
    {
        $this->line = $line;
    }

    /**
     * @return int
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param \Troubleshooting\Entity\Exception $previous
     */
    public function setPrevious(Exception $previous)
    {
        $this->previous = $previous;
    }

    /**
     * @return \Troubleshooting\Entity\Exception
     */
    public function getPrevious()
    {
        return $this->previous;
    }

    /**
     * @param array $trace
     */
    public function setTrace($trace)
    {
        $this->trace = $trace;
    }

    /**
     * @return array
     */
    public function getTrace()
    {
        return $this->trace;
    }

    /**
     * @param string $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }


}