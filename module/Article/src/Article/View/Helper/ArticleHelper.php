<?php
/**
 * Created by PhpStorm.
 * User: vikramraj
 * Date: 17/06/14
 * Time: 11:04 PM
 */

namespace Article\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Article\Entity\Article;
class ArticleHelper extends AbstractHelper
{
    /** @var Article */
    private $article;

    public function __invoke(Article $article)
    {
        $this->article = $article;
        return $this;
    }

    public function single()
    {
        $output = '<article>';
        $output .= $this->generateHeader();
        $abstract = $this->article->getAbstract();
        $isTruncated = false;
        if(!empty($abstract)) {
            $output .= '<h3>Abstract</h3><p>' . $this->article->getTruncatedAbstract($isTruncated) . '</p>';
        }
        if($isTruncated) {
            // read more link
        }
        $output .= '</article>';
    }

    public function multiple()
    {

    }

    private function generateHeader()
    {
        $html = '<header><h2>' . $this->article->getTitle() . '</h2>';
        $html .= '<h3>' . $this->formatAuthors() . '</h3></header>';
        return $html;
    }

    private function generateFooter()
    {

    }



    private function formatAuthors()
    {
        $authors = $this->article->getAuthors();
        if(empty($authors)) {
            return '[No authors listed]';
        }
        $tmp = array();
        foreach($authors as $author) {
            /** \Article\Entity\Author $author */
            $tmp[] = $author->getLastName() . ' ' . $author->getInitials();
        }
        return implode(', ', $tmp);
    }

    private function truncate($str = '', $limit = 300, $break = " ", $trailing = "...")
    {
        $truncated = $str;
        if(strlen($truncated) > $limit) {
            $truncated = substr($truncated, 0, $limit - 1);
            $bp = strrpos($truncated, $break);
            $truncated = substr($truncated, 0, $bp) . $trailing;
        }
        return $truncated;
    }
} 