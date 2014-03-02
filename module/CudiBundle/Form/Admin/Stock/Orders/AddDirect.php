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

namespace CudiBundle\Form\Admin\Stock\Orders;

use Doctrine\ORM\EntityManager;

/**
 * Add Order Directly
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class AddDirect extends Add
{
    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int             $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($entityManager, '', $name);

        $this->remove('article_id');
        $this->remove('article');
        $this->get('add')
            ->setName('add_order');
    }

    public function getInputFilter()
    {
        $inputFilter = parent::getInputFilter();

        $inputFilter->remove('article_id');
        $inputFilter->remove('article');

        return $inputFilter;
    }
}
