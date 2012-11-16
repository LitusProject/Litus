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

use BrBundle\Entity\Cv\Language as CvLanguage,
    CommonBundle\Component\Form\Bootstrap\Element\Button,
    CommonBundle\Component\Form\Bootstrap\Element\Collection,
    CommonBundle\Component\Form\Admin\Element\Hidden,
    CommonBundle\Component\Form\Bootstrap\Element\Select,
    CommonBundle\Component\Form\Bootstrap\Element\Submit,
    CommonBundle\Component\Form\Bootstrap\Element\Text,
    CommonBundle\Component\Form\Bootstrap\Element\Textarea,
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

        // TODO: set character limit on EVERY manual field
        // TODO: languages: add validators
        // TODO: languages: enforce at least 1 and max 5
        // TODO: languages: put correct stuff in the selects for dynamically added languages using twig
        // TODO: languages: add remove buttons for all but i = 0

        $studies = new Collection('studies');
        $studies->setLabel('Education');
        $this->add($studies);

        $field = new Select('degree');
        $field->setLabel('Primary Degree')
            ->setAttribute('options', $studiesMap);
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
            ->setAttribute('style', 'resize: none;');
        $studies->add($field);

        $erasmus = new Collection('erasmus');
        $erasmus->setLabel('Erasmus (Optional)');
        $this->add($erasmus);

        $field = new Text('erasmus_period');
        $field->setLabel('Period');
        $erasmus->add($field);

        $field = new Text('erasmus_location');
        $field->setLabel('Location');
        $erasmus->add($field);

        $languageCollection = new Collection('languages');
        $languageCollection->setLabel('Languages');
        $this->add($languageCollection);

        $field = new Hidden('lang_count');
        $field->setValue(1);
        $this->add($field);

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
            ->setAttribute('style', 'resize: none;');
        $capabilities->add($field);

        $field = new TextArea('experiences');
        $field->setLabel('Experiences, Projects (e.g. Internship, Holiday Jobs)')
            ->setAttribute('rows', 3)
            ->setAttribute('style', 'resize: none;');
        $capabilities->add($field);

        $thesis = new Collection('thesis');
        $thesis->setLabel('Thesis');
        $this->add($thesis);

        $field = new Text('thesis_title');
        $field->setLabel('Title');
        $thesis->add($field);

        $field = new TextArea('thesis_summary');
        $field->setLabel('Summary')
            ->setAttribute('rows', 3)
            ->setAttribute('style', 'resize: none;');
        $thesis->add($field);

        $future = new Collection('future');
        $future->setLabel('Future');
        $this->add($future);

        $field = new Text('field_of_interest');
        $field->setLabel('Field Of Interest');
        $future->add($field);

        $field = new Text('mobility_europe');
        $field->setLabel('Mobility Europe');
        $future->add($field);

        $field = new Text('mobility_world');
        $field->setLabel('Mobility World');
        $future->add($field);

        $field = new TextArea('career_expectations');
        $field->setLabel('Career Expectations')
            ->setAttribute('rows', 3)
            ->setAttribute('style', 'resize: none;');
        $future->add($field);

        $thesis = new Collection('profile');
        $thesis->setLabel('Profile');
        $this->add($thesis);

        $field = new TextArea('hobbies');
        $field->setLabel('Hobbies')
            ->setAttribute('rows', 3)
            ->setAttribute('style', 'resize: none;');
        $thesis->add($field);

        $field = new TextArea('profile_about');
        $field->setLabel('About Me')
            ->setAttribute('rows', 3)
            ->setAttribute('style', 'resize: none;');
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

    public function addLanguages($formData)
    {
        $languageCollection = $this->get('languages');
        $this->get('lang_count')->setValue($formData['lang_count']);

        for ($i = 0; $i < $formData['lang_count']; $i++) {

            if (!isset($formData['lang_name' . $i]))
                continue;

            $field = new Text('lang_name' . $i);
            $field->setLabel('Language');
            $languageCollection->add($field);

            $field = new Select('lang_oral' . $i);
            $field->setLabel('Oral Skills')
                ->setAttribute('options', CvLanguage::$ORAL_SKILLS);
            $languageCollection->add($field);

            $field = new Select('lang_written' . $i);
            $field->setLabel('Written Skills')
                ->setAttribute('options', CvLanguage::$WRITTEN_SKILLS);
            $languageCollection->add($field);

        }
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'additional_diplomas',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'erasmus_period',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'erasmus_location',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'computer_skills',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'experiences',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'thesis_title',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'thesis_summary',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'field_of_interest',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'mobility_europe',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'mobility_world',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'career_expectations',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'hobbies',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'profile_about',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );


        // TODO limit nr of characters per line + nr of lines

        return $inputFilter;
    }
}
