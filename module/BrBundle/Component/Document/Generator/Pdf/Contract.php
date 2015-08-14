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

use BrBundle\Component\ContractParser\Parser as BulletParser,
    BrBundle\Entity\Contract as ContractEntity,
    CommonBundle\Component\Util\File\TmpFile,
    CommonBundle\Component\Util\Xml\Generator as XmlGenerator,
    CommonBundle\Component\Util\Xml\Object as XmlObject,
    Doctrine\ORM\EntityManager,
    IntlDateFormatter,
    Zend\I18n\Translator\TranslatorInterface;

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
     * @var ContractEntity
     */
    private $contract;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param EntityManager       $entityManager The EntityManager instance
     * @param ContractEntity      $contract      The contract for which we want to generate a PDF
     * @param TranslatorInterface $translator
     */
    public function __construct(EntityManager $entityManager, ContractEntity $contract, TranslatorInterface $translator)
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
        $this->translator = $translator;
        $this->contract = $contract;
    }

    /**
     * @param TmpFile $tmpFile
     */
    protected function generateXml(TmpFile $tmpFile)
    {
        $xml = new XmlGenerator($tmpFile);

        $configs = $this->getEntityManager()->getRepository('CommonBundle\Entity\General\Config');

        $title = $this->contract->getTitle();
        $company = $this->contract->getOrder()->getCompany();

        $locale = $configs->getConfigValue('br.contract_language');
        $this->translator->setLocale($locale);

        $formatter = new IntlDateFormatter($locale, IntlDateFormatter::FULL, IntlDateFormatter::NONE);
        $date = $formatter->format($this->contract->getOrder()->getCreationTime());

        $ourContactPerson = $this->contract->getOrder()->getCreationPerson()->getPerson()->getFullName();
        $entries = $this->contract->getEntries();

        $unionName = $configs->getConfigValue('organization_name');
        $unionNameShort = $configs->getConfigValue('organization_short_name');
        $unionAddressArray = unserialize($configs->getConfigValue('organization_address_array'));

        $location = $unionAddressArray['city'];

        $brName = $configs->getConfigValue('br.contract_name');
        $logo = $configs->getConfigValue('organization_logo');

        $vatTypes = unserialize($configs->getConfigValue('br.vat_types'));
        $vatTotals = '';
        $this->contract->getOrder()->setEntityManager($this->getEntityManager());
        foreach ($vatTypes as $type) {
            if ($this->contract->getOrder()->getCostVatTypeExclusive($type) > 0) {
                $price = $this->contract->getOrder()->getCostVatTypeExclusive($type)/100;
                $vatTotals = $vatTotals . '<vat_total><vat>' . $type . '</vat><total>' . $price . '</total></vat_total>';
            }
        }

        $paymentDetailsText = str_replace(
            "<total_price/>",
            "<total_price>" . $vatTotals . "</total_price>",
            $this->contract->getPaymentDetails()
        );

        $paymentDetails = array();
        if ($paymentDetailsText != '') {
            $p = new BulletParser();
            $p->parse($paymentDetailsText);
            $paymentDetails[] = XmlObject::fromString($p->getXml());
        }

        $sub_entries = unserialize($configs->getConfigValue('br.contract_below_entries'))['nl']; //TODO make this possible in both english and dutch.

        $contractText = '';
        foreach ($entries as $entry) {
            $contractText = $contractText . "\n" . $entry->getContractText();
        }
        if ($this->contract->getAutoDiscountText() != '') {
            $contractText = $contractText . "\n" . $this->contract->getAutoDiscountText();
        }
        if ($this->contract->getDiscountText() != '') {
            $contractText = $contractText . "\n" . $this->contract->getDiscountText();
        }

        if ($contractText != '') {
            $p = new BulletParser();
            $p->parse($contractText);
            $entry_s = XmlObject::fromString($p->getXml());
        }

        $xml->append(
            new XmlObject(
                'contract',
                array(
                    'location' => $location,
                    'date' => $date,
                ),
                array(
                    new XmlObject('title', null, $title),
                    new XmlObject(
                        'our_union',
                        array(
                             'short_name' => $unionNameShort,
                             'contact_person' => $ourContactPerson,
                        ),
                        array(
                            new XmlObject('name', null, $brName),
                            new XmlObject('logo', null, $logo),
                        )
                    ),
                    new XmlObject(
                        'company',
                        array(
                            'contact_person' => $this->contract->getOrder()->getContact()->getFullName(),
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
                                        $this->translator->translate($company->getAddress()->getCountry())
                                    ),
                                )
                            ),
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
                                    ),
                                )
                            ),
                        )
                    ),
                    $entry_s,
                    new XmlObject(
                        'payment_details',
                        array('payment_days' => (String) $this->contract->getPaymentDays()),
                        $paymentDetails
                    ),
                    new XmlObject('sub_entries', null, $sub_entries),
                    new XmlObject('footer'),
                    new XmlObject('sale_conditions_nl'),
                )
            )
        );
    }
}
