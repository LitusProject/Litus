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
     * @var bool true if video is to be shown on home page
     *
     * @ORM\Column(name="show_on_home_page", type="boolean")
     */
    private $showOnHomePage;

    /**
     * Creates a new video with the given title
     *
     * @param string $title The title of this video
     */
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
        if (str_contains($url, "youtu") && !str_contains($url, "embed")){
            $yt_id = explode("?v=",$url)[1];
            $url = "https://youtube.com/embed/" . $yt_id;
        }

        $this->url = $url;

        return $this;
    }

    /**
     * @return bool
     */
    public function getShowOnHomePage(){
        return $this->showOnHomePage;
    }

    /**
     * @param bool $showOnHomePage
     * @return self
     */
    public function setShowOnHomePage($showOnHomePage){
        $this->showOnHomePage = $showOnHomePage;
        return $this;
    }
}
