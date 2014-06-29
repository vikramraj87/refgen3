<?php
namespace Article\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Article\Entity\AbstractPara;

class AbstractTextHelper extends AbstractHelper
{
    /** @var AbstractPara[] */
    private $abstract = array();

    /**
     * @param AbstractPara[] $abstract
     * @return $this
     */
    public function __invoke(array $abstract = array())
    {
        $this->abstract = $abstract;
        return $this;
    }

    public function render()
    {
        if(empty($this->abstract)) {
            return '';
        }
        $html = '<div class="abstract">';
        foreach($this->abstract as $para) {
            /** @var AbstractPara $para */
            $p = $para->getPara();
            $h = $para->getHeading();
            if(!empty($h)) {
                $html .= '<h3>' . $h . '</h3>';
            }
            $html .= '<p>' . $p . '</p>';
        }
        $html .= '</div>';
        return $html;
    }

    public function renderTruncated()
    {
        if(empty($this->abstract)) {
            return '';
        }
        $html = '<div class="abstract">';
        $paras = array();
        foreach($this->abstract as $para) {
            $paras[] = $para->getPara();
        }
        $html .= '<p>' . $this->truncate(implode(' ', $paras)) . '</p>';
        $html .= '</div>';
        return $html;
    }

    private function truncate($str = '', $limit = 300, $break = ' ', $trailing = '...')
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