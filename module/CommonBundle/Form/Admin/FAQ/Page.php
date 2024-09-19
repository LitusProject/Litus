<?php

namespace CommonBundle\Form\Admin\FAQ;

use CommonBundle\Entity\General\Node\FAQ\FAQ as FAQEntity;

/**
 * Add FAQ
 */
class Page extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var FAQEntity
     */
    private $faq;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'typeahead',
                'name'     => 'page_typeahead',
                'label'    => 'Page Name',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'Page',
                                'options' => array(
                                    'faq' => $this->getFAQ(),
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'submit',
                'name'       => 'page_add',
                'value'      => 'Add',
                'attributes' => array(
                    'class' => 'page_add',
                ),
            )
        );

        if ($this->getFAQ() !== null) {
            $this->bind($this->getFAQ());
        }
    }

    /**
     * @param FAQEntity $faq
     * @return self
     */
    public function setFAQ(FAQEntity $faq)
    {
        $this->faq = $faq;

        return $this;
    }

    /**
     * @return FAQEntity
     */
    public function getFAQ()
    {
        return $this->faq;
    }
}
