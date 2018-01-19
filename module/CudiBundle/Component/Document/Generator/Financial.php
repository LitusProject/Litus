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

namespace CudiBundle\Component\Document\Generator;

use CommonBundle\Component\Util\File\TmpFile,
    CommonBundle\Component\Util\Xml\Generator,
    CommonBundle\Component\Util\Xml\Node,
    CommonBundle\Entity\General\AcademicYear,
    DateTime,
    Doctrine\ORM\EntityManager;

/**
 * Financial
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Financial extends \CommonBundle\Component\Document\Generator\Pdf
{
    /**
     * @var AcademicYear
     */
    private $academicYear;

    /**
     * @param EntityManager $entityManager The EntityManager instance
     * @param AcademicYear  $academicYear
     * @param TmpFile       $file          The file to write to
     */
    public function __construct(EntityManager $entityManager, AcademicYear $academicYear, TmpFile $file)
    {
        $filePath = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.pdf_generator_path');

        parent::__construct(
            $entityManager,
            $filePath . '/financial/articles.xsl',
            $file->getFilename()
        );

        $this->academicYear = $academicYear;
    }

    /**
     * Generate the XML for the fop.
     *
     * @param TmpFile $tmpFile The file to write to.
     */
    protected function generateXml(TmpFile $tmpFile)
    {
        $configs = $this->getConfigRepository();

        $now = new DateTime();
        $organization_short_name = $configs->getConfigValue('organization_short_name');
        $organization_name = $configs->getConfigValue('organization_name');
        $organization_logo = $configs->getConfigValue('organization_logo');
        $cudi_name = $configs->getConfigValue('cudi.name');
        $cudi_mail = $configs->getConfigValue('cudi.mail');
        $person = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person')
            ->findOneById($configs->getConfigValue('cudi.person'));

        $period = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findOneActive();
        $period->setEntityManager($this->getEntityManager());

        $articles = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article')
            ->findAllByAcademicYear($this->academicYear);

        $external = array();
        $internal = array();

        foreach ($articles as $article) {
            $virtualOrdered = $period->getNbVirtualOrdered($article);

            $node = new Node(
                'item',
                null,
                array(
                    new Node(
                        'barcode',
                        null,
                        (string) $article->getBarcode()
                    ),
                    new Node(
                        'title',
                        null,
                        $article->getMainArticle()->getTitle()
                    ),
                    new Node(
                        'author',
                        null,
                        $article->getMainArticle()->getAuthors()
                    ),
                    new Node(
                        'publisher',
                        null,
                        $article->getMainArticle()->getPublishers()
                    ),
                    new Node(
                        'ordered',
                        null,
                        (string) $period->getNbOrdered($article) . ($virtualOrdered > 0 ? '(+ ' . $virtualOrdered . ')' : '')
                    ),
                    new Node(
                        'delivered',
                        null,
                        (string) $period->getNbDelivered($article)
                    ),
                    new Node(
                        'sold',
                        null,
                        (string) $period->getNbSold($article)
                    ),
                    new Node(
                        'stock',
                        null,
                        (string) $article->getStockValue()
                    ),
                )
            );

            if ($article->getMainArticle()->isInternal()) {
                $internal[$article->getBarcode()] = $node;
            } else {
                $external[] = $node;
            }
        }

        ksort($internal);

        $xml = new Generator($tmpFile);

        $xml->append(
            new Node(
                'financial',
                array(
                    'date' => $now->format('d F Y'),
                ),
                array(
                    new Node(
                        'our_union',
                        array(
                            'short_name' => $organization_short_name,
                        ),
                        array(
                            new Node(
                                'name',
                                null,
                                $organization_name
                            ),
                            new Node(
                                'logo',
                                null,
                                $organization_logo
                            ),
                        )
                    ),
                    new Node(
                        'cudi',
                        array(
                            'name' => $cudi_name,
                        ),
                        array(
                             new Node(
                                 'mail',
                                 null,
                                 $cudi_mail
                             ),
                             new Node(
                                 'phone',
                                 null,
                                 $person->getPhoneNumber()
                             ),
                        )
                    ),
                    new Node(
                        'external_items',
                        null,
                        $external
                    ),
                    new Node(
                        'internal_items',
                        null,
                        $internal
                    ),
                )
            )
        );
    }
}
