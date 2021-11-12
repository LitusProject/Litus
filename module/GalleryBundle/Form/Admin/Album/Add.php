<?php

namespace GalleryBundle\Form\Admin\Album;

use CommonBundle\Component\Form\FieldsetInterface;
use CommonBundle\Entity\General\Language;
use GalleryBundle\Entity\Album;

/**
 * Add an album.
 */
class Add extends \CommonBundle\Component\Form\Admin\Form\Tabbable
{
    protected $hydrator = 'GalleryBundle\Hydrator\Album';

    /**
     * @var Album|null
     */
    protected $album = null;

    protected function addTab(FieldsetInterface $container, Language $language, $isDefault)
    {
        $container->add(
            array(
                'type'     => 'text',
                'name'     => 'title',
                'label'    => 'Title',
                'required' => $isDefault,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name'    => 'AlbumName',
                                'options' => array(
                                    'album' => $this->album,
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );
    }

    protected function initAfterTabs()
    {
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
                'type'       => 'checkbox',
                'name'       => 'watermark',
                'label'      => 'Watermark',
                'value'      => true,
                'attributes' => array(
                    'data-help' => 'Embed a watermark into to photo\'s of this album. (Will only be applied to new uploaded photo\'s)',
                ),
            )
        );

        $this->addSubmit('Add', 'gallery_add');

        if ($this->album !== null) {
            $this->bind($this->album);
        }
    }

    /**
     * @param  Album $album
     * @return self
     */
    public function setAlbum(Album $album)
    {
        $this->album = $album;

        return $this;
    }
}
