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

namespace CudiBundle\Form\Admin\Sales\Session\OpeningHour;

use CommonBundle\Component\OldForm\Admin\Element\Text,
    CommonBundle\Component\OldForm\Admin\Element\Tabs,
    CommonBundle\Component\OldForm\Admin\Form\SubForm\TabContent,
    CommonBundle\Component\OldForm\Admin\Form\SubForm\TabPane,
    CommonBundle\Component\Validator\DateCompare as DateCompareValidator,
    CudiBundle\Entity\Sale\Session\OpeningHour\OpeningHour,
    Doctrine\ORM\EntityManager,
    Zend\Form\Element\Submit,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Add opening hour
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\OldForm\Admin\Form
{
    /**
     * @var EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @param EntityManager   $entityManager The EntityManager instance
     * @param null|string|int $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $field = new Text('start');
        $field->setLabel('Start')
            ->setRequired()
            ->setAttribute('placeholder', 'dd/mm/yyyy hh:mm')
            ->setAttribute('data-datepicker', true)
            ->setAttribute('data-timepicker', true);
        $this->add($field);

        $field = new Text('end');
        $field->setLabel('End')
            ->setRequired()
            ->setAttribute('placeholder', 'dd/mm/yyyy hh:mm')
            ->setAttribute('data-datepicker', true)
            ->setAttribute('data-timepicker', true);
        $this->add($field);

        $tabs = new Tabs('languages');
        $this->add($tabs);

        $tabContent = new TabContent('tab_content');

        foreach ($this->getLanguages() as $language) {
            $tabs->addTab(array($language->getName() => '#tab_' . $language->getAbbrev()));

            $pane = new TabPane('tab_' . $language->getAbbrev());

            $field = new Text('comment_' . $language->getAbbrev());
            $field->setLabel('Comment');

            $pane->add($field);

            $tabContent->add($pane);
        }

        $this->add($tabContent);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'clock_add');
        $this->add($field);
    }

    public function populateFromOpeningHour(OpeningHour $openingHour)
    {
        $data = array(
            'start' => $openingHour->getStart()->format('d/m/Y H:i'),
            'end' => $openingHour->getEnd()->format('d/m/Y H:i'),
        );
        foreach ($this->getLanguages() as $language) {
            $data['comment_' . $language->getAbbrev()] = $openingHour->getComment($language, false);
        }

        $this->setData($data);
    }

    protected function getLanguages()
    {
        return $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findAll();
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'start',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'date',
                            'options' => array(
                                'format' => 'd/m/Y H:i',
                            ),
                        ),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'end',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'date',
                            'options' => array(
                                'format' => 'd/m/Y H:i',
                            ),
                        ),
                        new DateCompareValidator('start', 'd/m/Y H:i'),
                    ),
                )
            )
        );

        foreach ($this->getLanguages() as $language) {
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'comment_' . $language->getAbbrev(),
                        'required' => false,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );
        }

        return $inputFilter;
    }
}
