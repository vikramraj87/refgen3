<?php
/**
 * Created by PhpStorm.
 * User: vikramraj
 * Date: 19/06/14
 * Time: 12:54 PM
 */

namespace ArticleTest\View\Helper;

use Article\View\Helper\AbstractTextHelper;
use Article\Entity\AbstractPara;
class AbstractTextHelperTest extends \PHPUnit_Framework_TestCase
{
    /** @var AbstractTextHelper */
    private $helper;

    /** @var AbstractPara[] */
    private $data;

    public function testHelperWithArray()
    {
        $helper = $this->getHelper();
        $data = $this->getData();
        $expected = '<div class="abstract">';
        $expected .= '<h3>Objective</h3><p>The current study aimed to investigate the molecular basis of cervical cancer development using a microarray to identify the differentially expressed genes. This study also aimed to detect apoptosis genes and proteins to find those genes most aberrantly expressed in cervical cancer and to explore the cause of Uighur cervical cancer.</p>';
        $expected .= '<h3>Methods</h3><p>An analysis of gene expression profiles obtained from cervical cancer cases was performed. Total RNA was prepared from 10 samples of cervical carcinoma and normal cervix and was hybridized to Affymetrix oligonucleotide microarrays with probe sets complementary to more than 20,000 transcripts. Several genes of the apoptosis pathway, which were differentially regulated, included BCL2, BCLXL, and c-IAP1. These were validated by quantitative reverse transcription-polymerase chain reaction and immunohistochemical staining on an independent set of cancer and control specimens.</p>';
        $expected .= '<h3>Results</h3><p>Unsupervised hierarchical clustering of the expression data readily distinguished the normal cervix from cancer. Supervised analysis of gene expression data identified 1,326 and 1,432 genes that were upregulated and downregulated, respectively; a set of genes belonging to the apoptosis pathways were upregulated or downregulated in patients with cervical cancer. BCL2, BCLXL, and c-IAP1 were found to be upregulated in late-stage cancer compared to early-stage cancer.</p>';
        $expected .= '<h3>Conclusions</h3><p>These findings provide a new understanding of the gene expression profile in cervical cancer. BCL2, BCLXL, and c-IAP1 might be involved in cancer progression. The pathway analysis of expression data showed that the BCL2, BCLXL, and c-IAP1 genes were coordinately differentially regulated between cancer and normal cases. Our results may serve as basis for further development of biomarkers for the diagnosis and treatment of cervical cancer.</p>';
        $expected .= '</div>';
        $this->assertEquals($expected, $helper($data)->render());
    }

    public function testHelperWithArrayWithoutHeading()
    {
        $helper = $this->getHelper();
        $data = $this->getData();
        foreach($data as &$para) {
            /** @var AbstractPara $para */
            $para->setNlmCategory('');
            $para->setHeading('');
        }
        $expected = '<div class="abstract">';
        $expected .= '<p>The current study aimed to investigate the molecular basis of cervical cancer development using a microarray to identify the differentially expressed genes. This study also aimed to detect apoptosis genes and proteins to find those genes most aberrantly expressed in cervical cancer and to explore the cause of Uighur cervical cancer.</p>';
        $expected .= '<p>An analysis of gene expression profiles obtained from cervical cancer cases was performed. Total RNA was prepared from 10 samples of cervical carcinoma and normal cervix and was hybridized to Affymetrix oligonucleotide microarrays with probe sets complementary to more than 20,000 transcripts. Several genes of the apoptosis pathway, which were differentially regulated, included BCL2, BCLXL, and c-IAP1. These were validated by quantitative reverse transcription-polymerase chain reaction and immunohistochemical staining on an independent set of cancer and control specimens.</p>';
        $expected .= '<p>Unsupervised hierarchical clustering of the expression data readily distinguished the normal cervix from cancer. Supervised analysis of gene expression data identified 1,326 and 1,432 genes that were upregulated and downregulated, respectively; a set of genes belonging to the apoptosis pathways were upregulated or downregulated in patients with cervical cancer. BCL2, BCLXL, and c-IAP1 were found to be upregulated in late-stage cancer compared to early-stage cancer.</p>';
        $expected .= '<p>These findings provide a new understanding of the gene expression profile in cervical cancer. BCL2, BCLXL, and c-IAP1 might be involved in cancer progression. The pathway analysis of expression data showed that the BCL2, BCLXL, and c-IAP1 genes were coordinately differentially regulated between cancer and normal cases. Our results may serve as basis for further development of biomarkers for the diagnosis and treatment of cervical cancer.</p>';
        $expected .= '</div>';
        $this->assertEquals($expected, $helper($data)->render());
    }

