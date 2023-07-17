<?php

namespace PageBundle\Hydrator;

use PageBundle\Entity\Frame\BigFrame as BigFrameEntity;
use PageBundle\Entity\Frame\SmallFrameDescription as SmallFrameDescriptionEntity;
use PageBundle\Entity\Frame\SmallFramePoster as SmallFramePosterEntity;
use PageBundle\Entity\Frame\BigFrame\Translation as BigFrameTranslationEntity;
use PageBundle\Entity\Frame\SmallFrameDescription\Translation as SmallFrameDescriptionTranslationEntity;

/**
 * This hydrator hydrates/extracts frame data.
 *
 * @author Robbe Serry <robbe.serry@vtk.be>
 */
class Frame extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected function doHydrate(array $data, $object = null)
    {
        $isbig = false;
        $hasdescription = false;
        $hasposter = false;

        if ($object === null) {
            switch ($data['frame_type']) {
                case 'big':
                    $object = new BigFrameEntity();
                    $isbig = true;
                    $hasdescription = true;
                    break;
                case'smallposter':
                    $object = new SmallFramePosterEntity();
                    $hasposter = true;
                    break;
                case'smalldescription':
                    $object = new SmallFrameDescriptionEntity();
                    $hasdescription = true;
                    break;
            }
        }

        if ($data['active'] !== null) {
            $object->setActive($data['active']);
        }

        if ($data['link_to'] !== null) {
            $type = substr($data['link_to'],0,4);
            if($type == "page"){
                $page = $this->getEntityManager()->getRepository("PageBundle\Entity\Node\Page")
                        ->findById(substr($data['link_to'],5))[0];
                $object->setLinkTo($page);
            } else if ($type == "link") {
                $link = $this->getEntityManager()->getRepository("PageBundle\Entity\Link")
                    ->findById(substr($data['link_to'],5))[0];
                $object->setLinkTo($link);
            }
            else {
                die("Invalid type");
            }
        }

        if ($hasdescription) {
            foreach ($this->getLanguages() as $language) {
                $translation = $object->getTranslation($language, false);

                $translationData = $data['tab_content']['tab_' . $language->getAbbrev()];

                if ($translation !== null) {
                    $translation->setDescription($translationData['description']);
                } else {
                    if ($translationData['description'] != '') {
                        if ($isbig) {
                            $translation = new BigFrameTranslationEntity(
                                $object,
                                $language,
                                $translationData['description']
                            );
                        } else {
                            $translation = new SmallFrameDescriptionTranslationEntity(
                                $object,
                                $language,
                                $translationData['description']
                            );
                        }

                        // this persists the translations even if the returned
                        // object is never persisted.
                        // This should never happen though.
                        $this->getEntityManager()->persist($translation);
                    }
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

        if($object instanceof BigFrameEntity){
            $data['frame_type'] = 'big';
        } else if($object instanceof SmallFrameDescriptionEntity){
            $data['frame_type'] = 'smalldescription';
        } else if($object instanceof SmallFramePosterEntity){
            $data['frame_type'] = 'smallposter';
        }

        $data['link_to'] = $object->getLinkTo();

        return $data;
    }
}
