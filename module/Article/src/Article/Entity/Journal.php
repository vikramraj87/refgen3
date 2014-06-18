<?php
/**
 * Created by PhpStorm.
 * User: vikramraj
 * Date: 25/05/14
 * Time: 5:07 PM
 */

namespace Article\Entity;


class Journal
{
    /** @var int  */
    protected $id = 0;

    /** @var string  */
    protected $title = '';

    /** @var string  */
    protected $issn = '';

    /** @var string  */
    protected $abbr = '';

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $abbr
     */
    public function setAbbr($abbr)
    {
        $this->abbr = str_replace(".", "", $abbr);
    }

    /**
     * @return string
     */
    public function getAbbr()
    {
        return $this->abbr;
    }

    /**
     * @param string $issn
     */
    public function setIssn($issn)
    {
        $this->issn = $issn;
    }

    /**
     * @return string
     */
    public function getIssn()
    {
        return $this->issn;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * returns the Journal object as an array
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'issn'  => $this->issn,
            'title' => $this->title,
            'abbr'  => $this->abbr
        );
    }

    /**
     * Static function to create Journal object from array of data
     *
     * @param array $data
     * @return Journal
     */
    static public function createFromArray(array $data = array())
    {
        $id    = isset($data['id'])    ? (int) $data['id']    : '';
        $issn  = isset($data['issn'])  ? $data['issn']        : '';
        $title = isset($data['title']) ? $data['title']       : '';
        $abbr  = isset($data['abbr'])  ? $data['abbr']        : '';

        $journal = new self();
        $journal->setId($id);
        $journal->setAbbr($abbr);
        $journal->setTitle($title);
        $journal->setIssn($issn);
        return $journal;
    }
} 