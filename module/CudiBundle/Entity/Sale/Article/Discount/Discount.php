<?php

namespace CudiBundle\Entity\Sale\Article\Discount;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\General\Organization;
use CommonBundle\Entity\User\Person;
use CudiBundle\Entity\Sale\Article;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sale\Article\Discount\Discount")
 * @ORM\Table(name="cudi_sale_articles_discounts_discounts")
 */
class Discount
{
    /**
     * @var integer The ID of the discount
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Template The template of the discount
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sale\Article\Discount\Template")
     * @ORM\JoinColumn(name="template", referencedColumnName="id")
     */
    private $template;

    /**
     * @var integer The value of the discount
     *
     * @ORM\Column(type="bigint", nullable=true)
     */
    private $value;

    /**
     * @var string The method of this discount (percentage, fixed, override)
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $method;

    /**
     * @var string The type of discount (member, acco)
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $type;

    /**
     * @var string The type of rounding
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $rounding;

    /**
     * @var boolean Apply the discount only once
     *
     * @ORM\Column(name="apply_once", type="boolean", nullable=true)
     */
    private $applyOnce;

    /**
     * @var Article The article of the discount
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sale\Article")
     * @ORM\JoinColumn(name="article", referencedColumnName="id")
     */
    private $article;

    /**
     * @var Organization|null The organization for the discount
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Organization")
     * @ORM\JoinColumn(name="organization", referencedColumnName="id")
     */
    private $organization;

    /**
     * @var array The possible types of a discount
     */
    public static $possibleTypes = array(
        'member' => 'Member',
        'acco'   => 'Acco',
    );

    /**
     * @var array The possible methods of a discount
     */
    public static $possibleMethods = array(
        'percentage' => 'Percentage',
        'fixed'      => 'Fixed',
        'override'   => 'Override',
    );

    /**
     * @var array The possible methods of rounding
     */
    public static $possibleRoundings = array(
        'none' => array(
            'name'  => 'None',
            'value' => '1',
            'type'  => 'up',
        ),
        '0.05_up' => array(
            'name'  => '0.05 (up)',
            'value' => '5',
            'type'  => 'up',
        ),
        '0.05_down' => array(
            'name'  => '0.05 (down)',
            'value' => '5',
            'type'  => 'down',
        ),
        '0.10_up' => array(
            'name'  => '0.10 (up)',
            'value' => '10',
            'type'  => 'up',
        ),
        '0.10_down' => array(
            'name'  => '0.10 (down)',
            'value' => '10',
            'type'  => 'down',
        ),
        '0.50_up' => array(
            'name'  => '0.50 (up)',
            'value' => '50',
            'type'  => 'up',
        ),
        '0.50_down' => array(
            'name'  => '0.50 (down)',
            'value' => '50',
            'type'  => 'down',
        ),
    );

    /**
     * @param Article $article The article of the discount
     */
    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    /**
     * @param Template $template The template of the discount
     *
     * @return self
     */
    public function setTemplate(Template $template)
    {
        $this->template = $template;
        $this->value = null;
        $this->method = null;
        $this->type = null;

        return $this;
    }

    /**
     * @throws InvalidArgumentException
     *
     * @param integer           $value        The value of the discount
     * @param string            $method       The method of the discount
     * @param string            $type         The type of the discount
     * @param string            $rounding     The type of the rounding
     * @param boolean           $applyOnce    Apply the discount only once
     * @param Organization|null $organization The organization for the discount
     *
     * @return self
     */
    public function setDiscount($value, $method, $type, $rounding, $applyOnce, Organization $organization = null)
    {
        if (!self::isValidDiscountType($type)) {
            throw new InvalidArgumentException('The discount type is not valid.');
        }

        if (!self::isValidDiscountMethod($method)) {
            throw new InvalidArgumentException('The discount method is not valid.');
        }

        if (!self::isValidRoundingType($rounding)) {
            throw new InvalidArgumentException('The rounding type is not valid.');
        }

        $this->template = null;
        $this->value = (int) (str_replace(',', '.', $value) * 100);
        $this->method = $method;
        $this->type = $type;
        $this->rounding = $rounding;
        $this->applyOnce = $applyOnce;
        $this->organization = $organization;

        return $this;
    }

