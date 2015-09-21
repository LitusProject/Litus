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

namespace PublicationBundle\Form\Admin\Edition\Pdf;

use PublicationBundle\Entity\Publication;

/**
 * The form used to add a new Publication
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var Publication The publication
     */
    private $publication;

    public function init()
    {
        parent::init();

        $this->setAttribute('id', 'uploadFile');

        $this->add(array(
            'type'     => 'text',
            'name'     => 'title',
            'label'    => 'Title',
            'required' => true,
            'options'  => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'publication_title_edition_html',
                            'options' => array(
                                'publication' => $this->publication,
                                'academic_year' => $this->getCurrentAcademicYear(true),
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'     => 'date',
            'name'     => 'date',
            'label'    => 'Date',
            'required' => true,
        ));

        $this->add(array(
            'type'     => 'file',
            'name'     => 'file',
            'label'    => 'File',
            'required' => true,
            'options'  => array(
                'input' => array(
                    'validators' => array(
                        array(
                            'name' => 'filesize',
                            'options' => array(
                                'max' => '75MB',
                            ),
                        ),
                        array(
                            'name' => 'fileextension',
                            'options' => array(
                                'extension' => 'pdf',
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $this->addSubmit('Add', 'pdf_add');
    }

    /**
     * @param  Publication $publication
     * @return self
     */
    public function setPublication(Publication $publication)
    {
        $this->publication = $publication;

        return $this;
    }
}
