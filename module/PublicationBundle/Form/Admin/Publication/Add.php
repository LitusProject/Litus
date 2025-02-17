<?php

namespace PublicationBundle\Form\Admin\Publication;

use PublicationBundle\Entity\Publication;

/**
 * The form used to add a new Publication
 *
 * @author Niels Avonds
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    // Define a file size limit similar to the Event poster form
    const FILE_SIZE = '20MB';

    protected $hydrator = 'PublicationBundle\Hydrator\Publication';

    /**
     * @var Publication|null
     */
    protected $publication;

    public function init()
    {
        parent::init();

        // Add the title field
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
                                'name'    => 'TitlePublication',
                                'options' => array(
                                    'exclude' => $this->publication !== null ? $this->publication->getId() : null,
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        // Add a file field for the preview image 
        $this->add(
            array(
                'type'       => 'file',
                'name'       => 'previewImage',
                'label'      => 'Preview Image',
                'required'   => false,
                'attributes' => array(
                    'data-help' => 'Upload an image for the preview. The file must be an image and should not exceed ' . self::FILE_SIZE . '.',
                ),
                'options'    => array(
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
            )
        );

        $this->addSubmit('Add', 'publication_add');
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
