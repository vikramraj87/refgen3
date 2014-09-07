<?php
namespace ArticleTest\Table;

use ArticleTest\DbTestCase;

use Article\Table\AbstractParaTable,
    Article\Table\AuthorTable,
    Article\Table\JournalTable,
    Article\Table\ArticleTable;

use Article\Entity\Article,
    Article\Entity\AbstractPara,
    Article\Entity\Author,
    Article\Entity\Journal,
    Article\Entity\JournalIssue,
    Article\Entity\PubDate;

/**
 * Class ArticleTableTest.
 * TODO: Use mocks to test the event of saving author
 * and abstract data getting failed
 *
 * @package ArticleTest\Table
 */
class ArticleTableTest extends DbTestCase
{
    /** @var \Article\Table\ArticleTable */
    protected $table;

    protected function setUp()
    {
        $conn = $this->getConnection();
        $conn->getConnection()->query('set foreign_key_checks=0');
        $adapter = $this->getAdapter();

        $journalTable = new JournalTable();
        $journalTable->setDbAdapter($adapter);

        $authorTable = new AuthorTable();
        $authorTable->setDbAdapter($adapter);

        $abstractTable = new AbstractParaTable();
        $abstractTable->setDbAdapter($adapter);

        $this->table = new ArticleTable();
        $this->table->setDbAdapter($adapter);
        $this->table->setAbstractParaTable($abstractTable);
        $this->table->setAuthorTable($authorTable);
        $this->table->setJournalTable($journalTable);

        parent::setUp();
        $conn->getConnection()->query('set foreign_key_checks=1');
    }

    public function testFetchById()
    {
        $article = $this->table->fetchArticleById(243);

        // Testing article table data
        $this->assertEquals(243, $article->getId());
        $this->assertEquals('PMID16014764', $article->getIndexerId());
        $this->assertEquals('51', $article->getJournalIssue()->getVolume());
        $this->assertEquals('4', $article->getJournalIssue()->getIssue());
        $this->assertEquals('236-9', $article->getJournalIssue()->getPages());
        $this->assertEquals('2005', $article->getJournalIssue()->getPubDate()->getYear());
        $this->assertEquals('Aug', $article->getJournalIssue()->getPubDate()->getMonth());
        $this->assertEquals('', $article->getJournalIssue()->getPubDate()->getDay());
        $this->assertEquals('Pancytopenia in children: etiological profile.',
            $article->getTitle());
        $this->assertEquals(true, $article->getJournalIssue()->getPubStatus());

        // Testing journal table data
        $this->assertEquals(194, $article->getJournal()->getId());

        // Testing author table data
        $authors = [];
        foreach($article->getAuthors() as $author) {
            $authors[] = $author->getId();
        }
        $expected = [1576, 1577, 1578, 1579, 1580, 1581];
        $this->assertEquals($expected, $authors);

        // Testing abstract para table data
        $paras = [];
        foreach($article->getAbstract() as $para) {
            $paras[] = $para->getId();
        }
        $expected = [461];
        $this->assertEquals($expected, $paras);
    }

    public function testFetchByNonExistingId()
    {
        $this->assertNull($this->table->fetchArticleById(2000));
    }

    public function testCheckExistingArticle()
    {
        $journal = new Journal();
        $journal->setTitle('Journal of tropical pediatrics');
        $journal->setAbbr('J Trop Pediatr');
        $journal->setIssn('0142-6338');

        $pubDate = new PubDate();
        $pubDate->setDay('');
        $pubDate->setMonth('Aug');
        $pubDate->setYear('2005');

        $journalIssue = new JournalIssue();
        $journalIssue->setVolume('51');
        $journalIssue->setIssue('4');
        $journalIssue->setPages('236-9');
        $journalIssue->setPubDate($pubDate);
        $journalIssue->setPubStatus(true);

        $authorsData = [
            [1576, 'Bhatnagar', 'Shishir Kumar', 'SK'],
            [1577, 'Chandra', 'Jagdish', 'J'],
            [1578, 'Narayan', 'Shashi', 'S'],
            [1579, 'Sharma', 'Sunita', 'S'],
            [1580, 'Singh', 'Varinder', 'V'],
            [1581, 'Dutta', 'Ashok Kumar', 'AK']
        ];

        $authors = [];
        foreach($authorsData as $authorData) {
            $author = new Author();
            $author->setLastName($authorData[1]);
            $author->setForeName($authorData[2]);
            $author->setInitials($authorData[3]);
            $authors[] = $author;
        }

        $para = new AbstractPara();
        $para->setHeading('');
        $para->setNlmCategory('');
        $para->setPara('Pancytopenia is a common occurrence in pediatric patients. Though acute leukemias and bone marrow failure syndromes are usual causes of pancytopenia, etiologies such as infections and megaloblastic anemia also contribute. The aim of this study was to evaluate the clinico-hematological profile of varying degrees of childhood cytopenias with special reference to the non-malignant presentations. This is a retrospective study carried out in a tertiary care children\'s hospital. We retrospectively analyzed 109 pediatric patients who presented with pancytopenia for different etiologies. Acute leukemia (including ALL, AML and myelodysplastic syndrome) and aplastic anemia accounted for 21 per cent and 20 per cent cases respectively. Megaloblastic anemia was found in 31 (28.4 per cent) patients and was single most common etiological factor. Severe thrombocytopenia (platelet < or = 20 x 10(9)/l) occurred in 25.2 per cent of these patients. Various skin and mucosal bleeding occurred in 45.1 per cent of patients with megaloblastic anemia. Infections accounted for 23 (21 per cent) patients who presented with pancytopenia. Amongst infections, enteric fever occurred in 30 per cent patients. Malaria, kala-azar and bacterial infections were other causes of pancytopenia at presentation. The study focuses on identifying easily treatable causes such as megaloblastic anemia and infections presenting with pancytopenia. These conditions though look ominous but respond rapidly to effective therapy.');
        $abstracts = [$para];


        $article = new Article();
        $article->setIndexerId('PMID16014764');
        $article->setJournalIssue($journalIssue);
        $article->setJournal($journal);
        $article->setAuthors($authors);
        $article->setAbstract($abstracts);
        $article->setTitle('Pancytopenia in children: etiological profile.');

        $actual = $this->table->checkArticle($article);
        $expected = $article;

        // Setting the ids of various parts through reference
        reset($authorsData);
        foreach($authors as &$author) {
            $author->setId(current($authorsData)[0]);
            next($authorsData);
        }
        unset($author);
        $para->setId(461);
        $journal->setId(194);
        $article->setId(243);

        $this->assertEquals($expected, $actual);
    }

