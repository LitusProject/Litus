<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CalendarBundle\Form\Admin\Event;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
    CommonBundle\Component\Form\Admin\Element\Tabs,
    CommonBundle\Component\Form\Admin\Form\SubForm\TabContent,
    CommonBundle\Component\Form\Admin\Form\SubForm\TabPane,
    DateTime,
    Doctrine\ORM\EntityManager,
    CalendarBundle\Component\Validator\DateCompare as DateCompareValidator,
    CalendarBundle\Entity\Nodes\Event,
    Zend\Form\Element\Submit,
    Zend\Form\Element\Text,
    Zend\Form\Element\Textarea,
    Zend\Validator\Date as DateValidator;

/**
 * Add an event.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form\Tabbable
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @var \CalendarBundle\Entity\Nodes\Event
     */
    protected $event;

    /**
     * @param mixed $opts The validator's options
     */
    public function __construct(EntityManager $entityManager, $opts = null)
    {
        parent::__construct($opts);

        $this->_entityManager = $entityManager;

        $tabs = new Tabs('languages');
        $this->addElement($tabs);

        $tabContent = new TabContent();

        foreach($this->getLanguages() as $language) {
            $tabs->addTab(array($language->getName() => '#tab_' . $language->getAbbrev()));

            $pane = new TabPane('tab_' . $language->getAbbrev());

            $field = new Text('title_' . $language->getAbbrev());
            $field->setLabel('Title')
                ->setRequired()
            ->setDecorators(array(new FieldDecorator()));
            $pane->addElement($field);

            $field = new Text('location_' . $language->getAbbrev());
            $field->setLabel('Location')
                ->setRequired()
            ->setDecorators(array(new FieldDecorator()));
            $pane->addElement($field);

            $field = new Textarea('content_' . $language->getAbbrev());
            $field->setLabel('Content')
                ->setRequired()
            ->setDecorators(array(new FieldDecorator()));
            $pane->addElement($field);

            $tabContent->addSubForm($pane, 'tab_' . $language->getAbbrev());
        }

        $this->addSubForm($tabContent, 'tab_content');

        $field = new Text('start_date');
        $field->setLabel('Start Date')
            ->setRequired()
            ->addValidator(new DateValidator('dd/MM/yyyy H:m'))
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Text('end_date');
        $field->setLabel('End Date')
            ->addValidator(new DateCompareValidator('start_date', 'd/m/Y H:i'))
            ->addValidator(new DateValidator('dd/MM/yyyy H:m'))
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Submit('submit');
        $field->setLabel('Add')
            ->setAttrib('class', 'calendar_add')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
    }

    public function populateFromEvent(Event $event)
    {
        $data = array(
            'start_date' => $event->getStartDate()->format('d/m/Y H:i'),
        );
        if ($event->getEndDate())
            $data['end_date'] = $event->getEndDate()->format('d/m/Y H:i');

        foreach($this->getLanguages() as $language) {
            $data['location_' . $language->getAbbrev()] = $event->getLocation($language);
            $data['title_' . $language->getAbbrev()] = $event->getTitle($language);
            $data['content_' . $language->getAbbrev()] = $event->getContent($language);
        }
        $this->populate($data);
    }

    protected function getLanguages()
    {
        return $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findAll();
    }

    /**
     * Validate the form
     *
     * @param  array $data
     * @return boolean
     */
    public function isValid($data)
    {
        $valid = parent::isValid($data);

        $form = $this->getSubForm('tab_content');
        $date = DateTime::createFromFormat('d/m/Y H:i', $data['start_date']);

        if ($date) {
            $fallbackLanguage = \Locale::getDefault();
            $name = $date->format('d_m_Y_H_i_s') . '_' . \CommonBundle\Component\Util\Url::createSlug($data['title_' . $fallbackLanguage]);

            $event = $this->_entityManager
                ->getRepository('CalendarBundle\Entity\Nodes\Event')
                ->findOneByName($name);

            if (!(null == $event || (null != $this->event && null != $event && $event == $this->event))) {
                $title->addError('This event already exists');
                $valid = false;
            }
        }

        return $valid;
    }
}
