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

namespace CudiBundle\Component\Document\Generator\Order;

use CommonBundle\Component\Util\File\TmpFile;
use CommonBundle\Component\Util\Xml\Generator;
use CommonBundle\Component\Util\Xml\Node;
use CudiBundle\Component\Document\Generator\Front as FrontGenerator;
use CudiBundle\Entity\Article\Internal as InternalArticle;
use CudiBundle\Entity\Stock\Order\Item;
use CudiBundle\Entity\Stock\Order\Order;
use Doctrine\ORM\EntityManager;
use UnexpectedValueException;
use ZipArchive;

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

        $xmlFormat = $this->entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.export_order_format');

        $zip = new ZipArchive();

        foreach ($this->order->getItems() as $item) {
            if (!$item->getArticle()->getMainArticle()->isInternal()) {
                continue;
            }

            $zip->open($archive->getFileName(), ZIPARCHIVE::CREATE);
            $xmlFile = new TmpFile();
            if ($xmlFormat == 'default') {
                $this->generateXml($item, $xmlFile);

                $file = new TmpFile();
                $document = new FrontGenerator($this->entityManager, $item->getArticle(), $file);
                $document->generate();

                $zip->addFile($file->getFilename(), 'front_' . $item->getArticle()->getId() . '.pdf');
                $zip->addFile($xmlFile->getFilename(), $item->getId() . '.xml');
            } elseif ($xmlFormat == 'pmr') {
                $this->generatePmrXml($item, $xmlFile);

                $jobId = $this->entityManager
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.order_job_id');
                $jobId = str_replace('{{ code }}', substr((string) $item->getArticle()->getBarcode(), -5), str_replace('{{ date }}', $this->order->getDateOrdered()->format('Ymd'), $jobId));

                $zip->addFile($xmlFile->getFilename(), $jobId . '.xml');
            } else {
                throw new UnexpectedValueException('unexpected configuration value cudi.order_export_format');
            }

            $mappings = $this->entityManager
                ->getRepository('CudiBundle\Entity\File\Mapping')
                ->findAllPrintableByArticle($item->getArticle()->getMainArticle());

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
            new Node(
                'Attachment',
                array(
                    'AttachmentKey' => 'File' . ($num++),
                    'FileName'      => 'front_' . $item->getArticle()->getId() . '.pdf',
                ),
                null
            ),
        );

        $mappings = $this->entityManager
            ->getRepository('CudiBundle\Entity\File\Mapping')
            ->findAllByArticle($mainArticle);
        foreach ($mappings as $mapping) {
            $attachments[] = new Node(
                'Attachment',
                array(
                    'AttachmentKey' => 'File' . ($num++),
                    'FileName'      => $mapping->getFile()->getName(),
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
            new Node(
                'ItemValue',
                array(
                    'ItemKey' => 'titel',
                ),
                array(
                    new Node(
                        'LastUsedValue',
                        null,
                        $mainArticle->getTitle()
                    ),
                )
            ),
            new Node(
                'ItemValue',
                array(
                    'ItemKey' => 'aantal',
                ),
                array(
                    new Node(
                        'LastUsedValue',
                        null,
                        (string) $item->getNumber()
                    ),
                )
            ),
            new Node(
                'ItemValue',
                array(
                    'ItemKey' => 'barcode',
                ),
                array(
                    new Node(
                        'LastUsedValue',
                        null,
                        (string) $item->getArticle()->getBarcode()
                    ),
                )
            ),
            new Node(
                'ItemValue',
                array(
                    'ItemKey' => 'afwerking',
                ),
                array(
                    new Node(
                        'LastUsedValue',
                        null,
                        $binding
                    ),
                )
            ),
            new Node(
                'ItemValue',
                array(
                    'ItemKey' => 'kleur',
                ),
                array(
                    new Node(
                        'LastUsedValue',
                        null,
                        $mainArticle->getNbColored() > 0 ? 'kleur' : 'zwart/wit'
                    ),
                )
            ),
            new Node(
                'ItemValue',
                array(
                    'ItemKey' => 'zijde',
                ),
                array(
                    new Node(
                        'LastUsedValue',
                        null,
                        (string) $mainArticle->isRectoVerso() ? 'Recto-Verso' : 'Recto'
                    ),
                )
            ),
            new Node(
                'ItemValue',
                array(
                    'ItemKey' => 'TypeDrukOpdracht',
                ),
                array(
                    new Node(
                        'LastUsedValue',
                        null,
                        'Cursus'
                    ),
                )
            ),
            new Node(
                'ItemValue',
                array(
                    'ItemKey' => 'DatumOpdrachtKlaar',
                ),
                array(
                    new Node(
                        'LastUsedValue',
                        null,
                        $this->order->getDeliveryDate()->format('d/m/Y')
                    ),
                )
            ),
            new Node(
                'ItemValue',
                array(
                    'ItemKey' => 'Referentie',
                ),
                array(
                    new Node(
                        'LastUsedValue',
                        null,
                        (string) 'eigen cursus'
                    ),
                )
            ),
            new Node(
                'ItemValue',
                array(
                    'ItemKey' => 'Opmerking',
                ),
                array(
                    new Node(
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
            new Node(
                'Document',
                null,
                array(
                    new Node(
                        'Job',
                        array(
                            'JobID' => str_replace('{{ date }}', $this->order->getDateOrdered()->format('YmdHi'), $jobId),
                        ),
                        array(
                            new Node(
                                'Attachments',
                                null,
                                $attachments
                            ),
                            new Node(
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

    private function generatePmrXml(Item $item, TmpFile $tmpFile)
    {
        $xml = new Generator($tmpFile, 'version="1.0" encoding="utf-8" standalone="yes"');

        $mainArticle = $item->getArticle()->getMainArticle();
        if (!($mainArticle instanceof InternalArticle)) {
            return;
        }

        switch ($mainArticle->getBinding()->getCode()) {
            case 'glued':
                $binding = 'A4vouw';
                break;
            case 'stapled':
                $binding = 'Geniet';
                break;
            default:
                $binding = 'Los en ingepakt in krimpfolie';
                break;
        }

        $jobId = $this->entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.order_job_id');

        $name = $this->entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('organization_short_name');

        $mail = $this->entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.mail');

        setlocale(LC_ALL, 'en_US.UTF8');
        $title = iconv('UTF-8', 'ASCII//TRANSLIT', $item->getArticle()->getMainArticle()->getTitle());

        $orderDetails = array(
            new Node(
                'Jobnummber',
                null,
                str_replace('{{ code }}', substr((string) $item->getArticle()->getBarcode(), -5), str_replace('{{ date }}', $this->order->getDateOrdered()->format('Ymd'), $jobId))
            ),
            new Node(
                'Klantnaam',
                null,
                $name
            ),
            new Node(
                'Klantvoornaam',
                null,
                ''
            ),
            new Node(
                'Klantemail',
                null,
                $mail
            ),
            new Node(
                'Levering',
                null,
                $this->order->getDeliveryDate()->format('d/m/Y')
            ),
            new Node(
                'Levering2',
                null,
                ''
            ),
            new Node(
                'Orderline',
                null,
                '1'
            ),
            new Node(
                'Titel',
                null,
                $title
            ),
            new Node(
                'Barcode',
                null,
                $item->getArticle()->getBarcode()
            ),
            new Node(
                'Categorie',
                null,
                'DOC'
            ),
            new Node(
                'Quantity',
                null,
                (string) $item->getNumber()
            ),
            new Node(
                'Afdruk',
                null,
                $mainArticle->getNbColored() > 0 ? 'kleur' : 'zwart wit'
            ),
            new Node(
                'bedrukking',
                null,
                (string) $mainArticle->isRectoVerso() ? 'Recto-Verso' : 'Recto'
            ),
            new Node(
                'afwerking',
                null,
                $binding
            ),
            new Node(
                'Bestandsnaam',
                null,
                substr((string) $item->getArticle()->getBarcode(), -5) . '.pdf'
            ),
        );

        $xml->append(
            new Node(
                'Order',
                null,
                $orderDetails
            )
        );
    }
}
