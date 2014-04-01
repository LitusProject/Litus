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

namespace PublicationBundle\Form\Admin\Publication;

use Doctrine\ORM\EntityManager,
    PublicationBundle\Entity\Publication,
    PublicationBundle\Component\Validator\Title\Publication as TitleValidator,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * This form allows the user to edit the Publication.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Edit extends \PublicationBundle\Form\Admin\Publication\Add
{
    /**
     * @var Publication
     */
    private $_id;

    /**
     * @param EntityManager   $entityManager The EntityManager instance
     * @param Publication     $publication
     * @param null|string|int $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Publication $publication, $name = null)
    {
        parent::__construct($entityManager, $name);

        $this->_id = $publication->getId();

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'publication_edit');
        $this->add($field);

        $this->_populateFromPublication($publication);
    }

    private function _populateFromPublication(Publication $publication)
    {
        $formData = array(
            'title' => $publication->getTitle(),
        );

        $this->setData($formData);
    }

    public function getInputFilter()
    {
        $inputFilter = parent::getInputFilter();
        $factory = new InputFactory();

        $inputFilter->remove('title');

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'title',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new TitleValidator($this->_entityManager, $this->_id)
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
