<?php

namespace PublicationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a video
 *
 * @ORM\Entity(repositoryClass="PublicationBundle\Repository\Video")
 * @ORM\Table(name="publications_videos")
 */
class Video
{
    /**
     * @var integer The ID of this article
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The title of this video
     *
     * @ORM\Column(name="video_title",type="string", nullable=true)
     */
    private $title;

    /**
     * @var string The link to the video
     *
     * @ORM\Column(name="url", type="string", nullable=true)
     */
    private $url;

    /**
     * Creates a new video with the given title
     *
     * @param string $title The title of this video
     */
    public function __construct($title)
    {
        $this->title = $title;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string The title of this video
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param $title The new title
     * @return Video This
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param  string $url
     * @return self
     */
    public function setUrl($url)
    {
        if (strpos($url, 'http://') !== 0) {
            $url = 'http://' . $url;
        }

        $this->url = $url;

        return $this;
    }
}
