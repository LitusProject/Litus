<?php

namespace CommonBundle\Entity\General\Node\FAQ;

use Doctrine\ORM\Mapping as ORM;
use PageBundle\Entity\Node\Page;

/**
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\General\Node\FAQ\FAQPageMap")
 * @ORM\Table(name="faq_page_map")
 */
class FAQPageMap
{
    /**
     * @var integer The ID of the mapping
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var FAQ The FAQ of the mapping
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Node\FAQ\FAQ")
     * @ORM\JoinColumn(name="referenced_faq", referencedColumnName="id")
     */
    private $referencedFAQ;

    /**
     * @var Page The Page of the mapping
     *
     * @ORM\ManyToOne(targetEntity="PageBundle\Entity\Node\Page")
     * @ORM\JoinColumn(name="referenced_page", referencedColumnName="id")
     */
    private $referencedPage;

    /**
     * OrderArticleMap constructor.
     * @param FAQ  $faq
     * @param Page $page
     */
    public function __construct(FAQ $faq, Page $page)
    {
        $this->referencedFAQ = $faq;
        $this->referencedPage = $page;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return FAQ
     */
    public function getFAQ(): FAQ
    {
        return $this->referencedFAQ;
    }

    /**
     * @return Page
     */
    public function getPage(): Page
    {
        return $this->referencedPage;
    }
}
