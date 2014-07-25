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
    /** @var int */
    private $id;

    /** @var string */
    private $class;

    /** @var int|mixed */
    private $code;

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

    /** @var \DateTime */
    private $raisedOn;

    /**
     * @param int|mixed $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
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
     * @return $this
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
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
     * @return $this
     */
    public function setLine($line)
    {
        $this->line = $line;
        return $this;
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
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
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
     * @return $this
     */
    public function setPrevious(Exception $previous)
    {
        $this->previous = $previous;
        return $this;
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
     * @return $this
     */
    public function setTrace($trace)
    {
        $this->trace = $trace;
        return $this;
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
     * @return $this
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param int $id
     * @return $this;
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \DateTime $raisedOn
     * @return $this
     */
    public function setRaisedOn(\DateTime $raisedOn)
    {
        $this->raisedOn = $raisedOn;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRaisedOn()
    {
        return $this->raisedOn;
    }
}