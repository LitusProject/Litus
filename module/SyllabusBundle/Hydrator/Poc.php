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
 *
 * @license http://litus.cc/LICENSE
 */

namespace SyllabusBundle\Hydrator;

use SyllabusBundle\Entity\Poc as PocEntity;

class Poc extends \CommonBundle\Component\Hydrator\Hydrator
{

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            $object = new PocEntity();
        }
        
		
		$object->setGroup($this->dataToGroup(($data['poc_group'])));
		
		$object -> setAcademic($this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneById($data['person']['id']) );
        return $object;
    }

    protected function doExtract($object = null)
    {
		return array();
    }
    protected function dataToGroup($groupData) {
		return $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\Group')
                ->findOneByName($groupData);
		
    }
}
