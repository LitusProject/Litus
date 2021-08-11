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

namespace BrBundle\Entity\Contract;

use BrBundle\Entity\Contract;
use BrBundle\Entity\Product\Order\Entry as OrderEntry;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/**
 * An entry of a contract.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Contract\Entry")
 * @ORM\Table(name="br_contracts_entries")
 */
class Entry
{
    /**
     * @var integer A generated ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var Contract The contract to which this entry belongs.
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Contract")
     * @ORM\JoinColumn(name="contract", referencedColumnName="id")
     */
    private $contract;

    /**
     * @var OrderEntry The order entry of which this is an entry in the contract.
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Product\Order\Entry")
     * @ORM\JoinColumn(name="order_entry", referencedColumnName="id")
     */
    private $orderEntry;

    /**
     * @var string The contract text of this product
     *
     * @ORM\Column(name="contract_text", type="text")
     */
    private $contractText;

    /**
     * @var string The contract text of this product in English
     *
     * @ORM\Column(name="contract_text_en", type="text", nullable=true)
     */
    private $contractTextEn;

    /**
     * @var integer The position number of the entry in the contract
     *
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * @var integer The version of the contract this entry belongs too.
     *
     * @ORM\Column(type="integer")
     */
    private $version;

    /**
     * @param Contract   $contract   The contract of which this entry is part.
     * @param OrderEntry $orderEntry The order entry corresponding to this contract entry.
     * @param integer    $position   The position number of the entry in the contract
     * @param integer    $version    The version number of this contract entry
     */
    public function __construct(Contract $contract, OrderEntry $orderEntry, $position, $version)
    {
        $this->contract = $contract;
        $this->orderEntry = $orderEntry;

        $this->setContractText($orderEntry->getProduct()->getContractText('nl'), 'nl');
        $this->setContractText($orderEntry->getProduct()->getContractText('en'), 'en');
        $this->setPosition($position);
        $this->setVersion($version);
    }

    /**
     * @return integer
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param  integer $versionNmbr
     * @return null
     */
    private function setVersion($versionNmbr)
    {
        if ($versionNmbr < 0) {
            throw new InvalidArgumentException('Version number must be larger or equal to zero');
        }

        $this->version = $versionNmbr;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Contract
     */
    public function getContract()
    {
        return $this->contract;
    }

    /**
     * @return OrderEntry
     */
    public function getOrderEntry()
    {
        return $this->orderEntry;
    }

    /**
     * @param boolean $replace
     * @param string  $language
     * @return string
     */
    public function getContractText($language, $replace = true)
    {
        if ($language == null || $language == 'nl') {
            return $this->getContractTextNl($replace);
        }
        return $this->getContractTextEn($replace);
    }

    /**
     * @param string $contractText
     * @param string $language
     * @return Entry
     */
    public function setContractText($contractText, $language)
    {
        if ($language == null || $language == 'nl') {
            return $this->setContractTextNl($contractText);
        } else {
            return $this->setContractTextEn($contractText);
        }
    }

    /**
     * @return string
     */
    public function getContractTextNl($replace = true)
    {
        if ($replace === true) {
            return str_replace('<amount />', (String) $this->getOrderEntry()->getQuantity(), $this->contractText);
        }

        return $this->contractText;
    }

    /**
     * @param  string $contractText
     * @return Entry
     */
    public function setContractTextNl($contractText)
    {
        $this->contractText = $contractText;

        return $this;
    }

    /**
     * @return string
     */
    public function getContractTextEn($replace = true)
    {
        if ($replace === true) {
            return str_replace('<amount />', (String) $this->getOrderEntry()->getQuantity(), $this->contractTextEn);
        }

        return $this->contractTextEn;
    }

    /**
     * @param  string $contractText
     * @return Entry
     */
    public function setContractTextEn($contractText)
    {
        $this->contractTextEn = $contractText;

        return $this;
    }

    /**
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Sets the position to the given value.
     *
     * @throws InvalidArgumentException
     * @param  integer $position
     * @return self
     */
    public function setPosition($position)
    {
        if ($position < 0) {
            throw new InvalidArgumentException('Position must be a positive number');
        }

        $this->position = (int) round($position);

        return $this;
    }
}
