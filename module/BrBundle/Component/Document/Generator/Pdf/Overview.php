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
 * @author Kristof MariÃ«n <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

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
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Component\Document\Generator\Pdf;

use CommonBundle\Component\Util\File\TmpFile,
    CommonBundle\Component\Util\Xml\Generator,
    CommonBundle\Component\Util\Xml\Object as Object,
    Doctrine\ORM\EntityManager;

/**
 * Generate an overview pdf.
 *
 * @author Floris Kint <floris.kint@litus.cc>
 */
class Overview extends \CommonBundle\Component\Document\Generator\Pdf
{
    public function __construct(EntityManager $entityManager, TmpFile $file)
    {
        $filePath = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.pdf_generator_path');

        parent::__construct(
            $entityManager,
            $filePath . '/overview/overview.xsl',
            $file->getFilename()
        );
    }

    protected function generateXml(TmpFile $tmpFile)
    {
        $configs = $this->getConfigRepository();

        $organization_name = $configs->getConfigValue('organization_name');
        $organization_logo = $configs->getConfigValue('organization_logo');

        $detailedOverview = $this->getDetailedCompanyOverview();

        $xml = new Generator($tmpFile);

        $xml->append(
            new Object(
                'companies_overview',
                array(
                    'name' => 'Overview of contracts per company',
                    'date' => (new \DateTime())->format('d F Y H:i'),
                ),
                array(
                    new Object(
                        'our_union',
                        array(),
                        array(
                            new Object(
                                'name',
                                array(),
                                $organization_name
                            ),
                            new Object(
                                'logo',
                                array(),
                                $organization_logo
                            ),
                        )
                    ),
                    new Object(
                        'summary',
                        array(),
                        $detailedOverview['totals']
                    ),
                    new Object(
                        'companies',
                        array(),
                        $detailedOverview['companies']
                    ),
                )
            )
        );
    }

    /**
     * @return array
     */
    private function getDetailedCompanyOverview()
    {
        $companyNmbr = 0;
        $totalContracted = 0;
        $totalSigned = 0;
        $totalPaid = 0;

        $ids = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Contract')
            ->findContractCompany();

        $collection = array();
        foreach ($ids as $id) {
            $company = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Company')
                ->findOneById($id);

            $contracts = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Contract')
                ->findAllNewOrSignedByCompany($company);

            ++$companyNmbr;

            $contracted = 0;
            $signed = 0;
            $paid = 0;

            $contract_details = array();

            foreach ($contracts as $contract) {
                $contract->getOrder()->setEntityManager($this->getEntityManager());
                $value = $contract->getOrder()->getTotalCostExclusive();
                $contracted = $contracted + $value;
                $totalContracted = $totalContracted + $value;

                $isPaid = false;
                if ($contract->isSigned()) {
                    $signed = $signed + $value;
                    $totalSigned = $totalSigned + $value;

                    if ($contract->getOrder()->getInvoice()->isPaid()) {
                        $isPaid = true;
                        $paid = $paid + $value;
                        $totalPaid = $totalPaid + $value;
                    }
                }

                $products = array();
                $orderEntries = $contract->getOrder()->getEntries();

                foreach ($orderEntries as $entry) {
                    $products[] = new Object(
                        'product',
                        array(),
                        array(
                            new Object(
                                'text',
                                array(),
                                $entry->getProduct()->getName()
                            ),
                        )
                    );
                }

                $contract_details[] = new Object(
                    'contract',
                    array(),
                    array(
                        new Object(
                            'title',
                            array(),
                            $contract->getTitle()
                        ),
                        new Object(
                            'date',
                            array(),
                            $contract->getDate()->format('Y-m-d')
                        ),
                        new Object(
                            'contract_nb',
                            array(),
                            $contract->getContractNb($this->getEntityManager())
                        ),
                        new Object(
                            'signed',
                            array(),
                            $contract->isSigned() ? '1' : '0'
                        ),
                        new Object(
                            'paid',
                            array(),
                            $isPaid ? '1' : '0'
                        ),
                        new Object(
                            'author',
                            array(),
                            $contract->getAuthor()->getPerson()->getFullName()
                        ),
                        new Object(
                            'value',
                            array(),
                            $value . ''
                        ),
                        new Object(
                            'products',
                            array(),
                            $products
                        ),
                    )
                );
            }

            $collection[] = new Object(
                'company',
                array(),
                array(
                    new Object(
                        'name',
                        array(),
                        $company->getName()
                    ),
                    new Object(
                        'amount',
                        array(),
                        sizeof($contracts) . ''
                    ),
                    new Object(
                        'contracted',
                        array(),
                        $contracted . ''
                    ),
                    new Object(
                        'signed',
                        array(),
                        $signed . ''
                    ),
                    new Object(
                        'paid',
                        array(),
                        $paid ? '1' : '0'
                    ),
                    new Object(
                        'contracts',
                        array(),
                        $contract_details
                    ),
                )
            );
        }
        $totals = array(
            new Object(
                'amount',
                array(),
                $companyNmbr . ''
            ),
            new Object(
                'contract',
                array(),
                $totalContracted . ''
            ),
            new Object(
                'paid',
                array(),
                $totalPaid . ''
            ),
            new Object(
                'signed',
                array(),
                $totalSigned . ''
            ),
        );

        return array(
            'totals' => $totals,
            'companies' => $collection,
        );
    }
}
