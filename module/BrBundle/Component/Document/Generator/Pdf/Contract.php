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
    BrBundle\Component\ContractParser\Parser as BulletParser,
    CommonBundle\Component\Util\File\TmpFile,
    CommonBundle\Component\Util\Xml\Generator as XmlGenerator,
    CommonBundle\Component\Util\Xml\Object as XmlObject,
    Doctrine\ORM\EntityManager,
    IntlDateFormatter,
    Zend\I18n\Translator\Translator;

/**
 * Generate a PDF for a contract.
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 */
class Contract extends \CommonBundle\Component\Document\Generator\Pdf
{
    /**
     * @var \BrBundle\Entity\Contract
     */
    private $_contract;

    /**
     * @var \Zend\I18n\Translator\Translator
     */
    private $_translator;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \BrBundle\Entity\Contract   $contract      The contract for which we want to generate a PDF
     */
    public function __construct(EntityManager $entityManager, ContractEntity $contract, Translator $translator)
    {
        parent::__construct(
            $entityManager,
            $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.pdf_generator_path') . '/contract/contract.xsl',
            $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.file_path') . '/contracts/'
                . $contract->getId() . '/contract.pdf'
        );
        $this->_translator = $translator;
        $this->_contract = $contract;
    }

    protected function generateXml(TmpFile $tmpFile)
    {
        $xml = new XmlGenerator($tmpFile);

        $configs = $this->getEntityManager()->getRepository('CommonBundle\Entity\General\Config');

        $title = $this->_contract->getTitle();
        $company = $this->_contract->getOrder()->getCompany();

        $locale = $configs->getConfigValue('br.contract_language');
        $this->_translator->setLocale($locale);

        $formatter = new IntlDateFormatter($locale, IntlDateFormatter::FULL, IntlDateFormatter::NONE);
        $date = $formatter->format($this->_contract->getOrder()->getCreationTime());

        $ourContactPerson = $this->_contract->getOrder()->getCreationPerson()->getPerson()->getFullName();
        $entries = $this->_contract->getEntries();

        $unionName = $configs->getConfigValue('organization_name');
        $unionNameShort = $configs->getConfigValue('organization_short_name');
        $unionAddressArray = unserialize($configs->getConfigValue('organization_address_array'));

        $location = $unionAddressArray['city'];

        $brName = $configs->getConfigValue('br.contract_name');
        $logo = $configs->getConfigValue('organization_logo');

        $finalEntry = $configs->getConfigValue('br.contract_final_entry');

        $sub_entries = unserialize($configs->getConfigValue('br.contract_below_entries'))['nl']; //TODO make this possible in both english and dutch.

        /*$entry_s = array();
        foreach ($entries as $entry)
            $entry_s[] = new XmlObject('entry', null, $entry->getContractText());*/
        
        $p = new BulletParser();
        $p->parse($entry->getContractText());
        $entry_s = XmlObject::fromString($p->getXml());

        $xml->append(
            new XmlObject(
                'contract',
                array(
                    'location' => $location,
                    'date' => $date
                ),
                array(
                    new XmlObject('title', null, $title),
                    new XmlObject(
                        'our_union',
                        array(
                             'short_name' => $unionNameShort,
                             'contact_person' => $ourContactPerson
                        ),
                        array(
                            new XmlObject('name', null, $brName),
                            new XmlObject('logo', null, $logo)
                        )
                    ),
                    new XmlObject(
                        'company',
                        array(
                            'contact_person' => $this->_contract->getOrder()->getContact()->getFullName(),
                        ),
                        array(
                            new XmlObject('name', null, $company->getName()),
                            new XmlObject(
                                'address',
                                null,
                                array(
                                    new XmlObject(
                                        'street',
                                        null,
                                        $company->getAddress()->getStreet()
                                    ),
                                    new XmlObject(
                                        'number',
                                        null,
                                        $company->getAddress()->getNumber()
                                    ),
                                    new XmlObject(
                                        'mailbox',
                                        null,
                                        $company->getAddress()->getMailbox()
                                    ),
                                    new XmlObject(
                                        'postal',
                                        null,
                                        $company->getAddress()->getPostal()
                                    ),
                                    new XmlObject(
                                        'city',
                                        null,
                                        $company->getAddress()->getCity()
                                    ),
                                    new XmlObject(
                                        'country',
                                        null,
                                        $this->_translator->translate($company->getAddress()->getCountry())
                                    )
                                )
                            )
                        )
                    ),
                    new XmlObject(
                        'union_address',
                        null,
                        array(
                            new XmlObject('name', null, $unionName),
                            new XmlObject(
                                'address',
                                null,
                                array(
                                    new XmlObject(
                                        'street',
                                        null,
                                        $unionAddressArray['street']
                                    ),
                                    new XmlObject(
                                        'number',
                                        null,
                                        $unionAddressArray['number']
                                    ),
                                    new XmlObject(
                                        'mailbox',
                                        null,
                                        $unionAddressArray['mailbox']
                                    ),
                                    new XmlObject(
                                        'postal',
                                        null,
                                        $unionAddressArray['postal']
                                    ),
                                    new XmlObject(
                                        'city',
                                        null,
                                        $unionAddressArray['city']
                                    ),
                                    new XmlObject(
                                        'country',
                                        null,
                                        $unionAddressArray['country']
                                    )
                                )
                            )
                        )
                    ),
                    $entry_s,
                    new XmlObject('sub_entries', null, $sub_entries),
                    new XmlObject('footer')
                )
            )
        );
    }
}
