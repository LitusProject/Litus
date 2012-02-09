<?php

namespace Litus\Br;

use \Litus\Entity\Br\Contract;

use \Litus\Util\TmpFile;
use \Litus\Util\Xml\XmlGenerator;
use \Litus\Util\Xml\XmlObject;

use \Zend\Registry;

class ContractGenerator extends DocumentGenerator {

    /**
     * @var \Litus\Entity\Br\Contractt
     */
    private $_contract;

    public function __construct(Contract $contract)
    {
        parent::__construct(
            Registry::get('litus.resourceDirectory') . '/pdf_generators/contract.xsl',
            Registry::get('litus.resourceDirectory') . '/pdf/br/' . $contract->getId() . '/contract.pdf'
        );
        $this->_contract = $contract;
    }

    protected function _generateXml(TmpFile $tmpFile)
    {
        /** @var \Litus\Util\Xml\XmlGenerator $xml */
        $xml = new XmlGenerator($tmpFile);

        // Get the content

        /** @var \Litus\Repository\General\Config $configs  */
        $configs = self::_getConfigRepository();

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
