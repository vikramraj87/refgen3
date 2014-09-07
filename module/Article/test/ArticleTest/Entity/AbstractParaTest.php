<?php
namespace ArticleTest\Entity;

use Article\Entity\AbstractPara;
use PHPUnit_Framework_TestCase;

class AbstractParaTest extends PHPUnit_Framework_TestCase
{
    public function testSetNlmCategory()
    {
        $para = new AbstractPara();
        $para->setNlmCategory('UNLABELLED');
        $this->assertEquals('Unlabelled', $para->getNlmCategory());
        $para->setNlmCategory('unlabelled');
        $this->assertEquals('Unlabelled', $para->getNlmCategory());
        $para->setNlmCategory('Unlabelled');
        $this->assertEquals('Unlabelled', $para->getNlmCategory());
    }

    public function testSetHeading()
    {
        $para = new AbstractPara();
        $para->setHeading('Materials and Methods');
        $this->assertEquals('Materials and methods', $para->getHeading());
        $para->setHeading('materials and methods');
        $this->assertEquals('Materials and methods', $para->getHeading());
        $para->setHeading('MATERIALS AND METHODS');
        $this->assertEquals('Materials and methods', $para->getHeading());
    }

    public function testCreateFromArray()
    {
        $data = [
            'id'      => 461,
            'heading' => '',
            'para'    => 'Pancytopenia is a common occurrence in pediatric patients. Though acute leukemias and bone marrow failure syndromes are usual causes of pancytopenia, etiologies such as infections and megaloblastic anemia also contribute. The aim of this study was to evaluate the clinico-hematological profile of varying degrees of childhood cytopenias with special reference to the non-malignant presentations. This is a retrospective study carried out in a tertiary care children\'s hospital.',
            'nlm_category' => ''
        ];

        $para = AbstractPara::createFromArray($data);
        $this->assertSame(461, $para->getId());
        $this->assertSame('', $para->getHeading());
        $this->assertSame('', $para->getNlmCategory());
        $this->assertSame($data['para'], $para->getPara());

        unset($data['id']);
        $data['heading'] = 'Materials and Methods';
        $data['nlm_category'] = 'METHODS';

        $para = AbstractPara::createFromArray($data);
        $this->assertSame(0, $para->getId());
        $this->assertSame('Materials and methods', $para->getHeading());
        $this->assertSame('Methods', $para->getNlmCategory());
        $this->assertSame($data['para'], $para->getPara());
    }

    public function toArray()
    {
        $expected = [
            'heading' => 'Materials and methods',
            'nlm_category' => 'Methods',
            'para' => 'Pancytopenia is a common occurrence in pediatric patients. Though acute leukemias and bone marrow failure syndromes are usual causes of pancytopenia, etiologies such as infections and megaloblastic anemia also contribute. The aim of this study was to evaluate the clinico-hematological profile of varying degrees of childhood cytopenias with special reference to the non-malignant presentations. This is a retrospective study carried out in a tertiary care children\'s hospital.'
        ];

        $para = new AbstractPara();
        $para->setHeading('MATERIALS AND METHODS');
        $para->setNlmCategory('METHODS');
        $para->setPara('Pancytopenia is a common occurrence in pediatric patients. Though acute leukemias and bone marrow failure syndromes are usual causes of pancytopenia, etiologies such as infections and megaloblastic anemia also contribute. The aim of this study was to evaluate the clinico-hematological profile of varying degrees of childhood cytopenias with special reference to the non-malignant presentations. This is a retrospective study carried out in a tertiary care children\'s hospital.');

        $this->assertEquals($expected, $para->toArray());

        $expected = [
            'heading' => '',
            'nlm_category' => '',
            'para' => 'Pancytopenia is a common occurrence in pediatric patients. Though acute leukemias and bone marrow failure syndromes are usual causes of pancytopenia, etiologies such as infections and megaloblastic anemia also contribute. The aim of this study was to evaluate the clinico-hematological profile of varying degrees of childhood cytopenias with special reference to the non-malignant presentations. This is a retrospective study carried out in a tertiary care children\'s hospital.'
        ];

        $para = new AbstractPara();
        $para->setHeading('');
        $para->setNlmCategory('');
        $para->setPara('Pancytopenia is a common occurrence in pediatric patients. Though acute leukemias and bone marrow failure syndromes are usual causes of pancytopenia, etiologies such as infections and megaloblastic anemia also contribute. The aim of this study was to evaluate the clinico-hematological profile of varying degrees of childhood cytopenias with special reference to the non-malignant presentations. This is a retrospective study carried out in a tertiary care children\'s hospital.');

        $this->assertEquals($expected, $para->toArray());
    }
} 