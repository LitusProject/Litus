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

namespace CudiBundle\Hydrator\Sale\Session;

use CudiBundle\Entity\Sale\Session\OpeningHour as OpeningHourEntity;

class OpeningHour extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = array(
            'start'       => $object->getStart()->format('d/m/Y H:i'),
            'end'         => $object->getEnd()->format('d/m/Y H:i'),
            'tab_content' => array(),
        );

        foreach ($this->getLanguages() as $language) {
            $data['tab_content']['tab_' . $language->getAbbrev()] = array(
                'comment' => $object->getComment($language, false),
            );
        }

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new OpeningHourEntity($this->getPersonEntity());
        }

        $object->setStart(self::loadDateTime($data['start']))
            ->setEnd(self::loadDateTime($data['end']));

        foreach ($this->getLanguages() as $language) {
            $abbrev = $language->getAbbrev();

            if (isset($data['tab_content'])
                && isset($data['tab_content']['tab_' . $abbrev])
                && isset($data['tab_content']['tab_' . $abbrev]['comment'])
            ) {
                $object->setComment($language, $data['tab_content']['tab_' . $abbrev]['comment']);
            } else {
                $object->setComment($language, null);
            }
        }

        return $object;
    }
}
