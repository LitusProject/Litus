<?php

namespace CudiBundle\Component\Document\Generator;

use CommonBundle\Entity\General\AcademicYear;
use CudiBundle\Entity\Sale\Article;
use CudiBundle\Entity\Sale\Article\Discount\Discount;
use Doctrine\ORM\EntityManager;

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
//        $headers = array(
//            'Title',
//            'Author',
//            'Barcode',
//            'Sell Price',
//            'Stock',
//            'Code',
//            'Vak',
//            'Sold 21-22',
//            'Pages Black White',
//            'Pages Colored',
//            'Recto Verso',
//            'Purchase Price'
//        );

        $headers = array(
            'Title',
            'Barcode',
            'Author',
            'Code',
            'Vak',
            'Sold 21-22',
            'Stock',
            'Pages Black White',
            'Pages Colored',
            'Recto Verso',
            'Purchase Price',
            'Sell Price',
        );

        $organizations = $entityManager
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();

//        foreach (Discount::$possibleTypes as $type) {
//            foreach ($organizations as $organization) {
//                $headers[] = 'Sell Price (' . $type . ' Discounted ' . $organization->getName() . ')';
//            }
//        }

        parent::__construct(
            $headers,
            $this->getData($entityManager, $academicYear, $semester)
        );
    }

    /**
     * @param EntityManager $entityManager
     * @param AcademicYear  $academicYear
     * @param integer       $semester
     */
    private function getData(EntityManager $entityManager, AcademicYear $academicYear, $semester)
    {
//        die(json_encode($academicYear->getId()));
        $articles = $entityManager
            ->getRepository('CudiBundle\Entity\Sale\Article')
            ->findAllByAcademicYear($academicYear, $semester);

        $organizations = $entityManager
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();

        $data = array();
        foreach ($articles as $article) {
            $codes = $this->getSubjectCode($entityManager, $academicYear, $article);
            foreach ($codes as $code) {
                $articleData = array(
                    $article->getMainArticle()->getTitle(),
                    $article->getBarcode(),
                    $article->getMainArticle()->getAuthors(),
                    $code->getSubject()->getCode(),
                    $code->getSubject()->getName(),
                    $this->getNbSold($entityManager, $article, $academicYear),
                    $article->getStockValue(),
                    $this->getPagesBW($entityManager, $article),
                    $this->getPagesColored($entityManager, $article),
                    $this->getRectoVerso($entityManager, $article),
                    number_format($article->getPurchasePrice() / 100, 2),
                    number_format($article->getSellPrice() / 100, 2),
                );
            }

            $discounts = $article->getDiscounts();

//            foreach (array_keys(Discount::$possibleTypes) as $key) {
//                foreach ($organizations as $organization) {
//                    $foundDiscount = null;
//
//                    foreach ($discounts as $discount) {
//                        if ($discount->getRawType() == $key && ($discount->getOrganization() == $organization || $discount->getOrganization() === null)) {
//                            $foundDiscount = $discount;
//                        }
//                    }
//
//                    if ($foundDiscount !== null) {
//                        $articleData[] = number_format($foundDiscount->apply($article->getSellPrice()) / 100, 2);
//                    } else {
//                        $articleData[] = '';
//                    }
//                }
//            }

            $data[] = $articleData;
        }

        return $data;
    }

    private function getSubjectCode(EntityManager $entityManager, AcademicYear $academicYear,Article $saleArticle)
    {
        $article = $entityManager
            ->getRepository('CudiBundle\Entity\Article')
            ->find($saleArticle->getMainArticle());

        $codes = $entityManager
            ->getRepository('CudiBundle\Entity\Article\SubjectMap')
            ->findAllByArticleAndAcademicYearQuery($article, $academicYear)
            ->getResult();

        return $codes;
    }

    private function getPagesBW(EntityManager $entityManager, Article $saleArticle)
    {
        $article = $entityManager
            ->getRepository('CudiBundle\Entity\Article')
            ->find($saleArticle->getMainArticle());

        if ($article->isExternal())
            return null;
        else {
            $internal = $entityManager
                ->getRepository('CudiBundle\Entity\Article\Internal')
                ->find($saleArticle->getMainArticle());

            return $internal->getNbBlackAndWhite();
        }
    }

    private function getPagesColored(EntityManager $entityManager, Article $saleArticle)
    {
        $article = $entityManager
            ->getRepository('CudiBundle\Entity\Article')
            ->find($saleArticle->getMainArticle());

        if ($article->isExternal())
            return null;
        else {
            $internal = $entityManager
                ->getRepository('CudiBundle\Entity\Article\Internal')
                ->find($saleArticle->getMainArticle());

            return $internal->getNbColored();
        }
    }

    private function getNbSold(EntityManager $entityManager, Article $saleArticle, AcademicYear $academicYear)
    {
        $sold =  $entityManager
            ->getRepository('CudiBundle\Entity\Sale\SaleItem')
            ->findNumberByArticleAndAcademicYear($saleArticle, $academicYear, null);
        return $sold;
    }

    private function getRectoVerso(Entitymanager $entityManager, Article $saleArticle)
    {
        $article = $entityManager
            ->getRepository('CudiBundle\Entity\Article')
            ->find($saleArticle->getMainArticle());

        if ($article->isExternal())
            return null;
        else {
            $internal = $entityManager
                ->getRepository('CudiBundle\Entity\Article\Internal')
                ->find($saleArticle->getMainArticle());

            return $internal->isRectoVerso();
        }
    }
}
