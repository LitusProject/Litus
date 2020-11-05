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

namespace LogisticsBundle\Form\Catalog\Search;

use LogisticsBundle\Entity\Article as ArticleEntity;

/**
 * Search for articles
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 */
class Article extends \CommonBundle\Component\Form\Bootstrap\Form
{
    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'type',
                'required'   => true,
                'attributes' => array(
                    'options' => ArticleEntity::$POSSIBLE_CATEGORIES,
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'location',
                'required'   => true,
                'attributes' => array(
                    'options' => $this->createLocationsArray(),
                ),
            )
        );

        $this->addSubmit('Search');
        $this->get('submit')->setAttribute('class', 'btn btn-default');
    }

    /**
     * @return array
     */
    private function createLocationsArray()
    {
        $locations = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Location')
            ->findAllActive();

        $locationsArray = array();
        foreach ($locations as $location) {
            $locationsArray[$location->getId()] = $location->getName();
        }

        return $locationsArray;
    }
}
