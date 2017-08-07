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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Form\Sale\Sale;

/**
 * Return Sale
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ReturnArticle extends \CommonBundle\Component\Form\Bootstrap\Form
{
    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'typeahead',
            'name'       => 'person',
            'label'      => 'Person',
            'required'   => true,
            'attributes' => array(
                'placeholder'  => 'Student',
            ),
            'options'    => array(
                'input' => array(
                    'validators'  => array(
                        array('name' => 'typeahead_person'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'typeahead',
            'name'       => 'article',
            'label'      => 'Article',
            'required'   => true,
            'attributes' => array(
                'placeholder'  => 'Article',
            ),
            'options'    => array(
                'input' => array(
                    'validators' => array(
                        array('name' => 'typeahead_sale_article'),
                        array('name' => 'sale_has_bought'),
                    ),
                ),
            ),
        ));

        $this->addSubmit('Return', null, 'submit', array(
            'autocomplete' => 'off',
            'id'           => 'signin',
        ));

        $this->add(array(
            'type'  => 'reset',
            'name'  => 'cancel',
            'value' => 'Cancel',
        ));
    }
}
