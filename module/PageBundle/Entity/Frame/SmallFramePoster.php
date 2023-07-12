<?php

namespace PageBundle\Entity\Frame;

use Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the small frame with poster item.
 *
 * @ORM\Entity(repositoryClass="PageBundle\Repository\Frame\SmallFramePoster")
 * @ORM\Table(name="frames_smallposter")
 */
class SmallFramePoster extends \PageBundle\Entity\Frame
{
    /**
     * @var string The poster of this frame
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $poster;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return string
     */
    public function getPoster()
    {
        return $this->poster;
    }

    /**
     * @param string $poster
     *
     * @return self
     */
    public function setPoster($poster)
    {
        $this->poster = trim($poster, '/');

        return $this;
    }
}