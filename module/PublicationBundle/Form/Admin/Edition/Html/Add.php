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

namespace PublicationBundle\Form\Admin\Edition\Html;

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
                                'name'    => 'TitleEditionHtml',
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
                'type'       => 'select',
                'name'       => 'pdf_version',
                'label'      => 'PDF Version',
                'required'   => true,
                'attributes' => array(
                    'options' => $this->getPdfEditions(),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'textarea',
                'name'       => 'html',
                'label'      => 'HTML',
                'required'   => true,
                'attributes' => array(
                    'rows' => 20,
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
                'label'      => 'Image Archive',
                'required'   => true,
                'attributes' => array(
                    'multiple'  => true,
                    'data-help' => 'The maximum file size is ' . self::FILE_SIZE . '.',
                ),
                'options' => array(
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
                                    'extension' => 'zip',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'html_add');
    }

    public function getPdfEditions()
    {
        $pdfs = $this->getEntityManager()
            ->getRepository('PublicationBundle\Entity\Edition\Pdf')
            ->findAllByPublicationAndAcademicYear($this->publication, $this->getCurrentAcademicYear(true));

        $options = array();
        foreach ($pdfs as $pdf) {
            $options[$pdf->getId()] = $pdf->getTitle();
        }

        return $options;
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
