<?php

namespace CommonBundle\Entity\General;

use CommonBundle\Entity\User\Person;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * This class represents a visit, every page view is a visit
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\General\Visit")
 * @ORM\Table(name="general_visits")
 */
class Visit
{
    /**
     * @var integer The ID of the visit
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \DateTime The timestamp of the visit
     *
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /**
     * @var string The browser used to view the page
     *
     * @ORM\Column(type="string")
     */
    private $browser;

    /**
     * @var string The url of the page view
     *
     * @ORM\Column(type="string")
     */
    private $url;

    /**
     * @var string The request method
     *
     * @ORM\Column(name="request_method", type="string")
     */
    private $requestMethod;

    /**
     * @var string The controller executed
     *
     * @ORM\Column(type="string")
     */
    private $controller;

    /**
     * @var string The action executed
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $action;

    /**
     * @var Person The person that visited the page
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     */
    private $user;

    /**
     * @param string      $browser
     * @param string      $url
     * @param string      $requestMethod
     * @param string      $controller
     * @param string      $action
     * @param Person|null $user
     */
    public function __construct($browser, $url, $requestMethod, $controller, $action, Person $user = null)
    {
        $this->timestamp = new DateTime();
        $this->browser = $browser === null ? '' : substr($browser, 0, 255);
        $this->url = $url;
        $this->requestMethod = $requestMethod;
        $this->controller = $controller;
        $this->action = $action;
        $this->user = $user;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return string
     */
    public function getBrowser()
    {
        return $this->browser;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getRequestMethod()
    {
        return $this->requestMethod;
    }

    /**
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return Person
     */
    public function getUser()
    {
        return $this->user;
    }
}
