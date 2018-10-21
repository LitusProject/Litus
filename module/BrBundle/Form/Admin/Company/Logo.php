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

namespace BrBundle\Form\Admin\Company;

/**
 * Company logo form.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Logo extends \CommonBundle\Component\Form\Admin\Form
{
    const FILE_SIZE = '2MB';

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'file',
            'name'       => 'logo',
            'label'      => 'Logo',
            'required'   => true,
            'attributes' => array(
                'data-help' => 'The logo must be an image with a file size limit of ' . self::FILE_SIZE . '.',
            ),
            'options' => array(
                'input' => array(
                    'validators' => array(
                        array(
                            'name' => 'FileIsImage',
                        ),
                        array(
                            'name'    => 'FileSize',
                            'options' => array(
                                'max' => self::FILE_SIZE,
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $this->addSubmit('Save', 'image_edit');
    }
}
