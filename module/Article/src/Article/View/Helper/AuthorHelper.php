<?php
namespace Article\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Article\Entity\Author;

class AuthorHelper extends AbstractHelper
{
    private $authors = array();

    /**
     * @param Author[] $authors
     * @return $this
     */
    public function __invoke(array $authors = array())
    {
        $this->authors = $authors;
        return $this;
    }

    public function render()
    {
        $authors = array();
        if(empty($this->authors)) {
            return '[No authors listed]';
        }
        foreach($this->authors as $author) {
            /** @var Author $author */
            if(!$author instanceof Author) {
                throw new \InvalidArgumentException('Param authors must be an array of Author objects');
            }
            $authors[] = $author->getLastName() . ' ' . $author->getInitials();
        }
        return implode(', ', $authors);
    }
} 