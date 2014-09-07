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
        $para = $this->table->fetchParasByArticleId(243)[0];
        $this->assertEquals(461, $para->getId());
        $this->assertEquals('', $para->getHeading());
        $this->assertEquals('', $para->getNlmCategory());
        $this->assertEquals('Pancytopenia is a common occurrence in pediatric patients. Though acute leukemias and bone marrow failure syndromes are usual causes of pancytopenia, etiologies such as infections and megaloblastic anemia also contribute. The aim of this study was to evaluate the clinico-hematological profile of varying degrees of childhood cytopenias with special reference to the non-malignant presentations. This is a retrospective study carried out in a tertiary care children\'s hospital. We retrospectively analyzed 109 pediatric patients who presented with pancytopenia for different etiologies. Acute leukemia (including ALL, AML and myelodysplastic syndrome) and aplastic anemia accounted for 21 per cent and 20 per cent cases respectively. Megaloblastic anemia was found in 31 (28.4 per cent) patients and was single most common etiological factor. Severe thrombocytopenia (platelet < or = 20 x 10(9)/l) occurred in 25.2 per cent of these patients. Various skin and mucosal bleeding occurred in 45.1 per cent of patients with megaloblastic anemia. Infections accounted for 23 (21 per cent) patients who presented with pancytopenia. Amongst infections, enteric fever occurred in 30 per cent patients. Malaria, kala-azar and bacterial infections were other causes of pancytopenia at presentation. The study focuses on identifying easily treatable causes such as megaloblastic anemia and infections presenting with pancytopenia. These conditions though look ominous but respond rapidly to effective therapy.', $para->getPara());

        $expectedParas = [
            [
                'id' => 467,
                'heading' => 'Objective',
                'nlmCategory' => 'Objective',
                'para' => 'To determine the spectrum of pancytopenia with its frequency, common clinical presentation and etiology on the basis of bone marrow examination in children from 2 months to 15 years.'
            ], [
                'id' => 468,
                'heading' => 'Design',
                'nlmCategory' => 'Methods',
                'para' => 'Observational study.'
            ], [
                'id' => 469,
                'heading' => 'Place and duration of study',
                'nlmCategory' => 'Methods',
                'para' => 'Department of Paediatrics, Liaquat University of Medical and Health Sciences (LUMHS), Jamshoro, from October 2005 to March 2007.'
            ], [
                'id' => 470,
                'heading' => 'Patients and methods',
                'nlmCategory' => 'Methods',
                'para' => 'All patients aged 2 months to 15 years having pancytopenia were included. Patients beyond this age limits, already diagnosed cases of aplastic anemia and leukemia, clinical suspicion of genetic or constitutional pancytopenia, history of blood transfusion in recent past, and those not willing for either admission or bone marrow examination were excluded. History, physical and systemic examination and hematological parameters at presentation were recorded. Hematological profile included hemoglobin, total and differential leucocyte count, platelet count, reticulocyte count, peripheral smear and bone marrow aspiration/biopsy.'
            ], [
                'id' => 471,
                'heading' => 'Results',
                'nlmCategory' => 'Results',
                'para' => 'During the study period, out of the 7000 admissions in paediatric ward, 250 patients had pancytopenia on their peripheral blood smear (3.57%). Out of those, 230 patients were finally studied. Cause of pancytopenia was identified in 220 cases on the basis of bone marrow and other supportive investigations, while 10 cases remained undiagnosed. Most common was aplastic anemia (23.9%), megaloblastic anemia (13.04%), leukemia (13.05%), enteric fever (10.8%), malaria (8.69%) and sepsis (8.69%). Common clinical presentations were pallor, fever, petechial hemorrhages, visceromegaly and bleeding from nose and gastrointestinal tract.'
            ], [
                'id' => 472,
                'heading' => 'Conclusion',
                'nlmCategory' => 'Conclusions',
                'para' => 'Pancytopenia is a common occurrence in paediatric patients. Though acute leukemia and bone marrow failure were the usual causes of pancytopenia, infections and megaloblastic anemia are easily treatable and reversible.'
            ]
        ];
        $paras = $this->table->fetchParasByArticleId(246);
        $this->assertCount(6, $paras);
        reset($expectedParas);
        foreach($paras as $p) {
            $expected = current($expectedParas);
            $this->assertEquals($expected['id'], $p->getId());
            $this->assertEquals($expected['heading'], $p->getHeading());
            $this->assertEquals($expected['nlmCategory'], $p->getNlmCategory());
            $this->assertEquals($expected['para'], $p->getPara());
            next($expectedParas);
        }
    }

    public function testFetchByArticleIds()
    {
        $expectedParasList = [
            243 => [[
                'id' => 461,
                'heading' => '',
                'nlmCategory' => '',
                'para' => 'Pancytopenia is a common occurrence in pediatric patients. Though acute leukemias and bone marrow failure syndromes are usual causes of pancytopenia, etiologies such as infections and megaloblastic anemia also contribute. The aim of this study was to evaluate the clinico-hematological profile of varying degrees of childhood cytopenias with special reference to the non-malignant presentations. This is a retrospective study carried out in a tertiary care children\'s hospital. We retrospectively analyzed 109 pediatric patients who presented with pancytopenia for different etiologies. Acute leukemia (including ALL, AML and myelodysplastic syndrome) and aplastic anemia accounted for 21 per cent and 20 per cent cases respectively. Megaloblastic anemia was found in 31 (28.4 per cent) patients and was single most common etiological factor. Severe thrombocytopenia (platelet < or = 20 x 10(9)/l) occurred in 25.2 per cent of these patients. Various skin and mucosal bleeding occurred in 45.1 per cent of patients with megaloblastic anemia. Infections accounted for 23 (21 per cent) patients who presented with pancytopenia. Amongst infections, enteric fever occurred in 30 per cent patients. Malaria, kala-azar and bacterial infections were other causes of pancytopenia at presentation. The study focuses on identifying easily treatable causes such as megaloblastic anemia and infections presenting with pancytopenia. These conditions though look ominous but respond rapidly to effective therapy.'
            ]],
            246 => [
                [
                    'id' => 467,
                    'heading' => 'Objective',
                    'nlmCategory' => 'Objective',
                    'para' => 'To determine the spectrum of pancytopenia with its frequency, common clinical presentation and etiology on the basis of bone marrow examination in children from 2 months to 15 years.'
                ], [
                    'id' => 468,
                    'heading' => 'Design',
                    'nlmCategory' => 'Methods',
                    'para' => 'Observational study.'
                ], [
                    'id' => 469,
                    'heading' => 'Place and duration of study',
                    'nlmCategory' => 'Methods',
                    'para' => 'Department of Paediatrics, Liaquat University of Medical and Health Sciences (LUMHS), Jamshoro, from October 2005 to March 2007.'
                ], [
                    'id' => 470,
                    'heading' => 'Patients and methods',
                    'nlmCategory' => 'Methods',
                    'para' => 'All patients aged 2 months to 15 years having pancytopenia were included. Patients beyond this age limits, already diagnosed cases of aplastic anemia and leukemia, clinical suspicion of genetic or constitutional pancytopenia, history of blood transfusion in recent past, and those not willing for either admission or bone marrow examination were excluded. History, physical and systemic examination and hematological parameters at presentation were recorded. Hematological profile included hemoglobin, total and differential leucocyte count, platelet count, reticulocyte count, peripheral smear and bone marrow aspiration/biopsy.'
                ], [
                    'id' => 471,
                    'heading' => 'Results',
                    'nlmCategory' => 'Results',
                    'para' => 'During the study period, out of the 7000 admissions in paediatric ward, 250 patients had pancytopenia on their peripheral blood smear (3.57%). Out of those, 230 patients were finally studied. Cause of pancytopenia was identified in 220 cases on the basis of bone marrow and other supportive investigations, while 10 cases remained undiagnosed. Most common was aplastic anemia (23.9%), megaloblastic anemia (13.04%), leukemia (13.05%), enteric fever (10.8%), malaria (8.69%) and sepsis (8.69%). Common clinical presentations were pallor, fever, petechial hemorrhages, visceromegaly and bleeding from nose and gastrointestinal tract.'
                ], [
                    'id' => 472,
                    'heading' => 'Conclusion',
                    'nlmCategory' => 'Conclusions',
                    'para' => 'Pancytopenia is a common occurrence in paediatric patients. Though acute leukemia and bone marrow failure were the usual causes of pancytopenia, infections and megaloblastic anemia are easily treatable and reversible.'
                ]
            ]
        ];
        $paraList = $this->table->fetchParasByAArticleIds([243, 246]);
        $this->assertCount(2, $paraList);

        $paras = $paraList[243];
        $this->assertCount(1, $paras);
        $expectedParas = $expectedParasList[243];
        reset($expectedParas);
        foreach($paras as $p) {
            $expected = current($expectedParas);
            $this->assertEquals($expected['id'], $p->getId());
            $this->assertEquals($expected['heading'], $p->getHeading());
            $this->assertEquals($expected['nlmCategory'], $p->getNlmCategory());
            $this->assertEquals($expected['para'], $p->getPara());
            next($expectedParas);
        }

        $paras = $paraList[246];
        $this->assertCount(6, $paras);
        $expectedParas = $expectedParasList[246];
        reset($expectedParas);
        foreach($paras as $p) {
            /** @var $p AbstractPara */

            $expected = current($expectedParas);
            $this->assertEquals($expected['id'], $p->getId());
            $this->assertEquals($expected['heading'], $p->getHeading());
            $this->assertEquals($expected['nlmCategory'], $p->getNlmCategory());
            $this->assertEquals($expected['para'], $p->getPara());
            next($expectedParas);
        }
    }

    public function testFetchByNonExistentArticleIds()
    {
        $paras = $this->table->fetchParasByArticleId(300);
        $this->assertEmpty($paras);
    }

    public function testCreateAbstract()
    {
        $length = $this->getConnection()->getRowCount('article_abstract_paras');
        $parasData = [
            [
                'heading' => 'Place and duration of study',
                'nlmCategory' => 'Methods',
                'para' => 'Department of Paediatrics, Liaquat University of Medical and Health Sciences (LUMHS), Jamshoro, from October 2005 to March 2007.'
            ], [
                'heading' => 'Patients and methods',
                'nlmCategory' => 'Methods',
                'para' => 'All patients aged 2 months to 15 years having pancytopenia were included. Patients beyond this age limits, already diagnosed cases of aplastic anemia and leukemia, clinical suspicion of genetic or constitutional pancytopenia, history of blood transfusion in recent past, and those not willing for either admission or bone marrow examination were excluded. History, physical and systemic examination and hematological parameters at presentation were recorded. Hematological profile included hemoglobin, total and differential leucocyte count, platelet count, reticulocyte count, peripheral smear and bone marrow aspiration/biopsy.'
            ], [
                'heading' => 'Results',
                'nlmCategory' => 'Results',
                'para' => 'During the study period, out of the 7000 admissions in paediatric ward, 250 patients had pancytopenia on their peripheral blood smear (3.57%). Out of those, 230 patients were finally studied. Cause of pancytopenia was identified in 220 cases on the basis of bone marrow and other supportive investigations, while 10 cases remained undiagnosed. Most common was aplastic anemia (23.9%), megaloblastic anemia (13.04%), leukemia (13.05%), enteric fever (10.8%), malaria (8.69%) and sepsis (8.69%). Common clinical presentations were pallor, fever, petechial hemorrhages, visceromegaly and bleeding from nose and gastrointestinal tract.'
            ], [
                'heading' => 'Conclusion',
                'nlmCategory' => 'Conclusions',
                'para' => 'Pancytopenia is a common occurrence in paediatric patients. Though acute leukemia and bone marrow failure were the usual causes of pancytopenia, infections and megaloblastic anemia are easily treatable and reversible.'
            ]
        ];

        $paras = [];
        foreach($parasData as $paraData) {
            $p = new AbstractPara();
            $p->setHeading($paraData['heading']);
            $p->setNlmCategory($paraData['nlmCategory']);
            $p->setPara($paraData['para']);
            $paras[] = $p;
        }
        $this->table->createAbstract($paras, 300);
        $this->assertEquals($length + 4, $this->getConnection()->getRowCount('article_abstract_paras'));
        $pdo = $this->getConnection()->getConnection();
        $st = $pdo->prepare('SELECT * FROM article_abstract_paras WHERE article_id = 300');
        $st->execute();
        $data = $st->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertCount(4, $data);
        $i = 0;
        foreach($data as $p) {
            $expected = $parasData[$i];
            $this->assertTrue($p['id'] > 0);
            $this->assertEquals($i + 1, $p['position']);
            $this->assertEquals($expected['heading'], $p['heading']);
            $this->assertEquals($expected['nlmCategory'], $p['nlm_category']);
            $this->assertEquals($expected['para'], $p['para']);
            $i++;
        }
    }

    public function testCreateAbstractWithEmptyArray()
    {
        $length = $this->getConnection()->getRowCount('article_abstract_paras');
        $this->table->createAbstract([], 300);
        $this->assertEquals($length, $this->getConnection()->getRowCount('article_abstract_paras'));
    }

    /**
     * @expectedException \Zend\Db\Adapter\Exception\InvalidQueryException
     */
    public function testCreateAbstractWithInvalidConstraints()
    {
        $length = $this->getConnection()->getRowCount('article_abstract_paras');
        $parasData = [
            [
                'heading' => 'Place and duration of study',
                'nlmCategory' => 'Methods',
                'para' => 'Department of Paediatrics, Liaquat University of Medical and Health Sciences (LUMHS), Jamshoro, from October 2005 to March 2007.'
            ], [
                'heading' => 'Patients and methods',
                'nlmCategory' => 'Methods',
                'para' => 'All patients aged 2 months to 15 years having pancytopenia were included. Patients beyond this age limits, already diagnosed cases of aplastic anemia and leukemia, clinical suspicion of genetic or constitutional pancytopenia, history of blood transfusion in recent past, and those not willing for either admission or bone marrow examination were excluded. History, physical and systemic examination and hematological parameters at presentation were recorded. Hematological profile included hemoglobin, total and differential leucocyte count, platelet count, reticulocyte count, peripheral smear and bone marrow aspiration/biopsy.'
            ], [
                'heading' => 'Results',
                'nlmCategory' => 'Results',
                'para' => 'During the study period, out of the 7000 admissions in paediatric ward, 250 patients had pancytopenia on their peripheral blood smear (3.57%). Out of those, 230 patients were finally studied. Cause of pancytopenia was identified in 220 cases on the basis of bone marrow and other supportive investigations, while 10 cases remained undiagnosed. Most common was aplastic anemia (23.9%), megaloblastic anemia (13.04%), leukemia (13.05%), enteric fever (10.8%), malaria (8.69%) and sepsis (8.69%). Common clinical presentations were pallor, fever, petechial hemorrhages, visceromegaly and bleeding from nose and gastrointestinal tract.'
            ], [
                'heading' => 'Conclusion',
                'nlmCategory' => 'Conclusions',
                'para' => 'Pancytopenia is a common occurrence in paediatric patients. Though acute leukemia and bone marrow failure were the usual causes of pancytopenia, infections and megaloblastic anemia are easily treatable and reversible.'
            ]
        ];
        $paras = [];
        foreach($parasData as $paraData) {
            $p = new AbstractPara();
            $p->setHeading($paraData['heading']);
            $p->setNlmCategory($paraData['nlmCategory']);
            $p->setPara($paraData['para']);
            $paras[] = $p;
        }
        $this->table->createAbstract($paras, 1000);
        $this->assertEquals($length, $this->getConnection()->getRowCount('article_abstract_paras'));
    }

    public function testDeleteAbstract()
    {
        $length = $this->getConnection()->getRowCount('article_abstract_paras');
        $this->table->deleteParasByArticleId(246);
        $this->assertEquals($length - 6, $this->getConnection()->getRowCount('article_abstract_paras'));
        $pdo = $this->getConnection()->getConnection();
        $st = $pdo->prepare('SELECT * FROM article_abstract_paras WHERE article_id = 246');
        $st->execute();
        $data = $st->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertEmpty($data);
    }

    public function testDeleteAbstractForNonexistentArticleId()
    {
        $length = $this->getConnection()->getRowCount('article_abstract_paras');
        $this->table->deleteParasByArticleId(300);
        $this->assertEquals($length, $this->getConnection()->getRowCount('article_abstract_paras'));
    }
}