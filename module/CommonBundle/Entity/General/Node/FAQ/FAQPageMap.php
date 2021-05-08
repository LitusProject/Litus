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