    public function testHelperWithEmptyArray()
    {
        $helper = $this->getHelper();
        $data = array();
        $this->assertEquals('', $helper($data)->render());
    }

    public function testHelperWithSinglePara()
    {
        $helper = $this->getHelper();
        $para1 = $this->getData()[0];
        $data = array($para1);
        $expected = '<div class="abstract">';
        $expected .= '<h3>Objective</h3><p>The current study aimed to investigate the molecular basis of cervical cancer development using a microarray to identify the differentially expressed genes. This study also aimed to detect apoptosis genes and proteins to find those genes most aberrantly expressed in cervical cancer and to explore the cause of Uighur cervical cancer.</p>';
        $expected .= '</div>';
        $this->assertEquals($expected, $helper($data)->render());
    }

    public function testHelperWithSingleParaWithoutHeading()
    {
        $helper = $this->getHelper();
        /** @var AbstractPara $para1 */
        $para1 = $this->getData()[0];
        $para1->setHeading('');
        $para1->setNlmCategory('');
        $data = array($para1);
        $expected = '<div class="abstract">';
        $expected .= '<p>The current study aimed to investigate the molecular basis of cervical cancer development using a microarray to identify the differentially expressed genes. This study also aimed to detect apoptosis genes and proteins to find those genes most aberrantly expressed in cervical cancer and to explore the cause of Uighur cervical cancer.</p>';
        $expected .= '</div>';
        $this->assertEquals($expected, $helper($data)->render());
    }

    public function testTruncateWithArray()
    {
        $helper = $this->getHelper();
        $data = $this->getData();
        $expected = '<div class="abstract">';
        $expected .= '<p>The current study aimed to investigate the molecular basis of cervical cancer development using a microarray to identify the differentially expressed genes. This study also aimed to detect apoptosis genes and proteins to find those genes most aberrantly expressed in cervical cancer and to explore...</p>';
        $expected .= '</div>';
        $this->assertEquals($expected, $helper($data)->renderTruncated());
    }

    public function testTruncateWithArrayWithoutHeading()
    {
        $helper = $this->getHelper();
        $data = $this->getData();
        foreach($data as &$para) {
            /** @var AbstractPara $para */
            $para->setNlmCategory('');
            $para->setHeading('');
        }
        $expected = '<div class="abstract">';
        $expected .= '<p>The current study aimed to investigate the molecular basis of cervical cancer development using a microarray to identify the differentially expressed genes. This study also aimed to detect apoptosis genes and proteins to find those genes most aberrantly expressed in cervical cancer and to explore...</p>';
        $expected .= '</div>';
        $this->assertEquals($expected, $helper($data)->renderTruncated());
    }

    public function testTruncateWithEmptyArray()
    {
        $helper = $this->getHelper();
        $data = array();
        $this->assertEquals('', $helper($data)->renderTruncated());
    }

    public function testTruncateWithSingleElement()
    {
        $helper = $this->getHelper();
        $para1 = $this->getData()[0];
        $data = array($para1);
        $expected = '<div class="abstract">';
        $expected .= '<p>The current study aimed to investigate the molecular basis of cervical cancer development using a microarray to identify the differentially expressed genes. This study also aimed to detect apoptosis genes and proteins to find those genes most aberrantly expressed in cervical cancer and to explore...</p>';
        $expected .= '</div>';
        $this->assertEquals($expected, $helper($data)->renderTruncated());
    }

    public function testTruncateWithSingleElementWithoutHeading()
    {
        $helper = $this->getHelper();
        /** @var AbstractPara $para1 */
        $para1 = $this->getData()[0];
        $para1->setHeading('');
        $para1->setNlmCategory('');
        $data = array($para1);
        $expected = '<div class="abstract">';
        $expected .= '<p>The current study aimed to investigate the molecular basis of cervical cancer development using a microarray to identify the differentially expressed genes. This study also aimed to detect apoptosis genes and proteins to find those genes most aberrantly expressed in cervical cancer and to explore...</p>';
        $expected .= '</div>';
        $this->assertEquals($expected, $helper($data)->renderTruncated());
    }

