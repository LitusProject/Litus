<?php

namespace CudiBundle\Hydrator\Sale\Session;

use CudiBundle\Entity\Sale\Session\Message as MessageEntity;

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
                && isset($data['tab_content']['tab_' . $abbrev]['content'])
            ) {
                $object->setContent($language, $data['tab_content']['tab_' . $abbrev]['content']);
            } else {
                $object->setContent($language, null);
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
                'content' => $object->getContent($language, false),
            );
        }

        return $data;
    }
}
