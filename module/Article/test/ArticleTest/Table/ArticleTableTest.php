<?php
/**
 * Created by PhpStorm.
 * User: vikramraj
 * Date: 14/06/14
 * Time: 10:12 PM
 */

namespace ArticleTest\Table;

use ArticleTest\DbTestCase;
use Article\Table\ArticleTable;
use Article\Entity\Article,
    Article\Entity\AbstractPara,
    Article\Entity\Author,
    Article\Entity\Journal,
    Article\Entity\JournalIssue,
    Article\Entity\PubDate;

class ArticleTableTest extends DbTestCase
{
    /** @var \Article\Table\ArticleTable */
    protected $table;

    protected function setUp()
    {
        $conn = $this->getConnection();
        $conn->getConnection()->query('set foreign_key_checks=0');
        $adapter = $this->getAdapter();
        $this->table = new ArticleTable();
        $this->table->setDbAdapter($adapter);
        parent::setUp();
        $conn->getConnection()->query('set foreign_key_checks=1');
    }

    public function testFetchById()
    {
        $expected = new Article();
        $expected->setId(1);
        $expected->setIndexerId('PMID24914885');

        $pubDate = new PubDate();
        $pubDate->setDay('9');
        $pubDate->setMonth('Jun');
        $pubDate->setYear('2014');
        $journalIssue = new JournalIssue();
        $journalIssue->setPubStatus(false);
        $journalIssue->setVolume('');
        $journalIssue->setIssue('');
        $journalIssue->setPages('');
        $journalIssue->setPubDate($pubDate);

        $expected->setJournalIssue($journalIssue);
        $expected->setTitle('Variation in Apoptotic Gene Expression in Cervical Cancer Through Oligonucleotide Microarray Profiling.');

        $journalData = array(
            'id'    => 1,
            'issn'  => '1526-0976',
            'title' => 'Journal of lower genital tract disease',
            'abbr'  => 'J Low Genit Tract Dis'
        );
        $journal = Journal::createFromArray($journalData);
        $expected->setJournal($journal);

        $authorsData = array(
            array(
                'id'        => 1,
                'last_name' => 'Zhu',
                'fore_name' => 'Ming-Yue',
                'initials'  => 'MY'
            ),
            array(
                'id'        => 2,
                'last_name' => 'Chen',
                'fore_name' => 'Fan',
                'initials'  => 'F'
            ),
            array(
                'id'        => 3,
                'last_name' => 'Niyazi',
                'fore_name' => 'Mayinuer',
                'initials'  => 'M'
            ),
            array(
                'id'        => 4,
                'last_name' => 'Sui',
                'fore_name' => 'Shuang',
                'initials'  => 'S'
            ),
            array(
                'id'        => 5,
                'last_name' => 'Gao',
                'fore_name' => 'Dong-Mei',
                'initials'  => 'DM'
            )
        );
        $authors = array();
        foreach($authorsData as $authorData) {
            $authors[] = Author::createFromArray($authorData);
        }

        $abstractData = array(
            array(
                'id'           => 1,
                'heading'      => 'Objective',
                'nlm_category' => 'Objective',
                'para'         => 'The current study aimed to investigate the molecular basis of cervical cancer development using a microarray to identify the differentially expressed genes. This study also aimed to detect apoptosis genes and proteins to find those genes most aberrantly expressed in cervical cancer and to explore the cause of Uighur cervical cancer.'
            ),
            array(
                'id'           => 2,
                'heading'      => 'Methods',
                'nlm_category' => 'Methods',
                'para'         => 'An analysis of gene expression profiles obtained from cervical cancer cases was performed. Total RNA was prepared from 10 samples of cervical carcinoma and normal cervix and was hybridized to Affymetrix oligonucleotide microarrays with probe sets complementary to more than 20,000 transcripts. Several genes of the apoptosis pathway, which were differentially regulated, included BCL2, BCLXL, and c-IAP1. These were validated by quantitative reverse transcription-polymerase chain reaction and immunohistochemical staining on an independent set of cancer and control specimens.'
            ),
            array(
                'id'           => 3,
                'heading'      => 'Results',
                'nlm_category' => 'Results',
                'para'         => 'Unsupervised hierarchical clustering of the expression data readily distinguished the normal cervix from cancer. Supervised analysis of gene expression data identified 1,326 and 1,432 genes that were upregulated and downregulated, respectively; a set of genes belonging to the apoptosis pathways were upregulated or downregulated in patients with cervical cancer. BCL2, BCLXL, and c-IAP1 were found to be upregulated in late-stage cancer compared to early-stage cancer.'
            ),
            array(
                'id'           => 4,
                'heading'      => 'Conclusions',
                'nlm_category' => 'Conclusions',
                'para'         => 'These findings provide a new understanding of the gene expression profile in cervical cancer. BCL2, BCLXL, and c-IAP1 might be involved in cancer progression. The pathway analysis of expression data showed that the BCL2, BCLXL, and c-IAP1 genes were coordinately differentially regulated between cancer and normal cases. Our results may serve as basis for further development of biomarkers for the diagnosis and treatment of cervical cancer.'
            )
        );
        $abstract = array();
        foreach($abstractData as $para) {
            $abstract[] = AbstractPara::createFromArray($para);
        }

        $expected->setAuthors($authors);
        $expected->setAbstract($abstract);
        $this->assertEquals($expected, $this->table->fetchArticleById(1));


        $expected2 = new Article();
        $expected2->setId(4);
        $expected2->setIndexerId('PMID24837888');
        $expected2->setTitle('Better radiation therapy for cervix cancer would save lives.');

        $pubDate2 = new PubDate();
        $pubDate2->setYear('2014');
        $pubDate2->setMonth('Jun');
        $pubDate2->setDay('1');

        $journalIssue2 = new JournalIssue();
        $journalIssue2->setVolume('89');
        $journalIssue2->setIssue('2');
        $journalIssue2->setPages('257-9');
        $journalIssue2->setPubDate($pubDate2);
        $journalIssue2->setPubStatus(true);
        $expected2->setJournalIssue($journalIssue2);

        $journal2 = new Journal();
        $journal2->setId(3);
        $journal2->setIssn('1879-355X');
        $journal2->setTitle('International journal of radiation oncology, biology, physics');
        $journal2->setAbbr('Int J Radiat Oncol Biol Phys');
        $expected2->setJournal($journal2);

        $author = new Author();
        $author->setId(19);
        $author->setForeName('Gillian M');
        $author->setLastName('Thomas');
        $author->setInitials('GM');
        $expected2->setAuthors(array($author));

        $expected2->setAbstract(array());
        $this->assertEquals($expected2, $this->table->fetchArticleById(4));
    }

