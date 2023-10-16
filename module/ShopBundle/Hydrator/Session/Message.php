<?php

namespace ShopBundle\Hydrator\Session;

use ShopBundle\Entity\Session\Message as MessageEntity;

class Message extends \CommonBundle\Component\Hydrator\Hydrator
{

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new MessageEntity();
        }

        foreach ($this->getLanguages() as $language) {
            $abbrev = $language->getAbbrev();

            if (isset($data['tab_content'])
                && isset($data['tab_content']['tab_' . $abbrev])
                && (isset($data['tab_content']['tab_' . $abbrev]['topContent'])
                || isset($data['tab_content']['tab_' . $abbrev]['bottomContent']))
            ) {
                $object->setContent($language, $data['tab_content']['tab_' . $abbrev]['topContent'], $data['tab_content']['tab_' . $abbrev]['bottomContent']);
            } else {
                $object->setContent($language, null, null);
            }
        }

        $object->setActive($data['active']);

        return $object;
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = array(
            'tab_content' => array(),
            'active'      => $object->isActive(),
        );

        foreach ($this->getLanguages() as $language) {
            $data['tab_content']['tab_' . $language->getAbbrev()] = array(
                'topContent' => $object->getTopContent($language, false),
                'bottomContent' => $object->getBottomContent($language, false),
            );
        }

        return $data;
    }
}
