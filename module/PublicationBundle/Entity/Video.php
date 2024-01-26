<?php

namespace PublicationBundle\Entity;

use DateTime;
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
     * @var DateTime The datum of upload
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var boolean true if video is to be shown on home page
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

    public function getEmbedUrl()
    {
        $url = $this->url;
        if (str_contains($url, 'youtu') && !str_contains($url, 'embed')) {
            $yt_id = explode('?v=', $url)[1];
            return 'https://youtube.com/embed/' . $yt_id;
        }
        return $url;
    }

    /**
     * @param  string $url
     * @return self
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     * @return self
     */
    public function setDate(DateTime $date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getShowOnHomePage()
    {
        return $this->showOnHomePage;
    }

    /**
     * @return boolean
     */
    public function isShowOnHomePage()
    {
        return $this->showOnHomePage;
    }

    /**
     * @param boolean $showOnHomePage
     * @return self
     */
    public function setShowOnHomePage($showOnHomePage)
    {
        $this->showOnHomePage = $showOnHomePage;
        return $this;
    }
}
