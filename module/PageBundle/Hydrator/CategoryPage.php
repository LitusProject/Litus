<?php

namespace PageBundle\Hydrator;

use PageBundle\Entity\CategoryPage as CategoryPageEntity;

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
            $object = new CategoryPageEntity();
        }

        if ($data['category'] != '') {
            $category = $this->getEntityManager()
                ->getRepository('PageBundle\Entity\Category')
                ->findOneById($data['category']);

            $object->setCategory($category);
        } else {
            $object->setCategory(null);
        }

        $editRoles = array();
        if (isset($data['edit_roles'])) {
            foreach ($data['edit_roles'] as $editRole) {
                $editRoles[] = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\Acl\Role')
                    ->findOneByName($editRole);
            }
        }

        $object->setEditRoles($editRoles);

        return $object;
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = array();

        $data['category'] = $object->getCategory() ? $object->getCategory()->getId() : '';
        $data['edit_roles'] = array();
        foreach ($object->getEditRoles() as $role) {
            $data['edit_roles'][] = $role->getName();
        }

        return $data;
    }
}