    public function testTruncatedWithLessThan300()
    {
        $helper = $this->getHelper();
        $para1 = new AbstractPara();
        $para1->setHeading('Test');
        $para1->setPara('The current study aimed to investigate the molecular basis of cervical cancer development using a microarray to identify the differentially expressed genes.');
        $data = array($para1);
        $expected = '<div class="abstract">';
        $expected .= '<p>The current study aimed to investigate the molecular basis of cervical cancer development using a microarray to identify the differentially expressed genes.</p>';
        $expected .= '</div>';
        $this->assertEquals($expected, $helper($data)->renderTruncated());
    }

    public function testTruncatedWithMergingTwoParas()
    {
        $helper = $this->getHelper();

        $para1 = new AbstractPara();
        $para1->setHeading('Test');
        $para1->setPara('The current study aimed to investigate the molecular basis of cervical cancer development using a microarray to identify the differentially expressed genes.');

        $para2 = new AbstractPara();
        $para2->setHeading('Test2');
        $para2->setPara('This study also aimed to detect apoptosis genes and proteins to find those genes most aberrantly expressed in cervical cancer and to explore the cause of Uighur cervical cancer.');

        $data = array($para1, $para2);

        $expected = '<div class="abstract">';
        $expected .= '<p>The current study aimed to investigate the molecular basis of cervical cancer development using a microarray to identify the differentially expressed genes. This study also aimed to detect apoptosis genes and proteins to find those genes most aberrantly expressed in cervical cancer and to explore...</p>';
        $expected .= '</div>';
        $this->assertEquals($expected, $helper($data)->renderTruncated());
    }

    private function getHelper()
    {
        if(null === $this->helper) {
            $this->helper = new AbstractTextHelper();
        }
        return $this->helper;
    }

    private function getData()
    {
        if(null === $this->data) {
            $para1 = new AbstractPara();
            $para1->setHeading('Objective');
            $para1->setNlmCategory('Objective');
            $para1->setPara('The current study aimed to investigate the molecular basis of cervical cancer development using a microarray to identify the differentially expressed genes. This study also aimed to detect apoptosis genes and proteins to find those genes most aberrantly expressed in cervical cancer and to explore the cause of Uighur cervical cancer.');

            $para2 = new AbstractPara();
            $para2->setHeading('Methods');
            $para2->setNlmCategory('Methods');
            $para2->setPara('An analysis of gene expression profiles obtained from cervical cancer cases was performed. Total RNA was prepared from 10 samples of cervical carcinoma and normal cervix and was hybridized to Affymetrix oligonucleotide microarrays with probe sets complementary to more than 20,000 transcripts. Several genes of the apoptosis pathway, which were differentially regulated, included BCL2, BCLXL, and c-IAP1. These were validated by quantitative reverse transcription-polymerase chain reaction and immunohistochemical staining on an independent set of cancer and control specimens.');

            $para3 = new AbstractPara();
            $para3->setHeading('Results');
            $para3->setNlmCategory('Results');
            $para3->setPara('Unsupervised hierarchical clustering of the expression data readily distinguished the normal cervix from cancer. Supervised analysis of gene expression data identified 1,326 and 1,432 genes that were upregulated and downregulated, respectively; a set of genes belonging to the apoptosis pathways were upregulated or downregulated in patients with cervical cancer. BCL2, BCLXL, and c-IAP1 were found to be upregulated in late-stage cancer compared to early-stage cancer.');

            $para4 = new AbstractPara();
            $para4->setHeading('Conclusions');
            $para4->setNlmCategory('Conclusions');
            $para4->setPara('These findings provide a new understanding of the gene expression profile in cervical cancer. BCL2, BCLXL, and c-IAP1 might be involved in cancer progression. The pathway analysis of expression data showed that the BCL2, BCLXL, and c-IAP1 genes were coordinately differentially regulated between cancer and normal cases. Our results may serve as basis for further development of biomarkers for the diagnosis and treatment of cervical cancer.');

            $this->data = array(
                $para1,
                $para2,
                $para3,
                $para4
            );
        }
        return $this->data;
    }
}
 