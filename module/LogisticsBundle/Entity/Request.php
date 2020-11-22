<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace LogisticsBundle\Entity;

use LogisticsBundle\Entity\Order;
use CommonBundle\Entity\User\Person\Academic;
use LogisticsBundle\Entity\Article;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use RuntimeException;

/**
 * This entity stores a request for an order.
 *
 * @ORM\Entity(repositoryClass="LogisticsBundle\Repository\Request")
 * @ORM\Table(name="logistics_request")
 */
class Request
{
    /**
     * @var integer request's ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var Academic The contact used in this order
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person\Academic")
     * @ORM\JoinColumn(name="contact", referencedColumnName="id")
     */
    private $contact;

    /**
     * @var DateTime The time of creation of this node
     *
     * @ORM\Column(name="creation_time", type="datetime")
     */
    private $creationTime;

    /**
     * @var boolean True if the request has been handled, false if not.
     *
     * @ORM\Column(type="boolean")
     */
    private $handled;

    /**
     * @var string The type of the request
     *
     * @ORM\Column(type="text")
     */
    private $requestType;

    /**
     * @var Order The order this is associated with
     *
     * @ORM\ManyToOne(targetEntity="LogisticsBundle\Entity\Order")
     * @ORM\JoinColumn(name="referenced_order", referencedColumnName="id")
     */
    private $referencedOrder;

    /**
     * @var Order The new order
     *
     * @ORM\ManyToOne(targetEntity="LogisticsBundle\Entity\Order")
     * @ORM\JoinColumn(name="edit_order", referencedColumnName="id", nullable=true)
     */
    private $editOrder;

    /**
     * @var string The type of the request
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $rejectMessage;

    /**
     * @static
     * @var array All the possible requests allowed
     */
    public static $possibleRequests = array(
        'edit'        => 'edit',
        'edit reject' => 'edit reject',
        'add'         => 'add',
        'delete'      => 'delete',
    );

    /**
     * @param Academic $contact
     * @param Order $order
     * @param string $requestType
     * @param Order|null $editOrder
     */
    public function __construct(Academic $contact, Order $order, $requestType, Order $editOrder = null)
    {
        $this->creationTime = new DateTime();
        $this->contact = $contact;
        $this->handled = false;
        $this->referencedOrder = $order;
        $this->setRequestType($requestType);
        $this->editOrder = $editOrder;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param  Academic $contact
     * @return Request
     */
    public function setContact($contact)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * @return Academic
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @return DateTime
     */
    public function getCreationTime()
    {
        return $this->creationTime;
    }

    /**
     * @return boolean
     */
    public function handled()
    {
        $this->handled = true;

        return true;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        $result = 'pending';

        if ($this->handled) {
            $result = 'rejected';

            if ($this->getOrder()->isApproved()) {
                $result = 'approved';
            }
        }

        return $result;
    }

    /**
     * @param string $type
     */
    private function setRequestType($type)
    {
        if (!in_array($type, self::$possibleRequests)) {
            throw new RuntimeException('The requested type does not exist for the order');
        }

        $this->requestType = $type;
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->referencedOrder;
    }

    /**
     * @return string
     */
    public function getRejectMessage()
    {
        return $this->rejectMessage;
    }

    /**
     * @return Order
     */
    public function getEditOrder()
    {
        return $this->editOrder;
    }

    /**
     * @return string
     */
    public function getRequestType()
    {
        return $this->requestType;
    }

    /**
     * @return $this
     */
    public function approveRequest()
    {
        switch ($this->requestType) {
            case 'add':
                $this->getOrder()->approve();
                break;

            case 'edit':
                $this->getOrder()->approve();
                $this->getEditOrder()->remove();
                break;

            case 'edit reject':
                $this->getOrder()->approve();
                if (($editOrder = $this->getEditOrder())!== null)
                    $editOrder->remove();
                break;

            case 'delete':
                $this->getOrder()->remove();
                break;

            default:
                break;
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function rejectRequest($message)
    {
        $this->rejectMessage = $message;
        return $this;
    }
}
