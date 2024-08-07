<?php

namespace BrBundle\Component\Document\Generator\Pdf;

use BrBundle\Entity\Contract as ContractEntity;
use CommonBundle\Component\Util\File\TmpFile;
use CommonBundle\Component\Util\Xml\Generator as XmlGenerator;
use CommonBundle\Component\Util\Xml\Node as XmlNode;
use Doctrine\ORM\EntityManager;

class Letter extends \CommonBundle\Component\Document\Generator\Pdf
{
    /**
     * @var \BrBundle\Entity\Contract
     */
    private $contract;

    public function __construct(EntityManager $entityManager, ContractEntity $contract)
    {
        parent::__construct(
            $entityManager,
            $entityManager->getRepository('CommonBUndle\Entity\General\Config')
                ->getConfigValue('br.pdf_generator_path') . '/contract/letter.xsl',
            $entityManager->getRepository('CommonBUndle\Entity\General\Config')
                ->getConfigValue('br.file_path') . '/contracts/'
                . $contract->getId() . '/letter.pdf'
        );

        $this->contract = $contract;
    }

    protected function generateXml(TmpFile $file)
    {
        $xml = new XmlGenerator($file);

        $configs = $this->getConfigRepository();

        $ourUnionName = $configs->getConfigValue('br.letter.union_name');
        $ourUnionAddress = self::formatAddress($configs->getConfigValue('br.letter.union_address'));
        $ourUnionLogo = $configs->getConfigValue('br.letter.logo');
        $ourUnionVatNb = $configs->getConfigValue('br.letter.union_vat');

        $content = $configs->getConfigValue('br.letter.content');
        $footer = $configs->getConfigValue('br.letter.footer');

        $company = $this->contract->getCompany();
        $companyAddress = self::formatAddress($company->getAddress());
        $companyName = $company->getName();

        $ourContactPerson = $this->contract->getAuthor()->getPerson();

        $title = $configs->getConfigValue('br.letter.title.' . $ourContactPerson->getSex());

        $xml->append(
            new XmlNode(
                'letter',
                null,
                array(
                    new XmlNode(
                        'our_union',
                        null,
                        array(
                            new XmlNode('name', null, $ourUnionName),
                            new XmlNode('contact_person', null, $ourContactPerson->getFirstName() . ' ' . $ourContactPerson->getLastName()),
                            new XmlNode('address', null, $ourUnionAddress),
                            new XmlNode('logo', null, $ourUnionLogo),
                            new XmlNode('vat_number', null, $ourUnionVatNb),
                        )
                    ),

                    new XmlNode(
                        'company',
                        null,
                        array(
                            new XmlNode('name', null, $companyName),
                            new XmlNode(
                                'contact_person',
                                null,
                                array(
                                    new XmlNode('title', null, $title),
                                    new XmlNode('first_name', null, $ourContactPerson->getFirstName()),
                                    new XmlNode('last_name', null, $ourContactPerson->getLastName()),
                                )
                            ),
                            new XmlNode('address', null, $companyAddress),
                        )
                    ),

                    $content,

                    new XmlNode('footer', null, $footer),
                )
            )
        );
    }
}
