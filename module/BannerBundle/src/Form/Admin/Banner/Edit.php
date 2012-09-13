<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace BannerBundle\Form\Admin\Banner;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    Doctrine\ORM\EntityManager,
    BannerBundle\Entity\Nodes\Banner,
    Zend\Form\Element\Submit;

/**
 * Edit Banner
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends Add
{
    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \BannerBundle\Entity\Nodes\Banner $banner The banner we're going to modify
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Banner $banner, $name = null)
    {
        parent::__construct($entityManager, $name);

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'banner_edit');
        $this->add($field);

        $this->_populateFromBanner($banner);
    }

    private function _populateFromBanner(Banner $banner)
    {
        $data = array(
            'name'       => $banner->getName(),
            'image'      => $banner->getImage(),
            'start_date' => $banner->getStartDate()->format('d/m/Y H:i'),
            'end_date'   => $banner->getEndDate()->format('d/m/Y H:i'),
            'active'     => $banner->isActive(),
            'url'        => $banner->getUrl(),
        );

        $this->setData($data);
    }
}
