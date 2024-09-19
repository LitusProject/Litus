<?php

namespace BrBundle\Hydrator;

use BrBundle\Entity\Company as CompanyEntity;
use BrBundle\Entity\Company\Page as PageEntity;

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
    private static $stdKeys = array('name', 'vat_number', 'phone_number', 'website', 'large');

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new CompanyEntity();
        }

        $hydrator = $this->getHydrator('CommonBundle\Hydrator\General\Address');

        $object->setAddress(
            $hydrator->hydrate($data['address'], $object->getAddress())
        );

        if (isset($data['invoice']['invoice_name'])) {
            $object->setInvoiceName($data['invoice']['invoice_name']);
        }

        if (isset($data['invoice']['invoice_vat_number'])) {
            $object->setInvoiceVatNumber($data['invoice']['invoice_vat_number']);
        }

        if (isset($data['invoice']['invoice_address'])) {
            $object->setInvoiceAddress(
                $hydrator->hydrate($data['invoice']['invoice_address'], $object->getRawInvoiceAddress())
            );
        }
        
        $object->setAttendsJobfair($data['attends_jobfair']);

        $object->setSector($data['sector']);

        if (isset($data['cvbook'])) {
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
        }

        $years = array();
        if (isset($data['page']['years']) && count($data['page']['years']) > 0) {
            $yearIds = $data['page']['years'];
            $repository = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\AcademicYear');
            foreach ($yearIds as $yearId) {
                $years[] = $repository->findOneById($yearId);
            }
        }

        if ($object->getPage() === null) {
            $object->setPage(
                new PageEntity(
                    $object
                )
            );
            $this->getEntityManager()->persist($object->getPage());
        }

        $object->getPage()->setYears($years)
            ->setDescription($data['page']['description'])
            ->setShortDescription($data['page']['short_description'])
            ->setYoutubeURL($data['page']['youtube_url'])
            ->setAtEvent($data['page']['atEvent']);
        if (isset($data['page']['atEvent'])) {
            $object->getPage()->setAtEvent($data['page']['atEvent']);
        }


        return $this->stdHydrate($data, $object, self::$stdKeys);
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = $this->stdExtract($object, self::$stdKeys);

        $data['sector'] = $object->getSectorCode();
        
        $data['attends_jobfair'] = $object->attendsJobfair();

        $data['cvbook'] = array();
        foreach ($object->getCvBookYears() as $year) {
            $data['cvbook'][] = 'year-' . $year->getId();
        }

        foreach ($object->getCvBookArchiveYears() as $year) {
            $data['cvbook'][] = 'archive-' . $year;
        }

        $data['invoice']['invoice_name'] = $object->getRawInvoiceName();
        $data['invoice']['invoice_vat_number'] = $object->getRawInvoiceVatNumber();

        $hydrator = $this->getHydrator('CommonBundle\Hydrator\General\Address');

        $data['address'] = $hydrator->extract($object->getAddress());

        $invoiceAddress = $object->getRawInvoiceAddress();
        if ($invoiceAddress !== null) {
            $data['invoice']['invoice_address'] = $hydrator->extract($invoiceAddress);
        }

        $page = $object->getPage();
        if ($page !== null) {
            $data['page']['years'] = array();
            foreach ($page->getYears() as $year) {
                $data['page']['years'][] = $year->getId();
            }
            $data['page']['description'] = $page->getDescription();
            $data['page']['short_description'] = $page->getShortDescription();
            $data['page']['youtube_url'] = $page->getYoutubeURL();
            $data['page']['atEvent'] = $page->isAtEvent();
        }

        return $data;
    }
}
