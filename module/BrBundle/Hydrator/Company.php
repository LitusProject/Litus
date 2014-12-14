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

namespace BrBundle\Hydrator;

use BrBundle\Entity\Company as CompanyEntity,
    BrBundle\Entity\Company\Page as PageEntity;

/**
 * This hydrator hydrates/extracts Company data.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Company extends \CommonBundle\Component\Hydrator\Hydrator
{
    /**
     * @static @var string[] Key attributes to hydrate using the standard method.
     */
    private static $std_keys = array('name', 'vat_number', 'phone_number', 'website', 'sector');

    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            $object = new CompanyEntity();
        }

        $object->setAddress(
            $this->getHydrator('CommonBundle\Hydrator\General\Address')
                ->hydrate($data['address'], $object->getAddress())
        );

        $years = array();
        $archiveYears = array();
        if (count($data['cvbook']) > 0) {
            $repository = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\AcademicYear');
            foreach ($data['cvbook'] as $yearId) {
                if (strpos($yearId, 'archive-') === 0) {
                    $archiveYears[] = substr($yearId, strlen('archive-'));
                } else {
                    $years[] = $repository->findOneById(substr($yearId, strlen('year-')));
                }
            }
        }

        $object->setCvBookYears($years);
        $object->setCvBookArchiveYears($archiveYears);

        $years = array();
        if (count($data['page']['years']) > 0) {
            $yearIds = $data['page']['years'];
            $repository = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\AcademicYear');
            foreach ($yearIds as $yearId) {
                $years[] = $repository->findOneById($yearId);
            }
        }

        if (null === $object->getPage()) {
            $object->setPage(
                new PageEntity(
                    $object
                )
            );
            $this->getEntityManager()->persist($object->getPage());
        }

        $object->getPage()->setYears($years)
            ->setSummary($data['page']['summary'])
            ->setDescription($data['page']['description']);

        return $this->stdHydrate($data, $object, self::$std_keys);
    }

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array();
        }

        $data = $this->stdExtract($object, self::$std_keys);

        $data['cvbook'] = array();
        foreach ($object->getCvBookYears() as $year) {
            $data['cvbook'][] = 'year-' . $year->getId();
        }

        foreach ($object->getCvBookArchiveYears() as $year) {
            $data['cvbook'][] = 'archive-' . $year->getId();
        }

        $data['address'] = $this->getHydrator('CommonBundle\Hydrator\General\Address')
            ->extract($object->getAddress());

        $page = $object->getPage();
        if (null !== $page) {
            $data['page']['years'] = array();
            foreach ($page->getYears() as $year) {
                $data['page']['years'][] = $year->getId();
            }
            $data['page']['summary'] = $page->getSummary();
            $data['page']['description'] = $page->getDescription();
        }

        return $data;
    }
}
