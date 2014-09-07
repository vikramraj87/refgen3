<?php

namespace Article\Entity;


class AbstractPara {
    /** @var int */
    protected $id          = 0;

    /** @var string */
    protected $heading     = '';

    /** @var string */
    protected $nlmCategory = '';

    /** @var string */
    protected $para        = '';

    /**
     * @return string
     */
    public function getHeading()
    {
        return $this->heading;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPara()
    {
        return $this->para;
    }

    /**
     * @param string $heading
     */
    public function setHeading($heading)
    {
        $this->heading = ucfirst(strtolower($heading));
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param string $para
     */
    public function setPara($para)
    {
        $this->para = $para;
    }

    /**
     * @param string $nlmCategory
     */
    public function setNlmCategory($nlmCategory)
    {
        $this->nlmCategory = ucfirst(strtolower($nlmCategory));
    }

    /**
     * @return string
     */
    public function getNlmCategory()
    {
        return $this->nlmCategory;
    }

    /**
     * Factory function to create AbstractPara object from array of data
     *
     * @param array $data
     * @return AbstractPara
     */
    static public function createFromArray(array $data = array())
    {
        $id          = isset($data['id'])           ?(int) $data['id']                                : 0;
        $heading     = isset($data['heading'])      ? $data['heading']                                : '';
        $nlmCategory = isset($data['nlm_category']) ? $data['nlm_category']                           : '';
        $para        = isset($data['para'])         ? trim(preg_replace('/\s+/', ' ', $data['para'])) : '';

        $abstractPara = new self();
        $abstractPara->setId($id);
        $abstractPara->setHeading($heading);
        $abstractPara->setNlmCategory($nlmCategory);
        $abstractPara->setPara($para);
        return $abstractPara;
    }

    /**
     * Serializes object to array
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'heading'      => $this->heading,
            'nlm_category' => $this->nlmCategory,
            'para'         => $this->para
        );
    }
}