    /**
     * @param  string $type
     * @return boolean
     */
    public static function isValidDiscountType($type)
    {
        return array_key_exists($type, self::$possibleTypes);
    }

    /**
     * @param  string $method
     * @return boolean
     */
    public static function isValidDiscountMethod($method)
    {
        return array_key_exists($method, self::$possibleMethods);
    }

    /**
     * @param  string $rounding
     * @return boolean
     */
    public static function isValidRoundingType($rounding)
    {
        return array_key_exists($rounding, self::$possibleRoundings);
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Template
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @return integer|null
     */
    public function getValue()
    {
        if (!isset($this->value) && isset($this->template)) {
            return $this->template->getValue();
        }

        return $this->value;
    }

    /**
     * @return string|null
     */
    public function getMethod()
    {
        if (!isset($this->method) && isset($this->template)) {
            return $this->template->getMethod();
        }

        return $this->method;
    }

    /**
     * @return string|null
     */
    public function getType()
    {
        if (!isset($this->type) && isset($this->template)) {
            return $this->template->getType();
        }

        return self::$possibleTypes[$this->type];
    }

    /**
     * @return string|null
     */
    public function getRawType()
    {
        if (!isset($this->type) && isset($this->template)) {
            return $this->template->getRawType();
        }

        return $this->type;
    }

    /**
     * @return Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @return Organization|null
     */
    public function getOrganization()
    {
        if (!isset($this->organization) && isset($this->template)) {
            return $this->template->getOrganization();
        }

        return $this->organization;
    }

    /**
     * @return string|null
     */
    public function getRounding()
    {
        if (!isset($this->rounding) && isset($this->template)) {
            return $this->template->getRounding();
        } elseif (isset($this->rounding)) {
            return self::$possibleRoundings[$this->rounding]['name'];
        }
    }

    /**
     * @return boolean|null
     */
    public function applyOnce()
    {
        if (!isset($this->applyOnce) && isset($this->template)) {
            return $this->template->applyOnce();
        }

        return $this->applyOnce;
    }

    /**
     * @param integer $price
     *
     * @return integer
     */
    public function apply($price)
    {
        $value = 0;
        switch ($this->getMethod()) {
            case 'percentage':
                $value = round($price * (10000 - $this->getValue()) / 10000);
                break;
            case 'fixed':
                $value = $price - $this->getValue();
                break;
            case 'override':
                $value = $this->getValue();
                break;
        }

        if ($this->rounding) {
            $rounding = self::$possibleRoundings[$this->rounding];

            if ($rounding['type'] == 'up') {
                $value = ceil($value / $rounding['value']) * $rounding['value'];
            } else {
                $value = floor($value / $rounding['value']) * $rounding['value'];
            }
        }

        return (int) $value;
    }

    /**
     * @param EntityManager $entityManager
     * @param Article       $article
     * @param Person        $person
     * @param AcademicYear  $academicYear
     *
     * @return boolean
     */
    private function alreadyApplied(EntityManager $entityManager, Article $article, Person $person, AcademicYear $academicYear)
    {
        return $entityManager->getRepository('CudiBundle\Entity\Sale\SaleItem')
            ->findOneByArticleAndPersonAndDiscountType($article, $person, $this->getRawType(), $academicYear) != null;
    }

    /**
     * @param EntityManager $entityManager
     * @param Article       $article
     * @param Person        $person
     * @param AcademicYear  $academicYear
     *
     * @return boolean
     */
    public function canBeApplied(EntityManager $entityManager, Article $article, Person $person, AcademicYear $academicYear)
    {
        if ($this->applyOnce() && $this->alreadyApplied($entityManager, $article, $person, $academicYear)) {
            return false;
        }
        if ($this->getType() == 'member') {
            if (!$person->isMember($academicYear)) {
                return false;
            }

            if ($this->getOrganization() !== null) {
                $organization = $entityManager->getRepository('CommonBundle\Entity\User\Person\Organization\AcademicYearMap')
                    ->findOneByAcademicAndAcademicYear($person, $academicYear);
                if ($organization == null) {
                    return false;
                }
                if ($organization != $this->getOrganization()) {
                    return false;
                }
            }
        }

        return true;
    }
}
