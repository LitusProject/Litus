<?php

namespace PublicationBundle\Form\Admin\Edition\Pdf;

use PublicationBundle\Entity\Publication;

/**
 * The form used to add a new Publication
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    const FILE_SIZE = '50MB';

    /**
     * @var Publication The publication
     */
    private $publication;

    public function init()
    {
        parent::init();

        $this->setAttribute('id', 'uploadFile');

        $this->add(
            array(
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
                                'name'    => 'TitleEditionPdf',
                                'options' => array(
                                    'publication'   => $this->publication,
                                    'academic_year' => $this->getCurrentAcademicYear(true),
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'date',
                'name'     => 'date',
                'label'    => 'Date',
                'required' => true,
            )
        );

        $this->add(
            array(
                'type'       => 'file',
                'name'       => 'file',
                'label'      => 'File',
                'required'   => true,
                'attributes' => array(
                    'data-help' => 'The maximum file size is ' . self::FILE_SIZE . '.',
                ),
                'options'    => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'FileSize',
                                'options' => array(
                                    'max' => self::FILE_SIZE,
                                ),
                            ),
                            array(
                                'name'    => 'FileExtension',
                                'options' => array(
                                    'extension' => 'pdf',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

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
