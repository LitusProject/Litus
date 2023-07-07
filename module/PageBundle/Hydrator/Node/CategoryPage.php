<?php

namespace PageBundle\Hydrator\Node;

use PageBundle\Entity\Node\CategoryPage as CategoryPageEntity;

/**
 * This hydrator hydrates/extracts categorypage data.
 *
 * @author Robbe Serry <robbe.serry@vtk.be>
 */
class CategoryPage extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new CategoryPageEntity($this->getPersonEntity());
        }

        if ($data['category'] != '') {
            $category = $this->getEntityManager()
                ->getRepository('PageBundle\Entity\Category')
                ->findOneById($data['category']);

            $object->setCategory($category);
        } else {
            $object->setCategory(null);
        }

        return $object;
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = array();

        $data['category'] = $object->getCategory() ? $object->getCategory()->getId() : '';

        return $data;
    }
}
