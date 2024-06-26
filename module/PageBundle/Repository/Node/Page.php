<?php

namespace PageBundle\Repository\Node;

use PageBundle\Entity\Node\Page as PageEntity;

/**
 * Page
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Page extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    public function findAllQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('p')
            ->from('PageBundle\Entity\Node\Page', 'p')
            ->where(
                $query->expr()->isNull('p.endTime')
            )
            ->getQuery();
    }

    public function findByCategory($category)
    {
        return $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Node\Page')
            ->findBy(array('category' => $category, 'endTime' => null));
    }

    public function findByParent($parent)
    {
        return $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Node\Page')
            ->findBy(array('parent' => $parent, 'endTime' => null));
    }

    public function findOneByNameAndParent($name, $parentName)
    {
        if ($parentName === null) {
            return $this->findOneByName($name, null);
        }

        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('p')
            ->from('PageBundle\Entity\Node\Page', 'p')
            ->innerJoin('p.parent', 'par')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('p.name', ':name'),
                    $query->expr()->eq('par.name', ':parentName'),
                    $query->expr()->isNull('p.endTime')
                )
            )
            ->setParameter('name', $name)
            ->setParameter('parentName', $parentName)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByName($name, PageEntity $parent = null)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('p')
            ->from('PageBundle\Entity\Node\Page', 'p')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('p.name', ':name'),
                    $parent === null ? $query->expr()->isNull('p.parent') : $query->expr()->eq('p.parent', ':parent'),
                    $query->expr()->isNull('p.endTime')
                )
            )
            ->setParameter('name', $name);

        if ($parent !== null) {
            $query->setParameter('parent', $parent);
        }

        return $query->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllByTitleQuery($title)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $translations = $query->select('p.id')
            ->from('PageBundle\Entity\Node\Page\Translation', 't')
            ->innerJoin('t.page', 'p')
            ->where(
                $query->expr()->like($query->expr()->lower('t.title'), ':title')
            )
            ->setParameter('title', '%' . strtolower($title) . '%')
            ->getQuery()
            ->getResult();

        $ids = array(0);
        foreach ($translations as $translation) {
            $ids[] = $translation['id'];
        }

        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('p')
            ->from('PageBundle\Entity\Node\Page', 'p')
            ->where(
                $query->expr()->andX(
                    $query->expr()->in('p.id', $ids),
                    $query->expr()->isNull('p.endTime')
                )
            )
            ->getQuery();
    }
}
