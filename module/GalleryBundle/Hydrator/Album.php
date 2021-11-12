<?php

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