    public function testFetchByNonExistentId()
    {
        $this->assertEquals(null, $this->table->fetchArticleById(1001));
    }

    public function testCheckNonExistingArticle()
    {
        $expected = new Article();

        $pubDate = new PubDate();
        $pubDate->setDay('');
        $pubDate->setMonth('Oct');
        $pubDate->setYear('2002');

        $journalIssue = new JournalIssue();
        $journalIssue->setIssue('4');
        $journalIssue->setVolume('50');
        $journalIssue->setPages('461-7');
        $journalIssue->setPubDate($pubDate);
        $journalIssue->setPubStatus(true);

        $journal = new Journal();
        $journal->setTitle('The Journal of antimicrobial chemotherapy');
        $journal->setAbbr('J Antimicrob Chemother');
        $journal->setIssn('0305-7453');

        $para = new AbstractPara();
        $para->setHeading('');
        $para->setNlmCategory('');
        $para->setPara('Lactoferricin B is a cationic antimicrobial peptide derived from the N-terminal part of bovine lactoferrin. The effect of bacterial proteases on the antibacterial activity of lactoferricin B towards Escherichia coli and Staphylococcus aureus was investigated using various protease inhibitors and protease-deficient E. coli mutants. Sodium-EDTA, a metalloprotease inhibitor, was the most efficient inhibitors in both species, but combinations of sodium-EDTA with other types of protease inhibitor gave a synergic effect. The results indicate that several groups of proteases are involved in resistance to lactoferricin B in both E. coli and S. aureus. We also report that genetic inactivation of the heat shock-induced serine protease DegP increased the susceptibility to lactoferricin B in E. coli, suggesting that this protease, at least, is involved in reduced susceptibility to lactoferricin B.');
        $abstract = array($para);

        $author1 = new Author();
        $author1->setLastName('Ulvatne');
        $author1->setForeName('Hilde');
        $author1->setInitials('H');

        $author2 = new Author();
        $author2->setLastName('Haukland');
        $author2->setForeName('Hanne Husom');
        $author2->setInitials('HH');

        $author3 = new Author();
        $author3->setLastName('Samuelsen');
        $author3->setForeName('Ørjan');
        $author3->setInitials('Ø');

        $author4 = new Author();
        $author4->setLastName('Krämer');
        $author4->setForeName('Manuela');
        $author4->setInitials('M');

        $author5 = new Author();
        $author5->setLastName('Vorland');
        $author5->setForeName('Lars H');
        $author5->setInitials('LH');

        $authors = array($author1, $author2, $author3, $author4, $author5);

        $expected->setIndexerId('PMID12356789');
        $expected->setJournalIssue($journalIssue);
        $expected->setTitle('Proteases in Escherichia coli and Staphylococcus aureus confer reduced susceptibility to lactoferricin B.');
        $expected->setJournal($journal);
        $expected->setAbstract($abstract);
        $expected->setAuthors($authors);

        $result = $this->table->checkArticle($expected);

        $expected->setId(5);
        $expected->getJournal()->setId(4);
        $author1->setId(20);
        $author2->setId(21);
        $author3->setId(22);
        $author4->setId(23);
        $author5->setId(24);
        $expected->setAuthors(array($author1, $author2, $author3, $author4, $author5));

        $para->setId(10);
        $expected->setAbstract(array($para));

        $this->assertEquals($expected, $result);
    }

