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

namespace MailBundle\Form\Admin\Volunteer;

use CommonBundle\Component\Form\Admin\Element\Checkbox,
    CommonBundle\Component\Form\Admin\Element\Collection,
    CommonBundle\Component\Form\Bootstrap\Element\File,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Form\Admin\Element\Textarea,
    CommonBundle\Component\Form\Admin\Element\Select,
    MailBundle\Component\Validator\MultiMail as MultiMailValidator,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Send Mail
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Mail extends \CommonBundle\Component\Form\Admin\Form
{

    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $this->setAttribute('accept-charset', 'utf-8');

        $field = new Text('from');
        $field->setLabel('From')
            ->setAttribute('style', 'width: 400px;')
            ->setRequired();
        $this->add($field);

        $field = new Select('minimum_rank');
        $field->setLabel('Minimum Rank')
            ->setRequired()
            ->setAttribute('options', $this->_createRankArray());
        $this->add($field);

        $field = new Text('subject');
        $field->setLabel('Subject')
            ->setRequired()
            ->setAttribute('style', 'width: 400px;');
        $this->add($field);

        $field = new Textarea('message');
        $field->setLabel('Message')
            ->setRequired()
            ->setAttribute('style', 'width: 500px; height: 200px;');
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Send')
            ->setAttribute('class', 'mail');
        $this->add($field);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'from',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'emailAddress',
                        )
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'minimum_rank',
                    'required' => true,
                    'filters'  => array(),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'subject',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'message',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        return $inputFilter;
    }

    private function _createRankArray()
    {
        $rankingCriteria = unserialize($this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shift.ranking_criteria')
        );

        $ranks = array();
        foreach ($rankingCriteria as $key => $criterium)
            $ranks[$key] = ucfirst($criterium['name']);

        return $ranks;
    }
}
