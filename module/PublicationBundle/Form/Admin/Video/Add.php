<?php

namespace PublicationBundle\Form\Admin\Video;

use PublicationBundle\Entity\Video;

/**
 * The form used to add a new Video
 *
 * @author Robbe Serry <robbe.serry@vtk.be>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'PublicationBundle\Hydrator\Video';

    /**
     * @var Video|null
     */
    protected $video;

    public function init()
    {
        parent::init();

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
                                'name'    => 'TitleVideo',
                                'options' => array(
                                    'exclude' => $this->video !== null ? $this->video->getId() : null,
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'url',
                'label'    => 'Url',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'video_add');
    }

    /**
     * @param  Video $video
     * @return self
     */
    public function setVideo(Video $video)
    {
        $this->video = $video;

        return $this;
    }
}
