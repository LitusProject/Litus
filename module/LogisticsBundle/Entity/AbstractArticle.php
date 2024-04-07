<?php

namespace LogisticsBundle\Entity;

use DateTime;

/**
 * The abstract class for a general article
 * Inheritors:
 *  - InventoryArticle
 *  - FlesserkeArticle
 *
 * @ORM\MappedSuperclass
 *
 */
abstract class AbstractArticle
{
    /**
     * @var string The name of the article
     *
     * @ORM\Column(type="string")
     */
    private string $name;

    /**
     * @var integer The total amount of owned articles
     *
     * @ORM\Column(name="amount", type="integer")
     */
    private int $amount;

    /**
     * @var DateTime The date this article was last updated
     *
     * @ORM\Column(name="update_date", type="datetime")
     */
    private DateTime $updateDate;

    /**
     * @var string A comment which is also visible for people outside the article's unit(s)
     *
     * @ORM\Column(name="external_comment", type="text", nullable=true)
     */
    private string $externalComment;

    /**
     * @var string A comment which is only visible for people inside the article's unit(s)
     *
     * @ORM\Column(name="internal_comment", type="text", nullable=true)
     */
    private string $internalComment;
}