    public function testCheckingNonExistingArticle()
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

        $expected->setId(310);
        $expected->getJournal()->setId(243);
        $author1->setId(1942);
        $author2->setId(1943);
        $author3->setId(1944);
        $author4->setId(1945);
        $author5->setId(1946);

        $para->setId(597);

        $this->assertEquals($expected, $result);
    }

    public function testFetchByNonExistingIndexerId()
    {
        $this->assertNull($this->table->fetchArticleByIndexerId('PMID98765432'));
    }

    public function testFetchByIndexerId()
    {
        $article = $this->table->fetchArticleByIndexerId('PMID16014764');

        // Testing article table data
        $this->assertEquals(243, $article->getId());
        $this->assertEquals('PMID16014764', $article->getIndexerId());
        $this->assertEquals('51', $article->getJournalIssue()->getVolume());
        $this->assertEquals('4', $article->getJournalIssue()->getIssue());
        $this->assertEquals('236-9', $article->getJournalIssue()->getPages());
        $this->assertEquals('2005', $article->getJournalIssue()->getPubDate()->getYear());
        $this->assertEquals('Aug', $article->getJournalIssue()->getPubDate()->getMonth());
        $this->assertEquals('', $article->getJournalIssue()->getPubDate()->getDay());
        $this->assertEquals('Pancytopenia in children: etiological profile.',
            $article->getTitle());
        $this->assertEquals(true, $article->getJournalIssue()->getPubStatus());

        // Testing journal table data
        $this->assertEquals(194, $article->getJournal()->getId());

        // Testing author table data
        $authors = [];
        foreach($article->getAuthors() as $author) {
            $authors[] = $author->getId();
        }
        $expected = [1576, 1577, 1578, 1579, 1580, 1581];
        $this->assertEquals($expected, $authors);

        // Testing abstract para table data
        $paras = [];
        foreach($article->getAbstract() as $para) {
            $paras[] = $para->getId();
        }
        $expected = [461];
        $this->assertEquals($expected, $paras);
    }

    public function testCheckingArticles()
    {
        $ids = array(
            '16014764',
            '12348765',
            '12345678',
            '25087944',
            '25087046'
        );
        array_walk($ids, function(&$value, $key) {
            $value = 'PMID' . $value;
        });
        $articles = $this->table->checkArticles($ids);
        $expectedIds = array(
            'PMID16014764',
            'PMID25087944',
            'PMID25087046'
        );
        $expected = array();
        foreach($expectedIds as $id) {
            $expected[$id] = $this->table->fetchArticleByIndexerId($id);
        }
        $this->assertEquals($expected, $articles);
    }

    public function testFetchArticlesByIds()
    {
        $ids = [
            1,2,3,246,244,245,243,247,248,300
        ];
        $articles = $this->table->fetchArticlesByIds($ids);
        $this->assertEquals(5, count($articles));
        $expected = [
            'PMID25036370' => 1,
            'PMID18460245' => 246,
            'PMID16014764' => 243,
            'PMID2055619'  => 248,
            'PMID25088681' => 300
        ];
        foreach($articles as $indexerId => $article) {
            $this->assertEquals(key($expected), $indexerId);
            $this->assertEquals(current($expected), $article->getId());
            next($expected);
        }
    }
} 