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

namespace CudiBundle\Component\Document\Generator\Order;

use CommonBundle\Component\Util\File\TmpFile,
    CommonBundle\Component\Util\Xml\Generator,
    CommonBundle\Component\Util\Xml\Object,
    CudiBundle\Component\Document\Generator\Front as FrontGenerator,
    CudiBundle\Entity\Article\Internal as InternalArticle,
    CudiBundle\Entity\Stock\Order\Item,
    CudiBundle\Entity\Stock\Order\Order,
    Doctrine\ORM\EntityManager,
    ZipArchive;

class Xml
{
    /**
     * @var EntityManager The EntityManager instance
     */
    private $entityManager = null;

    /**
     * @var Order
     */
    private $order;

    /**
     * @param EntityManager $entityManager The EntityManager instance
     * @param Order         $order         The order
     */
    public function __construct(EntityManager $entityManager, Order $order)
    {
        $this->order = $order;
        $this->entityManager = $entityManager;
    }

    /**
     * Generate an archive to download.
     *
     * @param TmpFile $archive The file to write to
     */
    public function generateArchive(TmpFile $archive)
    {
        $filePath = $this->entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.file_path');

        $zip = new ZipArchive();

        foreach ($this->order->getItems() as $item) {
            if (!$item->getArticle()->getMainArticle()->isInternal()) {
                continue;
            }

            $zip->open($archive->getFileName(), ZIPARCHIVE::CREATE);
            $xmlFile = new TmpFile();
            $this->generateXml($item, $xmlFile);

            $file = new TmpFile();
            $document = new FrontGenerator($this->entityManager, $item->getArticle(), $file);
            $document->generate();

            $zip->addFile($file->getFilename(), 'front_' . $item->getArticle()->getId() . '.pdf');

            $mappings = $this->entityManager
                ->getRepository('CudiBundle\Entity\File\Mapping')
                ->findAllPrintableByArticle($item->getArticle()->getMainArticle());

            $zip->addFile($xmlFile->getFilename(), $item->getId() . '.xml');
            foreach ($mappings as $mapping) {
                $zip->addFile($filePath . $mapping->getFile()->getPath(), $mapping->getFile()->getName());
            }

            $zip->close();
        }
    }

    private function generateXml(Item $item, TmpFile $tmpFile)
    {
        $xml = new Generator($tmpFile);

        $mainArticle = $item->getArticle()->getMainArticle();
        if (!($mainArticle instanceof InternalArticle)) {
            return;
        }

        $num = 1;
        $attachments = array(
            new Object(
                'Attachment',
                array(
                    'AttachmentKey' => 'File' . ($num++),
                    'FileName' => 'front_' . $item->getArticle()->getId() . '.pdf',
                ),
                null
            ),
        );

        $mappings = $this->entityManager
            ->getRepository('CudiBundle\Entity\File\Mapping')
            ->findAllByArticle($mainArticle);
        foreach ($mappings as $mapping) {
            $attachments[] = new Object(
                'Attachment',
                array(
                    'AttachmentKey' => 'File' . ($num++),
                    'FileName' => $mapping->getFile()->getName(),
                ),
                null
            );
        }

        switch ($mainArticle->getBinding()->getCode()) {
            case 'glued':
                $binding = 'Ingelijmd';
                break;
            case 'stapled':
                $binding = 'Geniet';
                break;
            default:
                $binding = 'Los en ingepakt in krimpfolie';
                break;
        }

        $itemValues = array(
            new Object(
                'ItemValue',
                array(
                    'ItemKey' => 'titel',
                ),
                array(
                    new Object(
                        'LastUsedValue',
                        null,
                        $mainArticle->getTitle()
                    ),
                )
            ),
            new Object(
                'ItemValue',
                array(
                    'ItemKey' => 'aantal',
                ),
                array(
                    new Object(
                        'LastUsedValue',
                        null,
                        (string) $item->getNumber()
                    ),
                )
            ),
            new Object(
                'ItemValue',
                array(
                    'ItemKey' => 'barcode',
                ),
                array(
                    new Object(
                        'LastUsedValue',
                        null,
                        (string) $item->getArticle()->getBarcode()
                    ),
                )
            ),
            new Object(
                'ItemValue',
                array(
                    'ItemKey' => 'afwerking',
                ),
                array(
                    new Object(
                        'LastUsedValue',
                        null,
                        $binding
                    ),
                )
            ),
            new Object(
                'ItemValue',
                array(
                    'ItemKey' => 'kleur',
                ),
                array(
                    new Object(
                        'LastUsedValue',
                        null,
                        $mainArticle->getNbColored() > 0 ? 'kleur' : 'zwart/wit'
                    ),
                )
            ),
            new Object(
                'ItemValue',
                array(
                    'ItemKey' => 'zijde',
                ),
                array(
                    new Object(
                        'LastUsedValue',
                        null,
                        (string) $mainArticle->isRectoVerso() ? 'Recto-Verso' : 'Recto'
                    ),
                )
            ),
            new Object(
                'ItemValue',
                array(
                    'ItemKey' => 'TypeDrukOpdracht',
                ),
                array(
                    new Object(
                        'LastUsedValue',
                        null,
                        'Cursus'
                    ),
                )
            ),
            new Object(
                'ItemValue',
                array(
                    'ItemKey' => 'DatumOpdrachtKlaar',
                ),
                array(
                    new Object(
                        'LastUsedValue',
                        null,
                        $this->order->getDeliveryDate()->format('d/m/Y')
                    ),
                )
            ),
            new Object(
                'ItemValue',
                array(
                    'ItemKey' => 'Referentie',
                ),
                array(
                    new Object(
                        'LastUsedValue',
                        null,
                        (string) 'eigen cursus'
                    ),
                )
            ),
            new Object(
                'ItemValue',
                array(
                    'ItemKey' => 'Opmerking',
                ),
                array(
                    new Object(
                        'LastUsedValue',
                        null,
                        ''
                    ),
                )
            ),
        );

        $jobId = $this->entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.order_job_id');

        $xml->append(
            new Object(
                'Document',
                null,
                array(
                    new Object(
                        'Job',
                        array(
                            'JobID' => str_replace('{{ date }}', $this->order->getDateOrdered()->format('YmdHi'), $jobId),
                        ),
                        array(
                            new Object(
                                'Attachments',
                                null,
                                $attachments
                            ),
                            new Object(
                                'ItemValues',
                                null,
                                $itemValues
                            ),
                        )
                    ),
                )
            )
        );
    }
}
