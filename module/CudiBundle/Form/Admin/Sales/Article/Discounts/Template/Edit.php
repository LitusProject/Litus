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
 *
 * @license http://litus.cc/LICENSE
 */

namespace Cudibundle\Form\Admin\Sales\Article\Discounts\Template;

use Doctrine\ORM\EntityManager,
    Cudibundle\Entity\Sale\Article\Discount\Template,
    Zend\Form\Element\Text,
    Zend\Form\Element\Submit;

/**
 * Edit Discount Template
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 */
class Edit extends Add
{
    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param \CudiBundle\Entity\Sale\Article\Discount\Template $template The template we're going to modify
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Template $template, $name = null)
    {
        parent::__construct($entityManager, $name);

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'edit');
        $this->add($field);

        $this->_populateFromTemplate($template);
    }

    private function _populateFromTemplate(Template $template)
    {
        $data = array(
            'name' => $template->getName(),
            'method' => $template->getMethod(),
            'value' => $template->getValue(),
            'rounding' => $template->getRounding(),
            'apply_once' => $template->applyOnce(),
            'organization' => $template->getOrganization(),
            'type' => $template->getType()
        );

        $this->setData($data);
    }
}