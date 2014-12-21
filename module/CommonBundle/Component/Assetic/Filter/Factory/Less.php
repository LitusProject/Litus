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

namespace CommonBundle\Component\Assetic\Filter\Factory;

use CommonBundle\Component\Assetic\Filter\Less as LessFilter,
    Zend\ServiceManager\ServiceLocatorInterface;

class Less implements \Zend\ServiceManager\FactoryInterface
{
    public function createService(ServiceLocatorInterface $locator)
    {
        $config = $locator->get('Config');

        if (array_key_exists('litus', $config) && array_key_exists('node_prefix', $config['litus'])) {
            return new LessFilter($config['litus']['node_prefix']);
        } else {
            return new LessFilter();
        }
    }
}
