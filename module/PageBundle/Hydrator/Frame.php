<?php

namespace PageBundle\Hydrator;

use PageBundle\Entity\Frame as FrameEntity;
use PageBundle\Entity\Frame\Translation as TranslationEntity;

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
        $hasposter = false;

        if ($object === null) {
            $object = new FrameEntity();
            switch ($data['frame_type']) {
                case 'big':
                    $object->setBig(true);
                    $object->setHasDescription(true);
                    $object->setHasPoster(true);
                    break;
                case'smallposter':
                    $object->setBig(false);
                    $object->setHasDescription(false);
                    $object->setHasPoster(true);
                    break;
                case'smalldescription':
                    $object->setBig(false);
                    $object->setHasDescription(true);
                    $object->setHasPoster(false);
                    break;
            }
        }

        if ($data['active'] !== null) {
            $object->setActive($data['active']);
        }

        if ($data['link_to'] !== null) {
            $type = substr($data['link_to'], 0, 4);
            if ($type == "page") {
                $page = $this->getEntityManager()->getRepository("PageBundle\Entity\Node\Page")
                    ->findById(substr($data['link_to'], 5))[0];
                $object->setLinkTo($page);
            } else if ($type == "link") {
                $link = $this->getEntityManager()->getRepository("PageBundle\Entity\Link")
                    ->findById(substr($data['link_to'], 5))[0];
                $object->setLinkTo($link);
            } else {
                die("Invalid type");
            }
        }

        if ($object->hasDescription()) {
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
        }
        return $object;
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = array();

        if($object->hasDescription()){
            foreach ($this->getLanguages() as $language) {
                $data['tab_content']['tab_' . $language->getAbbrev()]['description'] = $object->getDescription($language, false);
            }
        }


        $data['active'] = $object->isActive();

        if($object->isBig()){
            $data['frame_type'] = 'big';
        } else if($object->hasDescription()){
            $data['frame_type'] = 'smalldescription';
        } else{
            $data['frame_type'] = 'smallposter';
        }

        $data['link_to'] = $object->getLinkTo();

        return $data;
    }
}
