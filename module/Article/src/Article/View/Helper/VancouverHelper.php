<?php
namespace Article\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Article\Entity\Article,
    Article\Entity\PubDate;

class VancouverHelper extends AbstractHelper
{
    public function __invoke(Article $article)
    {
        $citation = '';
        $authors = array();
        foreach($article->getAuthors() as $author) {
            $authors[] = $author->getLastName() . ' ' . $author->getInitials();
        }
        if(count($authors) > 6) {
            $authors = array_slice($authors, 0, 6);
            $authors[] = 'et al';
        }
        if(count($authors) > 0) {
            $citation .= implode(', ', $authors) . '. ';
        }
        $citation .= $article->getTitle() . ' ';
        $citation .= $article->getJournal()->getAbbr() . '. ';

        if($article->getJournalIssue()->getPubStatus()) {
            $citation .= $this->dateToString($article->getJournalIssue()->getPubDate());
            $volume = $article->getJournalIssue()->getVolume();
            if(!empty($volume)) {
                $citation .= ';' . $volume;
                $issue = $article->getJournalIssue()->getIssue();
                if(!empty($issue)) {
                    $citation .= '(' . $issue . ')';
                }
            }
            $pages = $article->getJournalIssue()->getPages();
            if(!empty($pages)) {
                $citation .= ':' . $pages;
            }
            return $citation;
        }
        $citation .= 'Epub ' . $this->dateToString($article->getJournalIssue()->getPubDate());
        return $citation;
    }

    private function dateToString(PubDate $date)
    {
        $str = $date->getYear();
        if(!empty($date)) {
            $month = $date->getMonth();
            if(!empty($month)) {
                $str .= ' ' . $month;
            }
        }
        return $str;
    }
}