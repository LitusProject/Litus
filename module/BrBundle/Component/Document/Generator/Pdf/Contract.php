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

namespace BrBundle\Component\Document\Generator\Pdf;

use BrBundle\Entity\Contract as ContractEntity,
    CommonBundle\Component\Util\File\TmpFile,
    CommonBundle\Component\Util\Xml\Generator as XmlGenerator,
    CommonBundle\Component\Util\Xml\Object as XmlObject,
    Doctrine\ORM\EntityManager;

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
     * @param \BrBundle\Entity\Contract $contract The contract for which we want to generate a PDF
     */
    public function __construct(EntityManager $entityManager, ContractEntity $contract)
    {

        parent::__construct(
            $entityManager,
            $entityManager->getRepository('CommonBUndle\Entity\General\Config')
                ->getConfigValue('br.pdf_generator_path') . '/contract/contract.xsl',
            $entityManager->getRepository('CommonBUndle\Entity\General\Config')
                ->getConfigValue('br.file_path') . '/contracts/'
                . $contract->getId() . '/contract.pdf'
        );
        $this->_contract = $contract;
    }

    protected function generateXml(TmpFile $xmlFile)
    {
        $xml = new XmlGenerator($xmlFile);

        $configs = $this->getConfigRepository();

        $title = $this->_contract->getTitle();
        /** @var \Litus\Entity\Users\People\Company $company  */
        $company = $this->_contract->getCompany();
        $date = $this->_contract->getDate()->format('j F Y');
        $ourContactPerson = $this->_contract->getAuthor();
        $ourContactPerson = $ourContactPerson->getFirstName() . ' ' . $ourContactPerson->getLastName();
        $entries = $this->_contract->getComposition();

        $unionNameShort = $configs->getConfigValue('union_short_name');
        $unionName = $configs->getConfigValue('union_name');
        $unionAddress = $configs->getConfigValue('union_address');

        $location = $configs->getConfigValue('union_city');

        $brName = $configs->getConfigValue('union_name'); // TODO: this was br_name
        $logo = $configs->getConfigValue('union_logo');

        // $sub_entries = $configs->getConfigValue('sub_entries'); // TODO
        $footer = $configs->getConfigValue('br.contract_footer');

        // Generate the xml

        $entry_s = array();
        foreach($entries as $entry) {
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
                            // 'contact_person' => $company->getFirstName() . ' ' . $company->getLastName()
                            'contract_person' => 'FirstName LastName' // TODO
                        ),

                        // children of <company>
                        array(
                            new XmlObject('name', null, $company->getName()),
                            // new XmlObject('address', null, self::_formatAddress($company->getAddress()))
                            new XmlObject('address', null, 'Company Address') // TODO
                        )
                    ),

                    new XmlObject(
                        'union_address',

                        // params of <union_address>
                        null,

                        // children of <union_address>
                        array(
                            new XmlObject('name', null, $unionName),
                            // new XmlObject('address', null, self::_formatAddress($unionAddress))
                            new XmlObject('address', null, 'Union Address') // TODO
                        )
                    ),

                    new XmlObject('entries', null, $entry_s),

                    // new XmlObject('sub_entries', null, $sub_entries), // TODO

                    new XmlObject('footer', null, $footer)
                )
            )
        );
    }
}
