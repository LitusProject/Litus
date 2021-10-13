<?php

namespace SyllabusBundle\Form\Admin\Group\Study;

/**
 * Add Study
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var array All possible studies
     */
    private $studies;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'studies',
                'label'      => 'Studies',
                'required'   => true,
                'attributes' => array(
                    'multiple' => true,
                    'style'    => 'max-width: 100%;height: 600px;',
                    'options'  => $this->getStudyNames(),
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'add');
    }

    /**
     * @return array
     */
    private function getStudyNames()
    {
        $studyNames = array();
        foreach ($this->studies as $study) {
            $studyNames[$study->getId()] = 'Phase ' . $study->getPhase() . ' - ' . $study->getTitle();
        }

        return $studyNames;
    }

    /**
     * @param  array All possible studies
     * @return self
     */
    public function setStudies(array $studies)
    {
        $this->studies = $studies;

        return $this;
    }
}
