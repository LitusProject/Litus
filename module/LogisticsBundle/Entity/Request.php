<?php

namespace LogisticsBundle\Entity;

use CommonBundle\Entity\User\Person\Academic;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use LogisticsBundle\Entity\Order;
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
     * @var boolean True if the request has been removed, false if not.
     *
     * @ORM\Column(type="boolean")
     */
    private $removed;

    /**
     * @var Academic The contact used in this order
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person\Academic")
     * @ORM\JoinColumn(name="contact", referencedColumnName="id")
     */
    private $contact;

//    START
    /**
     * @var string The type of the request
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $requestType;

    /**
     * @var Order The order this is associated with
     *
     * @ORM\ManyToOne(targetEntity="LogisticsBundle\Entity\Order")
     * @ORM\JoinColumn(name="referenced_order", referencedColumnName="id", nullable=true)
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
     * @var string The type of the request
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $flag;

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
//  END

    /**
//     * @param Academic   $contact
//     * @param Order      $order
//     * @param string     $requestType
//     * @param Order|null $editOrder
     */
//    WISSELEN
//
//    public function __construct(Academic $contact, Order $order, $requestType, Order $editOrder = null)
    public function __construct(Academic $contact)
    {
        $this->creationTime = new DateTime();
        $this->handled = false;
        $this->removed = false;
        $this->contact = $contact;
//        START
//        $this->referencedOrder = $order;
//        $this->setRequestType($requestType);
//        $this->editOrder = $editOrder;
//        END
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return boolean
     */
    public function isHandled()
    {
        return $this->handled;
    }

    /**
     * @param boolean $handled
     */
    public function setHandled(bool $handled)
    {
        $this->handled = $handled;
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
     * @return boolean
     */
    public function isRemoved()
    {
        return $this->removed;
    }

    /**
     * @param boolean $removed
     */
    public function setRemoved(bool $removed)
    {
        $this->removed = $removed;
    }

    /**
     * @return boolean
     */
    public function remove()
    {
        $this->removed = true;

        return true;
    }

    /**
     * @return DateTime
     */
    public function getCreationTime()
    {
        return $this->creationTime;
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

//    START
    /**
     * @return string
     */
    public function getStatus()
    {
        $result = 'pending';

        if ($this->handled) {
            $result = 'rejected';

            if ($this->getEditOrder()->isApproved()) {
                $result = 'approved';
            }
        } elseif ($this->removed) {
            $result = 'removed';
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
     * @return Order
     */
    public function getRecentOrder()
    {
        return $this->getEditOrder() ?? $this->getOrder();
    }

    /**
     * @return string
     */
    public function getRequestType()
    {
        return $this->requestType;
    }

    /**
     * @return string
     */
    public function getFlag()
    {
        return $this->flag;
    }

    /**
     * @param string $flag
     * @return self
     */
    public function setFlag(string $flag)
    {
        $this->flag = $flag;
        return $this;
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

            case 'edit' || 'edit reject':
                $this->getOrder()->remove();
                $this->getEditOrder()->approve();
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
        if ($this->getEditOrder()) {
            $this->getEditOrder()->reject();
        } else {
            $this->getOrder()->reject();
        }
        return $this;
    }
//    END
}
