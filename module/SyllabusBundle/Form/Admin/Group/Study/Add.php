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
    private $_studies;

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'select',
            'name'       => 'studies',
            'label'      => 'Studies',
            'required'   => true,
            'attributes' => array(
                'multiple' => true,
                'style'    => 'max-width: 100%;',
                'options'  => $this->_getStudyNames(),
            ),
            'options'    => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->addSubmit('Add', 'add');
    }

    /**
     * @return array
     */
    private function _getStudyNames()
    {
        $studyNames = array();
        foreach ($this->_studies as $study) {
            $studyNames[$study->getId()] = 'Phase ' . $study->getPhase() . ' - ' . $study->getFullTitle();
        }

        return $studyNames;
    }

    /**
     * @param  array All possible studies
     * @return self
     */
    public function setStudies(array $studies)
    {
        $this->_studies = $studies;

        return $this;
    }
}
