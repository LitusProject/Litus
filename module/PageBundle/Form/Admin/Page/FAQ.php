<?php

namespace PageBundle\Form\Admin\Page;

use PageBundle\Entity\Node\Page as PageEntity;

/**
 * Add FAQ
 */
class FAQ extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'PageBundle\Hydrator\Node\Page';

    /**
     * @var PageEntity
     */
    private $page;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'typeahead',
                'name'     => 'faq_typeahead',
                'label'    => 'FAQ Name',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'FAQ',
                                'options' => array(
                                    'page' => $this->getPage(),
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
                'name'       => 'faq_add',
                'value'      => 'Add',
                'attributes' => array(
                    'class' => 'faq_add',
                ),
            )
        );

        if ($this->getPage() !== null) {
            $this->bind($this->getPage());
        }
    }

    /**
     * @param PageEntity $page
     * @return self
     */
    public function setPage(PageEntity $page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @return PageEntity
     */
    public function getPage()
    {
        return $this->page;
    }
}
