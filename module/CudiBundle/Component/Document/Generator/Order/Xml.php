<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Component\Document\Generator\Order;

use CommonBundle\Component\Util\File\TmpFile,
    CommonBundle\Component\Util\Xml\Generator,
    CommonBundle\Component\Util\Xml\Object,
    CudiBundle\Component\Document\Generator\Front as FrontGenerator,
    CudiBundle\Entity\Stock\Order\Order,
    CudiBundle\Entity\Stock\Order\Item,
    Doctrine\ORM\EntityManager,
    ZipArchive;

class Xml
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @var \CudiBundle\Entity\Stock\Order
     */
    private $_order;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \CudiBundle\Entity\Stock\Order $order The order
     */
    public function __construct(EntityManager $entityManager, Order $order)
    {
        $this->_order = $order;
        $this->_entityManager = $entityManager;
    }

    /**
     * Generate an archive to download.
     *
     * @param \CommonBundle\Component\Util\TmpFile $archive The file to write to
     */
    public function generateArchive(TmpFile $archive)
    {
        $filePath = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.file_path');

        $zip = new ZipArchive();

        foreach($this->_order->getItems() as $item) {
            if (!$item->getArticle()->getMainArticle()->isInternal())
                continue;

            $zip->open($archive->getFileName(), ZIPARCHIVE::CREATE);
            $xmlFile = new TmpFile();
            $this->_generateXml($item, $xmlFile);

            $file = new TmpFile();
            $document = new FrontGenerator($this->_entityManager, $item->getArticle(), $file);
            $document->generate();

            $zip->addFile($file->getFilename(), 'front_' . $item->getArticle()->getId() . '.pdf');

            $mappings = $this->_entityManager
                ->getRepository('CudiBundle\Entity\File\Mapping')
                ->findAllPrintableByArticle($item->getArticle()->getMainArticle());

            $zip->addFile($xmlFile->getFilename(), $item->getId() . '.xml');
            foreach($mappings as $mapping)
                $zip->addFile($filePath . $mapping->getFile()->getPath(), $mapping->getFile()->getName());

            $zip->close();
        }
    }

    private function _generateXml(Item $item, TmpFile $tmpFile)
    {
        $configs = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Config');

        $xml = new Generator($tmpFile);

        $num = 1;

        $attachments = array(
            new Object(
                'Attachment',
                array(
                    'AttachmentKey' => 'File' . $num++,
                    'FileName' => 'front_' . $item->getArticle()->getId() . '.pdf',
                ),
                null
            )
        );

        $mappings = $this->_entityManager
            ->getRepository('CudiBundle\Entity\File\Mapping')
            ->findAllByArticle($item->getArticle()->getMainArticle());
        foreach($mappings as $mapping) {
            $attachments[] = new Object(
                'Attachment',
                array(
                    'AttachmentKey' => 'File' . $num++,
                    'FileName' => $mapping->getFile()->getName()
                ),
                null
            );
        }

        switch($item->getArticle()->getMainArticle()->getBinding()->getCode()) {
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
                    'ItemKey' => 'titel'
                ),
                array(
                    new Object(
                        'LastUsedValue',
                        null,
                        $item->getArticle()->getMainArticle()->getTitle()
                    )
                )
            ),
            new Object(
                'ItemValue',
                array(
                    'ItemKey' => 'aantal'
                ),
                array(
                    new Object(
                        'LastUsedValue',
                        null,
                        (string) $item->getNumber()
                    )
                )
            ),
            new Object(
                'ItemValue',
                array(
                    'ItemKey' => 'barcode'
                ),
                array(
                    new Object(
                        'LastUsedValue',
                        null,
                        (string) $item->getArticle()->getBarcode()
                    )
                )
            ),
            new Object(
                'ItemValue',
                array(
                    'ItemKey' => 'afwerking'
                ),
                array(
                    new Object(
                        'LastUsedValue',
                        null,
                        $binding
                    )
                )
            ),
            new Object(
                'ItemValue',
                array(
                    'ItemKey' => 'kleur'
                ),
                array(
                    new Object(
                        'LastUsedValue',
                        null,
                        $item->getArticle()->getMainArticle()->getNbColored() > 0 ? 'kleur' : 'zwart/wit'
                    )
                )
            ),
            new Object(
                'ItemValue',
                array(
                    'ItemKey' => 'zijde'
                ),
                array(
                    new Object(
                        'LastUsedValue',
                        null,
                        (string) $item->getArticle()->getMainArticle()->isRectoVerso() ? 'Recto-Verso' : 'Recto'
                    )
                )
            ),
            new Object(
                'ItemValue',
                array(
                    'ItemKey' => 'TypeDrukOpdracht'
                ),
                array(
                    new Object(
                        'LastUsedValue',
                        null,
                        'Cursus'
                    )
                )
            ),
            new Object(
                'ItemValue',
                array(
                    'ItemKey' => 'DatumOpdrachtKlaar'
                ),
                array(
                    new Object(
                        'LastUsedValue',
                        null,
                        $this->_order->getDeliveryDate()->format('d/m/Y')
                    )
                )
            ),
            new Object(
                'ItemValue',
                array(
                    'ItemKey' => 'Referentie'
                ),
                array(
                    new Object(
                        'LastUsedValue',
                        null,
                        (string) 'eigen cursus'
                    )
                )
            ),
            new Object(
                'ItemValue',
                array(
                    'ItemKey' => 'Opmerking'
                ),
                array(
                    new Object(
                        'LastUsedValue',
                        null,
                        ''
                    )
                )
            )
        );

        $jobId = $this->_entityManager
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
                            'JobID' => str_replace('{{ date }}', $this->_order->getDateOrdered()->format('YmdHi'), $jobId)
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
                            )
                        )
                    )
                )
            )
        );
    }
}
