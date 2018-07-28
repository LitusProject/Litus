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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
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
    CommonBundle\Component\Util\Xml\Node as Node,
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
            new Node(
                'companies_overview',
                array(
                    'name' => 'Overview of contracts per company',
                    'date' => (new \DateTime())->format('d F Y H:i'),
                ),
                array(
                    new Node(
                        'our_union',
                        array(),
                        array(
                            new Node(
                                'name',
                                array(),
                                $organization_name
                            ),
                            new Node(
                                'logo',
                                array(),
                                $organization_logo
                            ),
                        )
                    ),
                    new Node(
                        'summary',
                        array(),
                        $detailedOverview['totals']
                    ),
                    new Node(
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
                    $products[] = new Node(
                        'product',
                        array(),
                        array(
                            new Node(
                                'text',
                                array(),
                                $entry->getProduct()->getName()
                            ),
                        )
                    );
                }

                $contract_details[] = new Node(
                    'contract',
                    array(),
                    array(
                        new Node(
                            'title',
                            array(),
                            $contract->getTitle()
                        ),
                        new Node(
                            'date',
                            array(),
                            $contract->getDate()->format('Y-m-d')
                        ),
                        new Node(
                            'contract_nb',
                            array(),
                            $contract->getFullContractNumber($this->getEntityManager())
                        ),
                        new Node(
                            'signed',
                            array(),
                            $contract->isSigned() ? '1' : '0'
                        ),
                        new Node(
                            'paid',
                            array(),
                            $isPaid ? '1' : '0'
                        ),
                        new Node(
                            'author',
                            array(),
                            $contract->getAuthor()->getPerson()->getFullName()
                        ),
                        new Node(
                            'value',
                            array(),
                            $value . ''
                        ),
                        new Node(
                            'products',
                            array(),
                            $products
                        ),
                    )
                );
            }

            $collection[] = new Node(
                'company',
                array(),
                array(
                    new Node(
                        'name',
                        array(),
                        $company->getName()
                    ),
                    new Node(
                        'amount',
                        array(),
                        sizeof($contracts) . ''
                    ),
                    new Node(
                        'contracted',
                        array(),
                        $contracted . ''
                    ),
                    new Node(
                        'signed',
                        array(),
                        $signed . ''
                    ),
                    new Node(
                        'paid',
                        array(),
                        $paid ? '1' : '0'
                    ),
                    new Node(
                        'contracts',
                        array(),
                        $contract_details
                    ),
                )
            );
        }
        $totals = array(
            new Node(
                'amount',
                array(),
                $companyNmbr . ''
            ),
            new Node(
                'contract',
                array(),
                $totalContracted . ''
            ),
            new Node(
                'paid',
                array(),
                $totalPaid . ''
            ),
            new Node(
                'signed',
                array(),
                $totalSigned . ''
            ),
        );

        return array(
            'totals'    => $totals,
            'companies' => $collection,
        );
    }
}
