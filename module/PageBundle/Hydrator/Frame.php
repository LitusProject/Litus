<?php

namespace PageBundle\Hydrator;

use PageBundle\Entity\Frame\SmallFrameDescription as SmallFrameDescriptionEntity;
use PageBundle\Entity\Frame\SmallFrameDescription\Translation as TranslationEntity;

/**
 * This hydrator hydrates/extracts frame data.
 *
 * @author Robbe Serry <robbe.serry@vtk.be>
 */
class Frame extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new SmallFrameDescriptionEntity();
        }

        if($data['active'] !== null){
            $object->setActive($data['active']);
        }

        foreach ($this->getLanguages() as $language) {
            $translation = $object->getTranslation($language, false);

            $translationData = $data['tab_content']['tab_' . $language->getAbbrev()];

            if ($translation !== null) {
                $translation->setDescription($translationData['description']);
            } else {
                if ($translationData['description'] != '') {
                    $translation = new TranslationEntity(
                        $object,
                        $language,
                        $translationData['description']
                    );

                    // this persists the translations even if the returned
                    // object is never persisted.
                    // This should never happen though.
                    $this->getEntityManager()->persist($translation);
                }
            }
        }

        return $object;
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = array();

        foreach ($this->getLanguages() as $language) {
            $data['tab_content']['tab_' . $language->getAbbrev()]['description'] = $object->getDescription($language, false);
        }

        $data['active'] = $object->isActive();

        return $data;
    }
}
