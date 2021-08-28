<?php

namespace BannerBundle\Form\Admin\Banner;

/**
 * Add Banner
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'BannerBundle\Hydrator\Node\Banner';

    const BANNER_WIDTH = 940;
    const BANNER_HEIGHT = 100;
    const BANNER_FILESIZE = '10MB';

    public function init($fileRequired = true)
    {
        parent::init();

        $this->setAttribute('id', 'uploadBanner');

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'name',
                'label'      => 'Name',
                'required'   => true,
                'attributes' => array(
                    'data-help' => 'The name of the banner (only shown in the admin).',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'datetime',
                'name'       => 'start_date',
                'label'      => 'Start Date',
                'required'   => true,
                'attributes' => array(
                    'data-help' => 'The start date for showing this banner, overrulled by "active".',
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'datetime',
                'name'       => 'end_date',
                'label'      => 'End Date',
                'required'   => true,
                'attributes' => array(
                    'data-help' => 'The end date for showing this banner, overrulled by "active".',
                ),
                'options' => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'DateCompare',
                                'options' => array(
                                    'first_date' => 'start_date',
                                    'format'     => 'd/m/Y H:i',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'active',
                'label'      => 'Active',
                'attributes' => array(
                    'data-help' => 'Flag whether the banner will be shown on the website.',
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'file',
                'name'       => 'file',
                'label'      => 'Image  (' . self::BANNER_WIDTH . ' x ' . self::BANNER_HEIGHT . ')',
                'attributes' => array(
                    'data-help' => 'The image for the banner. The maximum file size is ' . self::BANNER_FILESIZE . '. This must be a valid image (jpg, png, ...). The image must have a width of  ' . self::BANNER_WIDTH . 'px and a height of ' . self::BANNER_HEIGHT . 'px.',
                ),
                'required' => $fileRequired,
                'options'  => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name' => 'FileIsImage',
                            ),
                            array(
                                'name'    => 'FileSize',
                                'options' => array(
                                    'max' => self::BANNER_FILESIZE,
                                ),
                            ),

                            array(
                                'name'    => 'FileImageSize',
                                'options' => array(
                                    'minwidth'  => self::BANNER_WIDTH,
                                    'maxwidth'  => self::BANNER_WIDTH,
                                    'minheight' => self::BANNER_HEIGHT,
                                    'maxheight' => self::BANNER_HEIGHT,
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'url',
                'label'      => 'URL',
                'attributes' => array(
                    'data-help' => 'The url to open after clicking on the banner.',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'banner_add');
    }
}
