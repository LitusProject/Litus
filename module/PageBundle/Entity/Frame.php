<?php

namespace PageBundle\Entity;


use PageBundle\Entity\Node\CategoryPage;
use PageBundle\Entity\Node\Page;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity represents a frame in a CategoryPage.
 *
 * @ORM\Entity(repositoryClass="PageBundle\Repository\Frame")
 * @ORM\Table(name="frames_frames")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="inheritance_type", type="string")
 * @ORM\DiscriminatorMap({
 *      "bigframe"="PageBundle\Entity\Frame\BigFrame",
 *      "smallframedescription"="PageBundle\Entity\Frame\SmallFrameDescription",
 *      "smallframeposter"="PageBundle\Entity\Frame\SmallFramePoster"
 * })
 */
abstract class Frame
{
    /**
     * @var integer The ID of this frame
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var CategoryPage The frame's categoryPage
     *
     * @ORM\ManyToOne(targetEntity="PageBundle\Entity\Node\CategoryPage")
     * @ORM\JoinColumn(name="categoryPage", referencedColumnName="id")
     */
    private $categoryPage;

    /**
     * @var Page|Link The frame's page or link to refer to
     *
     * @ORM\JoinColumn(name="link_to", referencedColumnName="id")
     */
    private $linkTo;

    /**
     * @var boolean reflects if the frame is active.
     *
     * @ORM\Column(name="active", type="boolean", options={"default" = true})
     */
    private $active;

    public function __construct()
    {
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return CategoryPage
     */
    public function getCategoryPage()
    {
        return $this->categoryPage;
    }

    /**
     * @param CategoryPage $categoryPage
     * @return self
     */
    public function setCategoryPage(CategoryPage $categoryPage)
    {
        $this->categoryPage = $categoryPage;

        return $this;
    }

    /**
     * @return Page|Link
     */
    public function getLinkTo()
    {
        return $this->linkTo;
    }

    /**
     * @param Page|Link $linkTo
     * @return self
     */
    public function setLinkTo($linkTo)
    {
        $this->linkTo = $linkTo;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     * @return self
     */
    public function setActive(bool $active)
    {
        $this->active = $active;
        return $this;
    }
}