    public function testCheckExistingArticle()
    {
        $expected = new Article();
        $expected->setIndexerId('PMID24914885');

        $pubDate = new PubDate();
        $pubDate->setDay('9');
        $pubDate->setMonth('Jun');
        $pubDate->setYear('2014');
        $journalIssue = new JournalIssue();
        $journalIssue->setPubStatus(false);
        $journalIssue->setVolume('');
        $journalIssue->setIssue('');
        $journalIssue->setPages('');
        $journalIssue->setPubDate($pubDate);

        $expected->setJournalIssue($journalIssue);
        $expected->setTitle('Variation in Apoptotic Gene Expression in Cervical Cancer Through Oligonucleotide Microarray Profiling.');

        $journalData = array(
            'id'    => 1,
            'issn'  => '1526-0976',
            'title' => 'Journal of lower genital tract disease',
            'abbr'  => 'J Low Genit Tract Dis'
        );
        $journal = Journal::createFromArray($journalData);
        $expected->setJournal($journal);

        $authorsData = array(
            array(
                'last_name' => 'Zhu',
                'fore_name' => 'Ming-Yue',
                'initials'  => 'MY'
            ),
            array(
                'last_name' => 'Chen',
                'fore_name' => 'Fan',
                'initials'  => 'F'
            ),
            array(
                'last_name' => 'Niyazi',
                'fore_name' => 'Mayinuer',
                'initials'  => 'M'
            ),
            array(
                'last_name' => 'Sui',
                'fore_name' => 'Shuang',
                'initials'  => 'S'
            ),
            array(
                'last_name' => 'Gao',
                'fore_name' => 'Dong-Mei',
                'initials'  => 'DM'
            )
        );
        $authors = array();
        foreach($authorsData as $authorData) {
            $authors[] = Author::createFromArray($authorData);
        }

        $abstractData = array(
            array(
                'heading'      => 'Objective',
                'nlm_category' => 'Objective',
                'para'         => 'The current study aimed to investigate the molecular basis of cervical cancer development using a microarray to identify the differentially expressed genes. This study also aimed to detect apoptosis genes and proteins to find those genes most aberrantly expressed in cervical cancer and to explore the cause of Uighur cervical cancer.'
            ),
            array(
                'heading'      => 'Methods',
                'nlm_category' => 'Methods',
                'para'         => 'An analysis of gene expression profiles obtained from cervical cancer cases was performed. Total RNA was prepared from 10 samples of cervical carcinoma and normal cervix and was hybridized to Affymetrix oligonucleotide microarrays with probe sets complementary to more than 20,000 transcripts. Several genes of the apoptosis pathway, which were differentially regulated, included BCL2, BCLXL, and c-IAP1. These were validated by quantitative reverse transcription-polymerase chain reaction and immunohistochemical staining on an independent set of cancer and control specimens.'
            ),
            array(
                'heading'      => 'Results',
                'nlm_category' => 'Results',
                'para'         => 'Unsupervised hierarchical clustering of the expression data readily distinguished the normal cervix from cancer. Supervised analysis of gene expression data identified 1,326 and 1,432 genes that were upregulated and downregulated, respectively; a set of genes belonging to the apoptosis pathways were upregulated or downregulated in patients with cervical cancer. BCL2, BCLXL, and c-IAP1 were found to be upregulated in late-stage cancer compared to early-stage cancer.'
            ),
            array(
                'heading'      => 'Conclusions',
                'nlm_category' => 'Conclusions',
                'para'         => 'These findings provide a new understanding of the gene expression profile in cervical cancer. BCL2, BCLXL, and c-IAP1 might be involved in cancer progression. The pathway analysis of expression data showed that the BCL2, BCLXL, and c-IAP1 genes were coordinately differentially regulated between cancer and normal cases. Our results may serve as basis for further development of biomarkers for the diagnosis and treatment of cervical cancer.'
            )
        );
        $abstract = array();
        foreach($abstractData as $para) {
            $abstract[] = AbstractPara::createFromArray($para);
        }

        $expected->setAuthors($authors);
        $expected->setAbstract($abstract);
        $result = $this->table->checkArticle($expected);

        $abstractData[0]['id'] = 1;
        $abstractData[1]['id'] = 2;
        $abstractData[2]['id'] = 3;
        $abstractData[3]['id'] = 4;
        $abstract = array();
        foreach($abstractData as $para) {
            $abstract[] = AbstractPara::createFromArray($para);
        }

        $authorsData[0]['id'] = 1;
        $authorsData[1]['id'] = 2;
        $authorsData[2]['id'] = 3;
        $authorsData[3]['id'] = 4;
        $authorsData[4]['id'] = 5;
        $authors = array();
        foreach($authorsData as $authorData) {
            $authors[] = Author::createFromArray($authorData);
        }

        $expected->setId(1);
        $expected->setAbstract($abstract);
        $expected->setAuthors($authors);

        $this->assertEquals($expected, $result);

    }

