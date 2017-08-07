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

namespace CudiBundle\Form\Admin\Prof\File;

use CudiBundle\Entity\File\Mapping as FileMapping,
    LogicException;

/**
 * Confirm File add action
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Confirm extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var FileMapping|null
     */
    private $mapping;

    public function init()
    {
        if (null === $this->mapping) {
            throw new LogicException('Cannot confirm a null mapping');
        }

        parent::init();

        $this->setAttribute('id', 'uploadFile');

        $this->add(array(
            'type'       => 'text',
            'name'       => 'description',
            'label'      => 'Description',
            'required'   => true,
            'value'      => $this->mapping->getFile()->getDescription(),
            'attributes' => array(
                'size' => 70,
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
            'type'       => 'checkbox',
            'name'       => 'printable',
            'label'      => 'Printable',
            'attributes' => array(
                'data-help' => 'Enabling this option will cause the file to be exported by exporting an order. This way these files will be also send to the supplier.',
            ),
        ));

        $this->addSubmit('Confirm', 'file_add');
    }

    /**
     * @param  FileMapping $mapping
     * @return self
     */
    public function setMapping(FileMapping $mapping)
    {
        $this->mapping = $mapping;

        return $this;
    }
}
