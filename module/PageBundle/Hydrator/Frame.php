<?php

namespace PageBundle\Hydrator;

use PageBundle\Entity\Frame as FrameEntity;
use PageBundle\Entity\Frame\Translation as TranslationEntity;
use PageBundle\Entity\Node\Page;
use PageBundle\Entity\Link;

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
        }

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

        if ($data['active'] !== null) {
            $object->setActive($data['active']);
        }

        $link_to_id = $data['link_to_' . $data['category_page_id']];
        if ($link_to_id !== null) {
            $type = substr($link_to_id, 0, 4);
            if ($type == "page") {
                $page = $this->getEntityManager()->getRepository("PageBundle\Entity\Node\Page")
                    ->findById(substr($link_to_id, 5))[0];
                $object->setLinkTo($page);
            } else if ($type == "link") {
                $link = $this->getEntityManager()->getRepository("PageBundle\Entity\Link")
                    ->findById(substr($link_to_id, 5))[0];
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

        error_log($data['poster']);
        if($object->hasPoster() && isset($data['poster'])){
            $object->setPoster($data['poster']);
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

        $linkto = $object->getLinkTo();
        if($linkto instanceof Page){
            $data['link_to_' . $object->getCategoryPage()->getId()] = 'page_' . $linkto->getId();
        } elseif ($linkto instanceof Link){
            $data['link_to_' . $object->getCategoryPage()->getId()] = 'link_' . $linkto->getId();
        }

        return $data;
    }
}
