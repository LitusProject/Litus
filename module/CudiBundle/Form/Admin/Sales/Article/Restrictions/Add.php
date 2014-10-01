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

namespace CudiBundle\Form\Admin\Sales\Article\Restrictions;

use CommonBundle\Component\Form\Admin\Element\Checkbox,
    CommonBundle\Component\Form\Admin\Element\Hidden,
    CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Util\AcademicYear,
    CudiBundle\Component\Validator\Sales\Article\Restrictions\Exists as RestrictionValidator,
    CudiBundle\Entity\Sale\Article,
    CudiBundle\Entity\Sale\Article\Restriction,
    Doctrine\ORM\EntityManager,
    Zend\Form\Element\Submit,
    Zend\InputFilter\Factory as InputFactory,
    Zend\InputFilter\InputFilter;

/**
 * Add Restriction
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var EntityManager
     */
    protected $_entityManager = null;

    /**
     * @var \CudiBundle\Entity\Sale\Article
     */
    protected $_article;

    /**
     * @param Article         $article
     * @param EntityManager   $entityManager
     * @param null|string|int $name          Optional name for the element
     */
    public function __construct(Article $article, EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;
        $this->_article = $article;

        $field = new Select('type');
        $field->setAttribute('id', 'restriction_type')
            ->setLabel('Type')
            ->setAttribute('options', array('amount' => 'Amount', 'member' => 'Member', 'study' => 'Study'))
            ->setAttribute('data-help', 'Limit the sale of this article on user base:
                <ul>
                    <li><b>Amount:</b> restrict the number of this article sold to this user</li>
                    <li><b>Member:</b> restrict this article to members only</li>
                    <li><b>Study:</b> restrict this article to students of one ore more studies</li>
                </ul>')
            ->setRequired();
        $this->add($field);

        $field = new Text('value_amount');
        $field->setAttribute('id', 'restriction_value_amount')
            ->setAttribute('class', 'restriction_value')
            ->setLabel('Amount')
            ->setRequired();
        $this->add($field);

        $field = new Checkbox('value_member');
        $field->setAttribute('id', 'restriction_value_member')
            ->setAttribute('class', 'restriction_value')
            ->setLabel('Member');
        $this->add($field);

        $field = new Select('value_study');
        $field->setAttribute('id', 'restriction_value_study')
            ->setAttribute('class', 'restriction_value')
            ->setAttribute('multiple', true)
            ->setAttribute('options', $this->_getStudies())
            ->setAttribute('style', 'max-width: 100%;')
            ->setLabel('Studies')
            ->setRequired();
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'add');
        $this->add($field);
    }

    public function _getStudies()
    {
        $academicYear = AcademicYear::getOrganizationYear($this->_entityManager);

        $studies = $this->_entityManager
            ->getRepository('SyllabusBundle\Entity\Study')
            ->findAllParentsByAcademicYear($academicYear);

        $options = array();
        foreach ($studies as $study) {
            $options[$study->getId()] = 'Phase ' . $study->getPhase() . ' - ' . $study->getFullTitle();
        }

        return $options;
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'type',
                    'required' => true,
                    'validators' => array(
                        new RestrictionValidator($this->_article, $this->_entityManager),
                    ),
                )
            )
        );

        if ('amount' == $this->data['type']) {
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'value_amount',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validator' => array(
                            array('name' => 'int'),
                        ),
                    )
                )
            );
        } elseif ('member' == $this->data['type']) {
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'value_member',
                        'required' => true,
                    )
                )
            );
        } elseif ('study' == $this->data['type']) {
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'value_study',
                        'required' => true,
                    )
                )
            );
        }

        return $inputFilter;
    }
}
