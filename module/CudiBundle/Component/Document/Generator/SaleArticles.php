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
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Component\Document\Generator;

use CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile,
    CommonBundle\Entity\General\AcademicYear,
    CudiBundle\Entity\Sale\Article\Discount\Discount,
    Doctrine\ORM\EntityManager;

/**
 * Sale Articles
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SaleArticles extends \CommonBundle\Component\Document\Generator\Csv
{
    /**
     * @param EntityManager $entityManager The EntityManager instance
     * @param AcademicYear  $academicYear  The academic year
     * @param integer       $semester      The semester
     */
    public function __construct(EntityManager $entityManager, AcademicYear $academicYear, $semester)
    {
        $headers = array(
            'Title',
            'Author',
            'Barcode',
            'Sell Price',
            'Stock',
        );

        $organizations = $entityManager
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();

        foreach (Discount::$POSSIBLE_TYPES as $key => $type) {
            foreach ($organizations as $organization) {
                $headers[] = 'Sell Price (' . $type . ' Discounted ' . $organization->getName() . ')';
            }
        }

        parent::__construct(
            $headers,
            $this->_getData($entityManager, $academicYear, $semester)
        );
    }

    private function _getData(EntityManager $entityManager, AcademicYear $academicYear, $semester)
    {
        $articles = $entityManager
            ->getRepository('CudiBundle\Entity\Sale\Article')
            ->findAllByAcademicYear($academicYear, $semester);

        $organizations = $entityManager
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();

        $data = array();
        foreach ($articles as $article) {
            $articleData = array(
                $article->getMainArticle()->getTitle(),
                $article->getMainArticle()->getAuthors(),
                $article->getBarcode(),
                number_format($article->getSellPrice() / 100, 2),
                $article->getStockValue(),
            );

            $discounts = $article->getDiscounts();

            foreach (Discount::$POSSIBLE_TYPES as $key => $type) {
                foreach ($organizations as $organization) {
                    $foundDiscount = null;

                    foreach ($discounts as $discount) {
                        if ($discount->getRawType() == $key && ($discount->getOrganization() == $organization || null === $discount->getOrganization())) {
                            $foundDiscount = $discount;
                        }
                    }

                    if (null !== $foundDiscount) {
                        $articleData[] = number_format($foundDiscount->apply($article->getSellPrice()) / 100, 2);
                    } else {
                        $articleData[] = '';
                    }
                }
            }

            $data[] = $articleData;
        }

        return $data;
    }
}
