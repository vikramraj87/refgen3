<?php
/**
 * Created by PhpStorm.
 * User: vikramraj
 * Date: 16/06/14
 * Time: 8:07 PM
 */

namespace Pubmed\Service;

use Article\Entity\Article;
use Article\Table\ArticleTable;
use Pubmed\Entity\Adapter;
use Zend\Session\Container;

class PubmedService
{
    /** @var ArticleTable */
    protected $table;

    private $container;

    /**
     * @param \Article\Table\ArticleTable $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    public function search($term = '', $page = 1)
    {
        $page = (int) $page;

        $container = $this->container();
        if($container->offsetGet('term') == $term && $container->offsetGet('page') == $page) {
            return $container->offsetGet('indexerIds');
        }
        $adapter = Adapter::getInstance();
        $indexerIds = $adapter->search($term, $page);

        if(!empty($indexerIds)) {
            $container->offsetSet('term', $term);
            $container->offsetSet('page', $page);
            $container->offsetSet('indexerIds', $indexerIds);
        }
        return $indexerIds;
    }

    /**
     * Gets the article from the database. If not available in
     * the database, gets it from pubmed and saves it in the
     * database before returning the entire result
     *
     * @param array $ids
     * @param bool $incomplete
     * @return array
     */
    public function fetchArticlesByIndexerIds(array $ids = array(), &$incomplete = false)
    {
        // 1. Get the results from the database
        $result = $this->table->checkArticles($ids);

        // 2. Get the ids not available in the database
        $dbIds = array_keys($result);
        $adapterIds = array_values(array_diff($ids, $dbIds));

        // 3. Get the articles for those ids from Pubmed
        if(!empty($adapterIds)) {
            $adapter = Adapter::getInstance();
            $adapterResult = $adapter->fetchByIds($adapterIds);
            if($adapterResult === null) {
                $incomplete = true;
            } else {
                if($adapterResult instanceof Article) {
                    $adapterResult = array($adapterResult);
                }

                // 4. Save the articles in the database
                foreach($adapterResult as &$article) {
                    /** @var Article $article */
                    $article = $this->table->checkArticle($article);
                }

                // 5. Populate the result with the articles obtained
                $result = array_merge($result, $adapterResult);
            }
        }

        // 6. Sort the result as requested by the ids
        $output = array();
        foreach($ids as $id) {
            if(isset($result[$id]) && $result[$id] instanceof Article) {
                $output[$id] = $result[$id];
            }
        }

        // 7. Return the result
        return $output;
    }

    private function container()
    {
        if(null === $this->container) {
            $this->container = new Container('searchCache');
        }
        return $this->container;
    }
} 