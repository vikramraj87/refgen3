<?php
namespace ArticleTest\Table;

use ArticleTest\DbTestCase;
use Article\Table\AbstractParaTable;
use Article\Entity\AbstractPara;

class AbstractParaTableTest extends DbTestCase
{
    /** @var \Article\Table\AbstractParaTable */
    protected $table;

    protected function setUp()
    {
        $conn = $this->getConnection();
        $conn->getConnection()->query('set foreign_key_checks=0');
        $adapter = $this->getAdapter();
        $this->table = new AbstractParaTable();
        $this->table->setDbAdapter($adapter);
        parent::setUp();
        $conn->getConnection()->query('set foreign_key_checks=1');
    }

    public function testFetchByArticleId()
    {
        $data     = array(
            array(
                'id'           => 5,
                'heading'      => 'Objective',
                'nlm_category' => 'Objective',
                'para'         => 'To determine whether a quality assurance (QA) program using digital cervicography improved the performance of a visual inspection with acetic acid (VIA) to detect cervical intraepithelial neoplasia grade 2 or worse (CIN 2+) in HIV-infected women in Johannesburg, South Africa.'
            ),
            array(
                'id'           => 6,
                'heading'      => 'Materials and methods',
                'nlm_category' => 'Methods',
                'para'         => 'Visual inspection with acetic acid was performed among HIV-infected women, aged 18 to 65 years, in Johannesburg, South Africa. Nurses received 2 weeks of training on the VIA procedure. The VIA interpretation was performed in real time. The VIA results were then photographed using a retail available digital camera. A gynecologist and medical officer reviewed the VIA digital images within 2 weeks of the procedure. Colposcopic biopsy was performed on all women with positive VIA and 25% negative VIA results. Sensitivity and specificity of VIA for the detection of CIN 2+ were compared between the nurses and physicians at the beginning and at the end of the study.'
            ),
            array(
                'id'           => 7,
                'heading'      => 'Results',
                'nlm_category' => 'Results',
                'para'         => 'Positive VIA results were found in 541 (45%) of the 1,202 participating women. The sensitivity of VIA to predict CIN 2+ was improved from 65% to 75% (p = .001) with the addition of digital cervicography and specialist review. There was no statistical difference in the sensitivity of the VIA readings when comparing the first 600 participants to the final 593 participants between the nurses (p = .613) and physicians (p = .624).'
            ),
            array(
                'id'           => 8,
                'heading'      => 'Conclusions',
                'nlm_category' => 'Conclusions',
                'para'         => 'Quality assurance performed by specialists using digital cervicography improved the sensitivity of VIA. There was no difference in sensitivity in interpreting VIA between the beginning and the end of the study. Quality assurance should form a cornerstone of any VIA program to improve sensitivity in detecting CIN 2+ lesions.'
            )
        );
        $expected = $this->abstractFromArray($data);
        $this->assertEquals($expected, $this->table->fetchParasByArticleId(2));

        $data2     = array(
            array(
                'id'           => 9,
                'heading'      => '',
                'nlm_category' => 'Unlabelled',
                'para'         => 'Each year 610,000 cases of anogenital and oropharyngeal cancers caused by human papillomavirus (HPV) occur worldwide. HPV vaccination represents a promising opportunity to prevent cancer on a global scale. The vaccine\'s story dates back to discoveries in chickens at the beginning of the 20th century with evidence that a cell-free filtrate could transmit the propensity to grow cancers. Later, studies with similarly derived filtrates from mammalian tumors showed that hosts could develop immunity to subsequent exposures. Epidemiologic studies linked cervical cancer to members of a family of viruses that cause papillomatosis and common warts. This led to work with DNA hybridization demonstrating a causal relationship. The formation of virus-like particles from viral capsid proteins led to the development of models for safe and effective vaccines. While much work remains with the acceptance of universal vaccination, the HPV vaccines Gardasil and Cervarix thus represent a century of successful translational research.'
            )
        );
        $expected2 = $this->abstractFromArray($data2);
        $this->assertEquals($expected2, $this->table->fetchParasByArticleId(3));
    }

    public function testFetchByNonExistentArticleId()
    {
        $this->assertEquals(array(), $this->table->fetchParasByArticleId(1001));
    }

    public function testCreateAbstract()
    {
        $para1 = new AbstractPara();
        $para1->setHeading('Lorem');
        $para1->setNlmCategory('Lorem');
        $para1->setPara('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas leo nibh, tempus ullamcorper sagittis eu, elementum at nulla. In venenatis iaculis volutpat. Aliquam dignissim venenatis lectus, id varius odio posuere nec. Quisque rhoncus vestibulum erat id molestie. Etiam dictum dolor nulla, non tincidunt ligula dignissim in. Proin malesuada, augue quis posuere pellentesque, metus sem elementum magna, a molestie leo erat id felis. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Donec lectus mauris, facilisis vitae quam nec, suscipit luctus velit. Etiam mollis justo non orci dictum, vel rutrum metus condimentum. Pellentesque sed euismod neque. Nam.');

        $para2 = new AbstractPara();
        $para2->setHeading('Suspendisse');
        $para2->setNlmCategory('Suspend');
        $para2->setPara('Suspendisse interdum mauris sit amet sapien ultricies fringilla. Quisque enim augue, ultrices in lectus non, pulvinar tempus dui. Quisque eu diam eu sapien vehicula lobortis ac et ante. Duis congue sed urna vel vulputate. Cras augue lacus, molestie eu faucibus vitae, pharetra id orci. Mauris sit amet eros vitae nisi elementum volutpat. Nulla malesuada molestie ante vel ultrices. Proin ac varius tortor. Fusce ornare ornare est ac hendrerit. In nec justo ut dui semper aliquet ut et felis.');

        $result = $this->table->createAbstract(array($para1, $para2), 4);
        $para1->setId(10);
        $para2->setId(11);

        $this->assertEquals(true, $result);
        $abstract = $this->table->fetchParasByArticleId(4);
        $this->assertEquals(2, count($abstract));
        $this->assertEquals($para1, $abstract[0]);
        $this->assertEquals($para2, $abstract[1]);
    }

    public function testCreateAbstractFromEmptyArray()
    {
        $result = $this->table->createAbstract(array(), 4);
        $this->assertEquals(true, $result);

        $abstract = $this->table->fetchParasByArticleId(4);
        $this->assertEquals(0, count($abstract));
    }

    public function testDeleteAbstract()
    {
        $result = $this->table->deleteParasByArticleId(1);
        $this->assertEquals(true, $result);

        $abstract = $this->table->fetchParasByArticleId(1);
        $this->assertEquals(array(), $abstract);
    }

    public function testDeleteAbstractOfNonExistingArticle()
    {
        $result = $this->table->deleteParasByArticleId(1001);
        $this->assertEquals(true, $result);
    }

    private function abstractFromArray(array $data = array())
    {
        $abstract = array();
        foreach($data as $paraData) {
            $abstract[] = AbstractPara::createFromArray($paraData);
        }
        return $abstract;
    }
} 