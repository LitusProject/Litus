<?php


namespace BrBundle\Form\Admin\Event\Company;


use BrBundle\Entity\Company;
use BrBundle\Entity\Event\CompanyMetadata;
use BrBundle\Form\Admin\Company\Add;

class Edit extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'BrBundle\Hydrator\Event\Company';

    protected $metaData;

    public function init()
    {
        parent::init();

        


        $this->addSubmit('Edit', 'metadata_edit');

        if ($this->metaData !== null) {
            $this->bind($this->metaData);
        }
    }

    /**
     * @param  CompanyMetadata $metaData
     * @return self
     */
    public function setCompany(CompanyMetadata $metaData)
    {
        $this->metaData = $metaData;

        return $this;
    }




}