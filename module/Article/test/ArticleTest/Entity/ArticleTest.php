<?php
/**
 * Created by PhpStorm.
 * User: vikramraj
 * Date: 09/06/14
 * Time: 12:24 AM
 */

namespace ArticleTest\Entity;

use Article\Entity\AbstractText,
    Article\Entity\Article,
    Article\Entity\Author,
    Article\Entity\Journal,
    Article\Entity\JournalIssue,
    Article\Entity\PubDate,
    Article\Entity\Keyword;

use PHPUnit_Framework_TestCase;

class ArticleTest extends PHPUnit_Framework_TestCase
{
    /** @var Article */
    protected $article;

    protected $data = array();

    /**
     * @return Article
     */
    protected function getArticle()
    {
        if($this->article == null) {

        }
        return $this->article;
    }

    protected function getData()
    {
        if(empty($this->data)) {
            $this->data = array(
                'id' => 0,
                'indexer_id' => 'PMID24837888',
                'journal_issue' => array(
                    'pub_status' => true,
                    'volume' => '89',
                    'issue'  => '2',
                    'pages'  => '257-9',
                    'pub_date' => array(
                        'year'  => '2014',
                        'month' => 'Jun',
                        'day'   => '1'
                    )
                ),
                'journal' => array(
                    'id'    => 0,
                    'issn'  => '1879-355X',
                    'title' => 'International journal of radiation oncology, biology, physics',
                    'abbr'  => 'Int J Radiat Oncol Biol Phys'
                ),
                'title' => 'Better radiation therapy for cervix cancer would save lives.',
                'abstract' => array(),
                'authors'  => array(
                    array(
                        'id' => 0,
                        'last_name' => 'Thomas',
                        'fore_name' => 'Gillian M',
                        'initials'  => 'GM'
                    )
                ),
                'keywords' => array()
            );
        }
        return $this->data;
    }

    public function testArticleWithUnpublishedArticle()
    {
        $data = array(
            'id' => 0,
            'indexer_id' => 'PMID24841923',
            'journal_issue' => array(
                'pub_status' => false,
                'volume' => '',
                'issue'  => '',
                'pages'  => '',
                'pub_date' => array(
                    'year'  => '2014',
                    'month' => 'May',
                    'day'   => '19'
                )
            ),
            'journal' => array(
                'id' => 0,
                'issn' => '1752-8062',
                'title' => 'Clinical and translational science',
                'abbr' => 'Clin Transl Sci'
            ),
            'title' => 'Human Papillomavirus Vaccination: A Case Study in Translational Science.',
            'abstract' => array(
                array(
                    'heading' => '',
                    'para'    => 'Each year 610,000 cases of anogenital and oropharyngeal cancers caused by human papillomavirus (HPV) occur worldwide. HPV vaccination represents a promising opportunity to prevent cancer on a global scale. The vaccine\'s story dates back to discoveries in chickens at the beginning of the 20th century with evidence that a cell-free filtrate could transmit the propensity to grow cancers. Later, studies with similarly derived filtrates from mammalian tumors showed that hosts could develop immunity to subsequent exposures. Epidemiologic studies linked cervical cancer to members of a family of viruses that cause papillomatosis and common warts. This led to work with DNA hybridization demonstrating a causal relationship. The formation of virus-like particles from viral capsid proteins led to the development of models for safe and effective vaccines. While much work remains with the acceptance of universal vaccination, the HPV vaccines Gardasil and Cervarix thus represent a century of successful translational research.'
                )
            ),
            'authors' => array(
                array(
                    'id' => 0,
                    'last_name' => 'Palmer',
                    'fore_name' => 'Allyson K',
                    'initials'  => 'AK'
                ),
                array(
                    'id' => 0,
                    'last_name' => 'Harris',
                    'fore_name' => 'Antoneicka L',
                    'initials'  => 'AL'
                ),
                array(
                    'id' => 0,
                    'last_name' => 'Jacobson',
                    'fore_name' => 'Robert M',
                    'initials'  => 'RM'
                )
            )
        );

        $article = Article::createArticleFromArray($data);
        $this->assertEquals(false, $article->getJournalIssue()->getPubStatus());
    }
}