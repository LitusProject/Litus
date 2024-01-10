<?php

namespace CudiBundle\Component\Document\Generator;

use CommonBundle\Entity\General\AcademicYear;
use CudiBundle\Entity\Sale\Article;
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
    public function __construct(EntityManager $entityManager, AcademicYear $academicYear, int $semester, bool $common)
    {
        $headers = array(
            'Title',
            'Barcode',
            'Supplier',
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
            'Name Contact Person',
            'E-mail Contact Person'
        );

        parent::__construct(
            $headers,
            $this->getData($entityManager, $academicYear, $semester, $common)
        );
    }

    /**
     * @param EntityManager $entityManager
     * @param AcademicYear  $academicYear
     * @param integer       $semester
     */
    private function getData(EntityManager $entityManager, AcademicYear $academicYear, int $semester, bool $common)
    {
        $articles = $entityManager
            ->getRepository('CudiBundle\Entity\Sale\Article')
            ->findAllByAcademicYear($academicYear, $semester);

        $data = array();
        foreach ($articles as $article) {
            $codes = $this->getSubjectCode($entityManager, $academicYear, $article);
            $articleData = array();
            if ($codes) {
                foreach ($codes as $code) {
                    $articleData = array(
                        $article->getMainArticle()->getTitle(),
                        $article->getBarcode(),
                        $article->getSupplier()->getName(),
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
                        $article->getMainArticle()->getNameContact(),
                        $article->getMainArticle()->getEmailContact()
                    );
                }
            } elseif ($common)  {
                $articleData = array(
                    $article->getMainArticle()->getTitle(),
                    $article->getBarcode(),
                    $article->getSupplier()->getName(),
                    $article->getMainArticle()->getAuthors(),
                    '',
                    '',
                    $this->getNbSold($entityManager, $article, $academicYear),
                    $article->getStockValue(),
                    $this->getPagesBW($entityManager, $article),
                    $this->getPagesColored($entityManager, $article),
                    $this->getRectoVerso($entityManager, $article),
                    number_format($article->getPurchasePrice() / 100, 2),
                    number_format($article->getSellPrice() / 100, 2),
                    $article->getMainArticle()->getNameContact(),
                    $article->getMainArticle()->getEmailContact()
                );
            }

            if (!in_array($articleData, $data)) {
                $data[] = $articleData;
            }
        }
        return $data;
    }

    private function getSubjectCode(EntityManager $entityManager, AcademicYear $academicYear, Article $saleArticle)
    {
        $article = $entityManager
            ->getRepository('CudiBundle\Entity\Article')
            ->find($saleArticle->getMainArticle());

        return $entityManager
            ->getRepository('CudiBundle\Entity\Article\SubjectMap')
            ->findAllByArticleAndAcademicYearQuery($article, $academicYear)
            ->getResult();
    }

    private function getPagesBW(EntityManager $entityManager, Article $saleArticle)
    {
        $article = $entityManager
            ->getRepository('CudiBundle\Entity\Article')
            ->find($saleArticle->getMainArticle());

        if ($article->isExternal()) {
            return null;
        } else {
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

        if ($article->isExternal()) {
            return null;
        } else {
            $internal = $entityManager
                ->getRepository('CudiBundle\Entity\Article\Internal')
                ->find($saleArticle->getMainArticle());

            return $internal->getNbColored();
        }
    }

    private function getNbSold(EntityManager $entityManager, Article $saleArticle, AcademicYear $academicYear)
    {
        return $entityManager
            ->getRepository('CudiBundle\Entity\Sale\SaleItem')
            ->findNumberByArticleAndAcademicYear($saleArticle, $academicYear, null);
    }

    private function getRectoVerso(Entitymanager $entityManager, Article $saleArticle)
    {
        $article = $entityManager
            ->getRepository('CudiBundle\Entity\Article')
            ->find($saleArticle->getMainArticle());

        if ($article->isExternal()) {
            return null;
        } else {
            $internal = $entityManager
                ->getRepository('CudiBundle\Entity\Article\Internal')
                ->find($saleArticle->getMainArticle());

            return $internal->isRectoVerso();
        }
    }
}
