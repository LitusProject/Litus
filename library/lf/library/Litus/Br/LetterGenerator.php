<?php

namespace Litus\Br;

use \Litus\Util\TmpFile;

use \Litus\Util\Xml\XmlObject;
use \Litus\Util\Xml\XmlGenerator;

use \Litus\Entity\Br\Contracts\Contract;

use \Zend\Registry;

class LetterGenerator extends DocumentGenerator {

    /**
     * @var \Litus\Entity\Br\Contracts\Contract
     */
    private $_contract;

    public function __construct(Contract $contract)
    {
        parent::__construct(
            Registry::get('litus.resourceDirectory') . '/pdf_generators/letter.xsl',
            Registry::get('litus.resourceDirectory') . '/pdf/br/' . $contract->getId() . '/letter.pdf'
        );
        $this->_contract = $contract;
    }

    protected function _generateXml(TmpFile $file)
    {
        /** @var $xml \Litus\Util\Xml\XmlGenerator */
        $xml = new XmlGenerator($file);

        /** @var $configs \Litus\Repository\Config\Config */
        $configs = self::_getConfigRepository();

        // Get the content
        $ourUnionName = $configs->getConfigValue('br.letter.union_name');
        $ourUnionAddress = self::_formatAddress($configs->getConfigValue('br.letter.union_address'));
        $ourUnionLogo = $configs->getConfigValue('br.letter.logo');
        $ourUnionVatNb = $configs->getConfigValue('br.letter.union_vat');

        $content = $configs->getConfigValue('br.letter.content');
        $footer = $configs->getConfigValue('br.letter.footer');

        /** @var $company \Litus\Entity\Users\People\Company */
        $company = $this->_contract->getCompany();
        $companyAddress = self::_formatAddress($company->getAddress());
        $companyName = $company->getName();

        $ourContactPerson = $this->_contract->getAuthor();
        $ourContactPerson = $ourContactPerson->getFirstName() . ' ' . $ourContactPerson->getLastName();

        $title = $configs->getConfigValue('br.letter.title.' . $company->getSex());

        // Generate the xml
        
        $xml->append(new XmlObject('letter', null,
                 array(
                     // children of <letter>
                     new XmlObject('our_union', null,
                        array(
                            // children of <our_union>
                            new XmlObject('name', null, $ourUnionName),
                            new XmlObject('contact_person', null, $ourContactPerson),
                            new XmlObject('address', null, $ourUnionAddress),
                            new XmlObject('logo', null, $ourUnionLogo),
                            new XmlObject('vat_number', null, $ourUnionVatNb)
                        )
                     ),

                     new XmlObject('company', null,
                         array(
                             // children of <company>
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
