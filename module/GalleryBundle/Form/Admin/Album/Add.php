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
