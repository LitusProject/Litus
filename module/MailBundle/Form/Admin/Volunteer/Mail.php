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

/**
 * Send Mail
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Mail extends \CommonBundle\Component\Form\Admin\Form
{
    public function init()
    {
        parent::init();

        $this->setAttribute('accept-charset', 'utf-8');

        $this->add(array(
            'type'       => 'text',
            'name'       => 'from',
            'label'      => 'From',
            'required'   => true,
            'attributes' => array(
                'style' => 'width: 400px;',
            ),
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array('name' => 'emailAddress'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'minimum_rank',
            'label'      => 'Minimum Rank',
            'required'   => true,
            'options'    => array(
                'options' => $this->_createRanksArray(),
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'subject',
            'label'      => 'Subject',
            'required'   => true,
            'attributes' => array(
                'style' => 'width: 400px;',
            ),
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'textarea',
            'name'       => 'message',
            'label'      => 'Message',
            'required'   => true,
            'attributes' => array(
                'style' => 'width: 500px; height: 200px;',
            ),
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->addSubmit('Send', 'mail');
    }

    private function _createRanksArray()
    {
        $rankingCriteria = unserialize($this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shift.ranking_criteria')
        );

        $ranks = array(
            'none' => ''
        );
        foreach ($rankingCriteria as $key => $criterium)
            $ranks[$key] = ucfirst($criterium['name']);

        return $ranks;
    }
}
