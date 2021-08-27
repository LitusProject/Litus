<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

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
                            )
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
