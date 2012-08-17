<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Entity\Acl;

use Doctrine\ORM\EntityManager;

/**
 * Class that represents a resource that can get accessed and/or manipulated, for example, a forum post, or a contact
 * form.
 *
 * @Entity(repositoryClass="CommonBundle\Repository\Acl\Resource")
 * @Table(name="acl.resources")
 */
class Resource
{
    /**
     * @var string $name The name of this resource
     *
     * @Id
     * @Column(type="string")
     */
    private $name;

    /**
     * @var \CommonBundle\Entity\Acl\Resource The parent of this resource
     *
     * @ManyToOne(targetEntity="CommonBundle\Entity\Acl\Resource")
     * @JoinColumn(name="parent", referencedColumnName="name")
     */
    private $parent;

    /**
     * @param string $name The name of the resource
     * @param \CommonBundle\Entity\Acl\Resource $parent The parent of the resource, or null if there is no parent
     */
    public function __construct($name, Resource $parent = null)
    {
        $this->name = $name;
        $this->parent = $parent;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return \CommonBundle\Entity\Acl\Resource
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Retrieves this resource's children.
     *
     * @param \Doctrine\EntityManager $entityManager The EntityManager instance
     * @return array
     */
    public function getChildren(EntityManager $entityManager)
    {
        return $entityManager
                ->getRepository('CommonBundle\Entity\Acl\Resource')
                ->findByParent($this->getName());
    }

    /**
     * Retrieves this resource's actions.
     *
     * @param \Doctrine\EntityManager $entityManager The EntityManager instance
     * @return array
     */
    public function getActions(EntityManager $entityManager)
    {
        return $entityManager
                ->getRepository('CommonBundle\Entity\Acl\Action')
                ->findByResource($this->getName());
    }
}
