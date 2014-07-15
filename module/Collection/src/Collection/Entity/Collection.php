<?php
namespace Collection\Entity;

use DateTime;
use Article\Entity\Article;

class Collection implements \Countable
{
    /** @var int */
    private $id = 0;

    /** @var string */
    private $name = '';

    /** @var DateTime */
    private $createdOn;

    /** @var DateTime */
    private $updatedOn;

    /** @var Article[] */
    private $articles = array();

    /** @var int[] */
    private $position = array();

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        return count($this->articles);
    }


    /**
     * @param \Article\Entity\Article[] $articles
     */
    public function setArticles($articles)
    {
        $this->articles = array();
        $this->position = array();
        foreach($articles as $article) {
            /** @var Article $article */
            $this->articles[$article->getId()] = $article;
        }
        $this->position = array_keys($this->articles);
    }

    /**
     * @return \Article\Entity\Article[]
     */
    public function getArticles()
    {
        $articles = array();
        foreach($this->position as $id) {
            $articles[$id] = $this->articles[$id];
        }
        return $articles;
    }

    /**
     * @param \DateTime $createdOn
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

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
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param \DateTime $updatedOn
     */
    public function setUpdatedOn($updatedOn)
    {
        $this->updatedOn = $updatedOn;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedOn()
    {
        return $this->updatedOn;
    }

    public function addArticle(Article $article)
    {
        $this->articles[$article->getId()] = $article;
        if(in_array($article->getId(), $this->position)) {
            return;
        }
        $this->position[] = $article->getId();
    }

    public function removeArticle($id = 0)
    {
        $id = (int) $id;
        if(!isset($this->articles[$id])) {
            return;
        }
        unset($this->articles[$id]);
        if(in_array($id, $this->position)) {
            $pos = array_keys($this->position, $id)[0];
            array_splice($this->position, $pos, 1);
        }
    }

    public function serialize()
    {
        $articleIds = array();
        foreach($this->position as $pos => $id) {
            $articleIds[] = $id;
        }
        return array(
            'id'         => $this->id,
            'name'       => $this->name,
            'createdOn'  => $this->createdOn,
            'updatedOn'  => $this->updatedOn,
            'articleIds' => $articleIds,
        );
    }

    private function setPosition($ids = array()) {
        $this->position = array();
        foreach($ids as $id) {
            if(array_key_exists($id, $this->articles)) {
                $this->position[] = $id;
            }
        }
    }
}