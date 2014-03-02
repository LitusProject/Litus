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

use BrBundle\Entity\Contract,
    CommonBundle\Component\Util\TmpFile,
    CommonBundle\Component\Util\Xml\Generator as XmlGenerator,
    CommonBundle\Component\Util\Xml\Object as XmlObject;

/**
 * Generate a PDF for a contract.
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Contract extends \CommonBundle\Component\Document\Generator\Pdf
{
    /**
     * @var \Litus\Entity\Br\Contract
     */
    private $_contract;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \BrBundle\Entity\Contract   $contract      The contract for which we want to generate a PDF
     */
    public function __construct(EntityManager $entityManager, Contract $contract)
    {
        parent::__construct(
            Registry::get('litus.resourceDirectory') . '/pdf_generators/contract.xsl',
            Registry::get('litus.resourceDirectory') . '/pdf/br/' . $contract->getId() . '/contract.pdf'
        );
        $this->_contract = $contract;
    }

    protected function _generateXml(TmpFile $tmpFile)
    {
        $xml = new XmlGenerator($tmpFile);

        $configs = $this->_getConfigRepository();

        $title = $this->_contract->getTitle();
        /** @var \Litus\Entity\Users\People\Company $company  */
        $company = $this->_contract->getCompany();
        $date = $this->_contract->getDate()->format('j F Y');
        $ourContactPerson = $this->_contract->getAuthor();
        $ourContactPerson = $ourContactPerson->getFirstName() . ' ' . $ourContactPerson->getLastName();
        $entries = $this->_contract->getComposition();

        $unionName = $configs->getConfigValue('br.contract.union_name');
        $unionNameShort = $configs->getConfigValue('br.contract.union_name_short');
        $unionAddress = $configs->getConfigValue('br.contract.union_address');

        $location = $configs->getConfigValue('br.contract.location');

        $brName = $configs->getConfigValue('br.contract.br_name');
        $logo = $configs->getConfigValue('br.contract.logo');

        $sub_entries = $configs->getConfigValue('br.contract.sub_entries');
        $footer = $configs->getConfigValue('br.contract.footer');

        // Generate the xml

        $entry_s = array();
        foreach ($entries as $entry) {
            $entry_s[] = $entry->getSection()->getContent();
        }

        $xml->append(
            new XmlObject(
                'contract',

                // params of <contract>
                array(
                    'location' => $location,
                    'date' => $date
                ),

                // children of <contract>
                array(
                    new XmlObject('title', null, $title),

                    new XmlObject(
                        'our_union',

                        // params of <our_union>
                        array(
                             'short_name' => $unionNameShort,
                             'contact_person' => $ourContactPerson
                        ),

                        // children of <our_union>
                        array(
                            new XmlObject('name', null, $brName),
                            new XmlObject('logo', null, $logo)
                        )
                    ),

                    new XmlObject(
                        'company',

                        // params of <company>
                        array(
                            'contact_person' => $company->getFirstName() . ' ' . $company->getLastName()
                        ),

                        // children of <company>
                        array(
                            new XmlObject('name', null, $company->getName()),
                            new XmlObject('address', null, self::_formatAddress($company->getAddress()))
                        )
                    ),

                    new XmlObject(
                        'union_address',

                        // params of <union_address>
                        null,

                        // children of <union_address>
                        array(
                            new XmlObject('name', null, $unionName),
                            new XmlObject('address', null, self::_formatAddress($unionAddress))
                        )
                    ),

                    new XmlObject('entries', null, $entry_s),

                    new XmlObject('sub_entries', null, $sub_entries),

                    new XmlObject('footer', null, $footer)
                )
            )
        );
    }
}
