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

namespace GalleryBundle\Hydrator;

use GalleryBundle\Entity\Album as AlbumEntity;
use GalleryBundle\Entity\Album\Translation;

class Album extends \CommonBundle\Component\Hydrator\Hydrator
{
    private static $stdKeys = array('watermark');

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);

        $data['date'] = $object->getDate()->format('d/m/Y');

        $data['tab_content'] = array();
        foreach ($this->getLanguages() as $language) {
            $data['tab_content']['tab_' . $language->getAbbrev()] = array(
                'title' => $object->getTitle($language, false),
            );
        }

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new AlbumEntity($this->getPersonEntity());
        }

        if (isset($data['date'])) {
            $object->setDate(self::loadDate($data['date']));
        }

        foreach ($this->getLanguages() as $language) {
            $abbrev = $language->getAbbrev();

            if (!isset($data['tab_content']['tab_' . $abbrev])) {
                continue;
            }

            $existing = $object->getTranslation($language, false);

            $title = $data['tab_content']['tab_' . $abbrev]['title'];
            if ($title != '') {
                if ($existing !== null) {
                    $existing->setTitle($title);
                } else {
                    $object->addTranslation(new Translation($object, $language, $title));
                }
            } elseif ($existing !== null) {
                $object->removeTranslation($existing);
            }
        }

        return $this->stdHydrate($data, $object, self::$stdKeys);
    }
}
