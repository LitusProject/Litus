<?php

namespace Litus\Br;

use \Zend\Registry;

use \Litus\Entity\Br\Contracts\Contract;

use \Litus\Util\File as FileUtil;
use \Litus\Util\TmpFile;
use \Litus\Util\Xml\XmlGenerator;
use \Litus\Util\Xml\XmlObject;

use \Litus\Repository\Config\Config as ConfigRepository;
use \Litus\Entity\Config\Config as ConfigEntry;
 
class ContractGenerator extends DocumentGenerator {

    /**
     * @var \Litus\Entity\Br\Contracts\Contract
     */
    private $_contract;

    public function __construct(Contract $contract)
    {
        parent::__construct(Registry::get('litus.resourceDirectory') . '/pdf_generators/contract.xsl',
                            Registry::get('litus.resourceDirectory') . '/pdf/br/' . $contract->getId() . '/contract.pdf');
        $this->_contract = $contract;
    }

    protected function _generateXml(TmpFile $tmpFile)
    {
        /** @var \Litus\Util\Xml\XmlGenerator $xml */
        $xml = new XmlGenerator($tmpFile);

        // get the content

        /** @var \Litus\Repository\Config\Config $configs  */
        $configs = self::_getConfigRepository();

        $title = $this->_contract->getTitle();
        /** @var \Litus\Entity\Users\People\Company $company  */
        $company = $this->_contract->getCompany();
        $date = $this->_contract->getDate()->format('j F Y');
        $ourContactPerson = $this->_contract->getAuthor();
        $ourContactPerson = $ourContactPerson->getFirstName() . ' ' . $ourContactPerson->getLastName();
        $entries = $this->_contract->getParts();

        $unionName = $configs->getConfigValue('br.contract.union_name');
        $unionNameShort = $configs->getConfigValue('br.contract.union_name_short');
        $unionAddress = $configs->getConfigValue('br.contract.union_address');

        $location = $configs->getConfigValue('br.contract.location');

        $brName = $configs->getConfigValue('br.contract.br_name');
        $logo = $configs->getConfigValue('br.contract.logo');

        $sub_entries = $configs->getConfigValue('br.contract.sub_entries');
        $footer = $configs->getConfigValue('br.contract.footer');

        // generate the xml

        // <entry>s
        $entry_s = array();
        foreach($entries as $entry) {
            $entry_s[] = new XmlObject('entry', null, $entry->getSection()->getContent());
        }

        $xml->append(new XmlObject('contract',
            // params of contract
            array('location' => $location,
                'date' => $date),
            // children of contract
            array(
                // contract title
                new XmlObject('title',null,$title),

                // our_union
                new XmlObject('our_union',
                    // our_union parameters
                    array(
                         'short_name' => $unionNameShort,
                         'contact_person' => $ourContactPerson
                    ),

                    // our_union children
                    array(
                        new XmlObject('name', null, $brName),
                        new XmlObject('logo', null, $logo)
                    )
                ),

                // company
                new XmlObject('company',
                    // company parameters
                    array('contact_person' => $company->getFirstName() . ' ' . $company->getLastName()),

                    // company children
                    array(
                        new XmlObject('name', null, $company->getName()),
                        new XmlObject('address', null, self::_formatAddress($company->getAddress()))
                    )
                ),

                // union_address
                new XmlObject('union_address', null, array(
                        // union_address children
                        new XmlObject('name', null, $unionName),
                        new XmlObject('address', null, self::_formatAddress($unionAddress))
                )),

                // entries
                new XmlObject('entries', null, $entry_s),

                // sub_entries
                new XmlObject('sub_entries', null, $sub_entries),

                // footer
                new XmlObject('footer', null, $footer)
            )
        ));
    }
    
}
