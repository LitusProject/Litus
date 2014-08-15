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

namespace BrBundle\Component\Document\Generator\Pdf;

use BrBundle\Entity\Contract as ContractEntity,
    CommonBundle\Component\Util\File\TmpFile,
    CommonBundle\Component\Util\Xml\Generator as XmlGenerator,
    CommonBundle\Component\Util\Xml\Object as XmlObject,
    Doctrine\ORM\EntityManager;

class Letter extends DocumentGenerator
{
    /**
     * @var \BrBundle\Entity\Contract
     */
    private $_contract;

    public function __construct(EntityManager $entityManager, ContractEntity $contract)
    {
        parent::__construct(
            $entityManager->getRepository('CommonBUndle\Entity\General\Config')
                ->getConfigValue('br.pdf_generator_path') . '/contract/letter.xsl',
            $entityManager->getRepository('CommonBUndle\Entity\General\Config')
                ->getConfigValue('br.file_path') . '/contracts/'
                . $contract->getId() . '/letter.pdf'
        );

        $this->_contract = $contract;
    }

    protected function _generateXml(TmpFile $file)
    {
        $xml = new XmlGenerator($file);

        $configs = $this->_getConfigRepository();

        $ourUnionName = $configs->getConfigValue('br.letter.union_name');
        $ourUnionAddress = self::_formatAddress($configs->getConfigValue('br.letter.union_address'));
        $ourUnionLogo = $configs->getConfigValue('br.letter.logo');
        $ourUnionVatNb = $configs->getConfigValue('br.letter.union_vat');

        $content = $configs->getConfigValue('br.letter.content');
        $footer = $configs->getConfigValue('br.letter.footer');

        $company = $this->_contract->getCompany();
        $companyAddress = self::_formatAddress($company->getAddress());
        $companyName = $company->getName();

        $ourContactPerson = $this->_contract->getAuthor();
        $ourContactPerson = $ourContactPerson->getFirstName() . ' ' . $ourContactPerson->getLastName();

        $title = $configs->getConfigValue('br.letter.title.' . $company->getSex());

        $xml->append(new XmlObject('letter', null,
                 array(
                     new XmlObject('our_union', null,
                        array(
                            new XmlObject('name', null, $ourUnionName),
                            new XmlObject('contact_person', null, $ourContactPerson),
                            new XmlObject('address', null, $ourUnionAddress),
                            new XmlObject('logo', null, $ourUnionLogo),
                            new XmlObject('vat_number', null, $ourUnionVatNb)
                        )
                     ),

                     new XmlObject('company', null,
                         array(
                             new XmlObject('name', null, $companyName),
                             new XmlObject('contact_person', null,
                                array(
                                    new XmlObject('title', null, $title),
                                    new XmlObject('first_name', null, $company->getFirstName()),
                                    new XmlObject('last_name', null, $company->getLastName())
                                )
                             ),
                             new XmlObject('address', null, $companyAddress)
                         )
                     ),

                     $content,

                     new XmlObject('footer', null, $footer)
                 )
             )
        );
    }
}
