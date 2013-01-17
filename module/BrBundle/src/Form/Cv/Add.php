<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Form\Cv;

use BrBundle\Entity\Cv\Entry as CvEntry,
    BrBundle\Entity\Cv\Language as CvLanguage,
    CommonBundle\Component\Validator\FieldLength as LengthValidator,
    CommonBundle\Component\Form\Bootstrap\Element\Button,
    CommonBundle\Component\Form\Bootstrap\Element\Collection,
    CommonBundle\Component\Form\Admin\Element\Hidden,
    CommonBundle\Component\Form\Bootstrap\Element\Select,
    CommonBundle\Component\Form\Bootstrap\Element\Submit,
    CommonBundle\Component\Form\Bootstrap\Element\Text,
    CommonBundle\Component\Form\Bootstrap\Element\Textarea,
    CommonBundle\Component\Validator\Decimal as DecimalValidator,
    CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\General\Language,
    CommonBundle\Entity\Users\People\Academic,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * The form used to add a new cv
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{

    /**
     * The entity manager.
     */
    private $_entityManager;

    /**
     * The academic this form is for.
     */
    private $_academic;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Academic $academic, AcademicYear $year, Language $language, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;
        $this->_academic = $academic;

        $studiesMap = array();
        $studies = $entityManager->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
            ->findAllByAcademicAndAcademicYear($academic, $year);
        foreach($studies as $study) {
            $studiesMap[$study->getStudy()->getId()] = $study->getStudy()->getFullTitle();
        }

        $currentYear = date("Y");
        $years = array();
        for ($i = -1; $i < 20; $i++) {
            $year = $currentYear - $i;
            $years[$year] = $year;
        }

        $studies = new Collection('studies');
        $studies->setLabel('Education');
        $this->add($studies);

        $field = new Text('prior_degree');
        $field->setLabel('Prior Degree (e.g. Bachelor in Engineering, Industrial Engineering, ...)')
            ->setRequired(true)
            ->setAttribute('class', $field->getAttribute('class') . ' count')
            ->setAttribute('data-count', 100);
        $studies->add($field);

        $field = new Text('prior_grade');
        $field->setLabel('Grade for the Prior Degree (e.g. 65.48)')
            ->setRequired(true);
        $studies->add($field);

        $field = new Select('degree');
        $field->setLabel('Primary Degree')
            ->setAttribute('options', $studiesMap);
        $studies->add($field);

        $field = new Text('grade');
        $field->setLabel('(Provisional) Grade for the Current Degree (e.g. 65.48)')
            ->setRequired(true);
        $studies->add($field);

        $field = new Select('bachelor_start');
        $field->setLabel('Started Bachelor In')
            ->setAttribute('options', $years)
            ->setValue($currentYear - 4);
        $studies->add($field);

        $field = new Select('bachelor_end');
        $field->setLabel('Ended Bachelor In')
            ->setAttribute('options', $years)
            ->setValue($currentYear - 1);
        $studies->add($field);

        $field = new Select('master_start');
        $field->setLabel('Started Master In')
            ->setAttribute('options', $years)
            ->setValue($currentYear - 1);
        $studies->add($field);

        $field = new Select('master_end');
        $field->setLabel('Will End Master In')
            ->setAttribute('options', $years)
            ->setValue($currentYear + 1);
        $studies->add($field);

        $field = new TextArea('additional_diplomas');
        $field->setLabel('Additional Diplomas (e.g. driver\'s license)')
            ->setAttribute('rows', 3)
            ->setRequired(false)
            ->setAttribute('class', $field->getAttribute('class') . ' count')
            ->setAttribute('data-count', 150)
            ->setAttribute('style', 'resize: none;');
        $studies->add($field);

        $erasmus = new Collection('erasmus');
        $erasmus->setLabel('Erasmus (Optional)');
        $this->add($erasmus);

        $field = new Text('erasmus_period');
        $field->setLabel('Period')
            ->setRequired(false)
            ->setAttribute('class', $field->getAttribute('class') . ' count')
            ->setAttribute('data-count', 50);
        $erasmus->add($field);

        $field = new Text('erasmus_location');
        $field->setLabel('Location')
            ->setRequired(false)
            ->setAttribute('class', $field->getAttribute('class') . ' count')
            ->setAttribute('data-count', 50);
        $erasmus->add($field);

        $languageCollection = new Collection('languages');
        $languageCollection->setLabel('Languages (max. 5)');
        $this->add($languageCollection);

        $field = new Hidden('lang_count');
        $field->setValue(1);
        $this->add($field);

        $field = new TextArea('lang_extra');
        $field->setLabel('Extra Information (Year Abroad, Born Outside Belgium, ...)')
            ->setAttribute('rows', 2)
            ->setRequired(true)
            ->setAttribute('class', $field->getAttribute('class') . ' count')
            ->setAttribute('data-count', 130)
            ->setAttribute('style', 'resize: none;');
        $languageCollection->add($field);

        $field = new Button('language_add');
        $field->setLabel('Add Language')
            ->setAttribute('class', 'btn btn-primary')
            ->setAttribute('style', 'margin-top:20px; margin-left: 221px;');
        $languageCollection->add($field);

        $capabilities = new Collection('capabilities');
        $capabilities->setLabel('Capabilities');
        $this->add($capabilities);

        $field = new TextArea('computer_skills');
        $field->setLabel('Computer Skills')
            ->setAttribute('rows', 3)
            ->setRequired(true)
            ->setAttribute('class', $field->getAttribute('class') . ' count')
            ->setAttribute('data-count', 425)
            ->setAttribute('style', 'resize: none;');
        $capabilities->add($field);

        $field = new TextArea('experiences');
        $field->setLabel('Experiences, Projects (e.g. Internship, Holiday Jobs)')
            ->setAttribute('rows', 3)
            ->setRequired(true)
            ->setAttribute('class', $field->getAttribute('class') . ' count')
            ->setAttribute('data-count', 425)
            ->setAttribute('style', 'resize: none;');
        $capabilities->add($field);

        $thesis = new Collection('thesis');
        $thesis->setLabel('Thesis');
        $this->add($thesis);

        $field = new TextArea('thesis_summary');
        $field->setLabel('Summary')
            ->setAttribute('rows', 3)
            ->setAttribute('style', 'resize: none;')
            ->setRequired(true)
            ->setAttribute('class', $field->getAttribute('class') . ' count')
            ->setAttribute('data-count', 300);
        $thesis->add($field);

        $future = new Collection('future');
        $future->setLabel('Future');
        $this->add($future);

        $field = new Text('field_of_interest');
        $field->setLabel('Field Of Interest')
            ->setRequired(true)
            ->setAttribute('class', $field->getAttribute('class') . ' count')
            ->setAttribute('data-count', 50);
        $future->add($field);

        $field = new Text('mobility_europe');
        $field->setLabel('Mobility Europe (Would you be able to travel within Europe? How often?)')
            ->setRequired(true)
            ->setAttribute('class', $field->getAttribute('class') . ' count')
            ->setAttribute('data-count', 50);
        $future->add($field);

        $field = new Text('mobility_world');
        $field->setLabel('Mobility World (Would you be able to travel around the world? How often?)')
            ->setRequired(true)
            ->setAttribute('class', $field->getAttribute('class') . ' count')
            ->setAttribute('data-count', 50);
        $future->add($field);

        $field = new TextArea('career_expectations');
        $field->setLabel('Career Expectations')
            ->setAttribute('rows', 3)
            ->setAttribute('style', 'resize: none;')
            ->setRequired(true)
            ->setAttribute('class', $field->getAttribute('class') . ' count')
            ->setAttribute('data-count', 200);
        $future->add($field);

        $thesis = new Collection('profile');
        $thesis->setLabel('Profile');
        $this->add($thesis);

        $field = new TextArea('hobbies');
        $field->setLabel('Hobbies')
            ->setAttribute('rows', 3)
            ->setAttribute('style', 'resize: none;')
            ->setRequired(true)
            ->setAttribute('class', $field->getAttribute('class') . ' count')
            ->setAttribute('data-count', 200);
        $thesis->add($field);

        $field = new TextArea('profile_about');
        $field->setLabel('About Me')
            ->setAttribute('rows', 3)
            ->setAttribute('style', 'resize: none;')
            ->setRequired(true)
            ->setAttribute('class', $field->getAttribute('class') . ' count')
            ->setAttribute('data-count', 200);
        $thesis->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'btn btn-primary');
        $this->add($field);

        $this->addLanguages(
            array(
                'lang_count' => 1,
                'lang_name0' => '',
            )
        );
    }

    public function populateFromEntry(CvEntry $entry) {

        $formData = array(
            'prior_degree' => $entry->getPriorStudy(),
            'prior_grade' => $entry->getPriorGrade() / 100,
            'degree' => $entry->getStudy()->getId(),
            'grade' => $entry->getGrade() / 100,
            'bachelor_start' => $entry->getBachelorStart(),
            'bachelor_end' => $entry->getBachelorEnd(),
            'master_start' => $entry->getMasterStart(),
            'master_end' => $entry->getMasterEnd(),
            'additional_diplomas' => $entry->getAdditionalDiplomas(),
            'erasmus_period' => $entry->getErasmusPeriod(),
            'erasmus_location' => $entry->getErasmusLocation(),
            'lang_extra' => $entry->getLanguageExtra(),
            'computer_skills' => $entry->getComputerSkills(),
            'experiences' => $entry->getExperiences(),
            'thesis_summary' => $entry->getThesisSummary(),
            'field_of_interest' => $entry->getFutureInterest(),
            'mobility_europe' => $entry->getMobilityEurope(),
            'mobility_world' => $entry->getMobilityWorld(),
            'career_expectations' => $entry->getCareerExpectations(),
            'hobbies' => $entry->getHobbies(),
            'profile_about' => $entry->getAbout(),
            'lang_count' => count($entry->getLanguages()),
        );

        $i = 0;
        foreach ($entry->getLanguages() as $language) {

            $formData['lang_name' . $i] = $language->getName();
            $formData['lang_oral' . $i] = $language->getOralSkillCode();
            $formData['lang_written' . $i] = $language->getWrittenSkillCode();

            $i++;
        }

        $formData = $this->addLanguages($formData);
        $this->setData($formData);
    }

    public function addLanguages($formData)
    {
        $realCount = 0;
        $languageCollection = $this->get('languages');
        $this->get('lang_count')->setValue($formData['lang_count']);

        for ($i = 0; $i < $formData['lang_count']; $i++) {

            if (!isset($formData['lang_name' . $i]))
                continue;

            $field = new Text('lang_name' . $i);
            $field->setLabel('Language')
                ->setRequired(true)
                ->setAttribute('class', $field->getAttribute('class') . ' count')
                ->setAttribute('data-count', 30);
            $languageCollection->add($field);

            $field = new Select('lang_oral' . $i);
            $field->setLabel('Oral Skills')
                ->setAttribute('options', CvLanguage::$ORAL_SKILLS);
            $languageCollection->add($field);

            $field = new Select('lang_written' . $i);
            $field->setLabel('Written Skills')
                ->setAttribute('options', CvLanguage::$WRITTEN_SKILLS);
            $languageCollection->add($field);

            if ('' !== $formData['lang_name' . $i])
                $realCount++;
        }

        $formData['lang_realcount'] = $realCount;

        return $formData;
    }

    public function isValidLanguages($formData)
    {
        $count = $formData['lang_realcount'];
        return $count > 0 && $count <= 5;
    }

    private function _addCountFilters(InputFilter $inputFilter, InputFactory $factory, $parent) {
        $iterator = $parent->getIterator();
        foreach ($iterator as $element) {
            if ($element instanceof \Zend\Form\Fieldset) {
                $this->_addCountFilters($inputFilter, $factory, $element);
            } else {
                if (FALSE !== strpos($element->getAttribute('class'), 'count')) {
                    $count = $element->getAttribute('data-count');
                    $inputFilter->add(
                        $factory->createInput(
                            array(
                                'name' => $element->getName(),
                                'required' => $element->getAttribute('required'),
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                                'validators' => array(
                                    new LengthValidator(
                                        $count,
                                        75
                                    )
                                ),
                            )
                        )
                    );
                }
            }

        }
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $this->_addCountFilters($inputFilter, $factory, $this);

        for ($i = 0; $i < $this->data['lang_count']; $i++) {
            if (isset($this->data['lang_name' . $i])) {
                $inputFilter->add(
                    $factory->createInput(
                        array(
                            'name' => 'lang_name' . $i,
                            'required' => true,
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                        )
                    )
                );
            }
        }

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'lang_realcount',
                    'validators' => array(
                        array(
                            'name' => 'between',
                            'options' => array(
                                'min' => 1,
                                'max' => 5,
                            ),
                        ),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'prior_grade',
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new DecimalValidator(2),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'grade',
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new DecimalValidator(2),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