    public function testFetchArticleByIndexerId()
    {
        $expected = new Article();
        $expected->setId(1);
        $expected->setIndexerId('PMID24914885');

        $pubDate = new PubDate();
        $pubDate->setDay('9');
        $pubDate->setMonth('Jun');
        $pubDate->setYear('2014');
        $journalIssue = new JournalIssue();
        $journalIssue->setPubStatus(false);
        $journalIssue->setVolume('');
        $journalIssue->setIssue('');
        $journalIssue->setPages('');
        $journalIssue->setPubDate($pubDate);

        $expected->setJournalIssue($journalIssue);
        $expected->setTitle('Variation in Apoptotic Gene Expression in Cervical Cancer Through Oligonucleotide Microarray Profiling.');

        $journalData = array(
            'id'    => 1,
            'issn'  => '1526-0976',
            'title' => 'Journal of lower genital tract disease',
            'abbr'  => 'J Low Genit Tract Dis'
        );
        $journal = Journal::createFromArray($journalData);
        $expected->setJournal($journal);

        $authorsData = array(
            array(
                'id'        => 1,
                'last_name' => 'Zhu',
                'fore_name' => 'Ming-Yue',
                'initials'  => 'MY'
            ),
            array(
                'id'        => 2,
                'last_name' => 'Chen',
                'fore_name' => 'Fan',
                'initials'  => 'F'
            ),
            array(
                'id'        => 3,
                'last_name' => 'Niyazi',
                'fore_name' => 'Mayinuer',
                'initials'  => 'M'
            ),
            array(
                'id'        => 4,
                'last_name' => 'Sui',
                'fore_name' => 'Shuang',
                'initials'  => 'S'
            ),
            array(
                'id'        => 5,
                'last_name' => 'Gao',
                'fore_name' => 'Dong-Mei',
                'initials'  => 'DM'
            )
        );
        $authors = array();
        foreach($authorsData as $authorData) {
            $authors[] = Author::createFromArray($authorData);
        }

        $abstractData = array(
            array(
                'id'           => 1,
                'heading'      => 'Objective',
                'nlm_category' => 'Objective',
                'para'         => 'The current study aimed to investigate the molecular basis of cervical cancer development using a microarray to identify the differentially expressed genes. This study also aimed to detect apoptosis genes and proteins to find those genes most aberrantly expressed in cervical cancer and to explore the cause of Uighur cervical cancer.'
            ),
            array(
                'id'           => 2,
                'heading'      => 'Methods',
                'nlm_category' => 'Methods',
                'para'         => 'An analysis of gene expression profiles obtained from cervical cancer cases was performed. Total RNA was prepared from 10 samples of cervical carcinoma and normal cervix and was hybridized to Affymetrix oligonucleotide microarrays with probe sets complementary to more than 20,000 transcripts. Several genes of the apoptosis pathway, which were differentially regulated, included BCL2, BCLXL, and c-IAP1. These were validated by quantitative reverse transcription-polymerase chain reaction and immunohistochemical staining on an independent set of cancer and control specimens.'
            ),
            array(
                'id'           => 3,
                'heading'      => 'Results',
                'nlm_category' => 'Results',
                'para'         => 'Unsupervised hierarchical clustering of the expression data readily distinguished the normal cervix from cancer. Supervised analysis of gene expression data identified 1,326 and 1,432 genes that were upregulated and downregulated, respectively; a set of genes belonging to the apoptosis pathways were upregulated or downregulated in patients with cervical cancer. BCL2, BCLXL, and c-IAP1 were found to be upregulated in late-stage cancer compared to early-stage cancer.'
            ),
            array(
                'id'           => 4,
                'heading'      => 'Conclusions',
                'nlm_category' => 'Conclusions',
                'para'         => 'These findings provide a new understanding of the gene expression profile in cervical cancer. BCL2, BCLXL, and c-IAP1 might be involved in cancer progression. The pathway analysis of expression data showed that the BCL2, BCLXL, and c-IAP1 genes were coordinately differentially regulated between cancer and normal cases. Our results may serve as basis for further development of biomarkers for the diagnosis and treatment of cervical cancer.'
            )
        );
        $abstract = array();
        foreach($abstractData as $para) {
            $abstract[] = AbstractPara::createFromArray($para);
        }

        $expected->setAuthors($authors);
        $expected->setAbstract($abstract);

        $this->assertEquals($expected, $this->table->fetchArticleByIndexerId('PMID24914885'));
    }

    public function testFetchArticleByNonExistingIndexerId()
    {
        $this->assertEquals(null, $this->table->fetchArticleByIndexerId('PMID88776655'));
    }

    public function testCheckingArticles()
    {
        $ids = array(
            '24914885',
            '12348765',
            '12345678',
            '24837888',
            '24914887'
        );
        array_walk($ids, function(&$value, $key) {
            $value = 'PMID' . $value;
        });
        $articles = $this->table->checkArticles($ids);
        $expectedIds = array(
            'PMID24914885',
            'PMID24837888',
            'PMID24914887'
        );
        $expected = array();
        foreach($expectedIds as $id) {
            $expected[$id] = $this->table->fetchArticleByIndexerId($id);
        }
        $this->assertEquals($expected, $articles);
    }
} 