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

namespace BrBundle\Component\Document\Generator\Company;

use BrBundle\Entity\Company,
    CommonBundle\Component\Util\File\TmpFile,
    CommonBundle\Component\Util\Xml\Generator,
    CommonBundle\Component\Util\Xml\Object,
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
                $all_users[] = new Object(
                    'person',
                    array(),
                    array(
                        new Object(
                            'username',
                            array(),
                            $user->getUsername()
                        ),
                        new Object(
                            'name',
                            array(),
                            $user->getFullName()
                        ),
                        new Object(
                            'email',
                            array(),
                            $user->getEmail()
                        ),
                        new Object(
                            'userPhone',
                            array(),
                            $user->getPhoneNumber()
                        ),
                    )
                );
            }
            $all_companies[] = new Object(
                'company',
                array(),
                array(
                    new Object(
                        'name',
                        array(),
                        $company->getName()
                    ),
                    new Object(
                        'companyPhone',
                        array(),
                        $company->getPhoneNumber()
                    ),
                    new Object(
                        'users',
                        array(),
                        $all_users
                    ),
                )
            );
        }

        $xml = new Generator($tmpFile);

        $xml->append(
            new Object(
                'companieslist',
                array(
                    'name' => 'List of companies',
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
                        'companies',
                        array(),
                        $all_companies
                    ),
                )
            )
        );
    }
}
