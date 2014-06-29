<?php
/**
 * Created by PhpStorm.
 * User: vikramraj
 * Date: 09/06/14
 * Time: 1:28 AM
 */

namespace Article\Entity;

use Article\Entity\PubDate;

class JournalIssue
{
    /** @var bool */
    protected $pubStatus = true;

    /** @var string */
    protected $volume = '';

    /** @var string */
    protected $issue = '';

    /** @var string  */
    protected $pages = '';

    /** @var PubDate */
    protected $pubDate;

    /**
     * @param string $issue
     */
    public function setIssue($issue)
    {
        $this->issue = $issue;
    }

    /**
     * @return string
     */
    public function getIssue()
    {
        return $this->issue;
    }

    /**
     * @param \Article\Entity\PubDate $pubDate
     */
    public function setPubDate(PubDate $pubDate)
    {
        $this->pubDate = $pubDate;
    }

    /**
     * @return \Article\Entity\PubDate
     */
    public function getPubDate()
    {
        return $this->pubDate;
    }

    /**
     * @param boolean $pubStatus
     */
    public function setPubStatus($pubStatus)
    {
        $this->pubStatus = $pubStatus;
    }

    /**
     * @return boolean
     */
    public function getPubStatus()
    {
        return $this->pubStatus;
    }

    /**
     * @param string $volume
     */
    public function setVolume($volume)
    {
        $this->volume = $volume;
    }

    /**
     * @return string
     */
    public function getVolume()
    {
        return $this->volume;
    }

    /**
     * Setter for pages. Also formats the pages. Eg. 1025-1028 will become 1025-8
     *
     * @param string $pages
     */
    public function setPages($pages)
    {
        $p = explode('-', $pages, 2);
        if(count($p) > 1) {
            $from = trim($p[0]);
            $to   = trim($p[1]);
            if(strlen($from) >= strlen($to)) {
                if(strlen($to) > 1) {
                    $tFrom = substr($from, strlen($from) - strlen($to));
                    $i = 0;
                    while($i < strlen($to) && $to[$i] == $tFrom[$i]) {
                        $i++;
                    }
                    $to = substr($to, $i);
                }
                if(empty($to)) {
                    $pages = $from;
                } else {
                    $pages = $from . '-' . $to;
                }
            }
        }
        $this->pages = $pages;
    }

    /**
     * @return string
     */
    public function getPages()
    {
        return $this->pages;
    }

    public function toArray()
    {
        return array_merge(array(
            'volume' => $this->volume,
            'issue'  => $this->issue,
            'pages'  => $this->pages
            ), $this->pubDate->toArray()
        );

    }
} 