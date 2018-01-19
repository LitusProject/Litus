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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Component\Document\Generator\Company;

use BrBundle\Entity\Company,
    CommonBundle\Component\Util\File\TmpFile,
    CommonBundle\Component\Util\Xml\Generator,
    CommonBundle\Component\Util\Xml\Node,
    Doctrine\ORM\EntityManager;

/**
 * CompanyPdf
 *
 * @author Floris Kint <floris.kint@litus.cc>
 */
class Pdf extends \CommonBundle\Component\Document\Generator\Pdf
{
    /**
     * @var array
     */
    private $companies;

    /**
     * Create a new Event PDF Generator.
     *
     * @param EntityManager $entityManager
     * @param TmpFile       $file          The file to write to
     */
    public function __construct(EntityManager $entityManager, TmpFile $file)
    {
        $filePath = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.pdf_generator_path');

        parent::__construct(
            $entityManager,
            $filePath . '/company/companies.xsl',
            $file->getFilename()
        );

        $this->companies = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findAll();
    }

    /**
     * Generate the XML for FOP.
     *
     * @param TmpFile $tmpFile The file to write to.
     */
    protected function generateXml(TmpFile $tmpFile)
    {
        $configs = $this->getConfigRepository();

        $organization_name = $configs->getConfigValue('organization_name');
        $organization_logo = $configs->getConfigValue('organization_logo');
        $all_companies = array();
        foreach ($this->companies as $company) {
            $all_users = array();
            $company_users = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\User\Person\Corporate')
                ->findBy(array(
                    'canLogin'  => 'true',
                    'company'   => $company->getId(),
                ));

            foreach ($company_users as $user) {
                $all_users[] = new Node(
                    'person',
                    array(),
                    array(
                        new Node(
                            'username',
                            array(),
                            $user->getUsername()
                        ),
                        new Node(
                            'name',
                            array(),
                            $user->getFullName()
                        ),
                        new Node(
                            'email',
                            array(),
                            $user->getEmail()
                        ),
                        new Node(
                            'userPhone',
                            array(),
                            $user->getPhoneNumber()
                        ),
                    )
                );
            }
            $all_companies[] = new Node(
                'company',
                array(),
                array(
                    new Node(
                        'name',
                        array(),
                        $company->getName()
                    ),
                    new Node(
                        'companyPhone',
                        array(),
                        $company->getPhoneNumber()
                    ),
                    new Node(
                        'users',
                        array(),
                        $all_users
                    ),
                )
            );
        }

        $xml = new Generator($tmpFile);

        $xml->append(
            new Node(
                'companieslist',
                array(
                    'name' => 'List of companies',
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
                        'companies',
                        array(),
                        $all_companies
                    ),
                )
            )
        );
    }
}
