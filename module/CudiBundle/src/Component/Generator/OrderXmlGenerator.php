<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
 
namespace CudiBundle\Component\Generator;

use CommonBundle\Component\Util\File\TmpFile,
	CommonBundle\Component\Util\Xml\Generator,
	CommonBundle\Component\Util\Xml\Object,
	CudiBundle\Entity\Stock\Order,
	CudiBundle\Entity\Stock\OrderItem,
	Doctrine\ORM\EntityManager,
	ZipArchive;

class OrderXmlGenerator
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
	 * Create a Order XML Generator.
	 *
	 * @param \Doctrine\ORM\EntityManager $entityManager The entityManager
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
		
		foreach($this->_order->getOrderItems() as $item) {
			if (!$item->getArticle()->isInternal())
				continue;
			
			$zip->open($archive->getFileName(), ZIPARCHIVE::CREATE);
			$xmlFile = new TmpFile();
			$this->_generateXml($item, $xmlFile);
			
			$zip->addFile($xmlFile->getFilename(), $item->getId() . '.xml');
			foreach($item->getArticle()->getFiles($this->_entityManager) as $file)
				$zip->addFile($filePath . $file->getPath(), $file->getName());
			
			$zip->close();
		}
	}
	
    private function _generateXml(OrderItem $item, TmpFile $tmpFile)
    {
    	$configs = $this->_entityManager
    		->getRepository('CommonBundle\Entity\General\Config');
        
        $xml = new Generator($tmpFile);
		
		$attachments = array();
		$num = 1;
		foreach($item->getArticle()->getFiles($this->_entityManager) as $file) {
			$attachments[] = new Object(
				'Attachment',
				array(
					'AttachmentKey' => 'File' . $num++,
					'FileName' => $file->getName()
				),
				null
			);
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
						$item->getArticle()->getTitle()
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
			// TODO: generate text
			new Object(
				'ItemValue',
				array(
					'ItemKey' => 'afwerking'
				),
				array(
					new Object(
						'LastUsedValue',
						null,
						(string) ''
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
						$item->getArticle()->getNbColored() > 0 ? 'kleur' : 'zwart/wit'
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
						(string) $item->getArticle()->isRectoVerso() ? 'Recto-Verso' : 'Recto'
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
			// TODO: generate text
			new Object(
				'ItemValue',
				array(
					'ItemKey' => 'DatumOpdrachtKlaar'
				),
				array(
					new Object(
						'LastUsedValue',
						null,
						(string) ''
					)
				)
			),
			// TODO: generate text
			new Object(
				'ItemValue',
				array(
					'ItemKey' => 'Referentie'
				),
				array(
					new Object(
						'LastUsedValue',
						null,
						(string) ''
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

        $xml->append(
        	new Object(
        		'Document',
        		null,
        		array(
        			new Object(
        				'Job',
        				array(
        					'JobID' => 'vtk-' . $this->_order->getDate()->format('YmdHi') . '-'
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
