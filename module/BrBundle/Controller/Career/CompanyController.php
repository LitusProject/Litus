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

namespace BrBundle\Controller\Career;

use BrBundle\Entity\Company;
use BrBundle\Entity\Company\Page;
use DateTime;
use Laminas\Http\Headers;
use Laminas\View\Model\ViewModel;

/**
 * CompanyController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class CompanyController extends \BrBundle\Component\Controller\CareerController
{
    public function overviewAction()
    {
        $logoPath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.public_logo_path');

        return new ViewModel(
            array(
                'logoPath'         => $logoPath,
                'possible_sectors' => array('all' => 'All') + Company::POSSIBLE_SECTORS,
            )
        );
    }

    public function viewAction()
    {
        $page = $this->getPageEntity();
        if ($page === null) {
            return new ViewModel();
        }

        $language = $this->getLanguage();

        $logoPath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.public_logo_path');

        $events = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Event')
            ->findAllFutureByCompany(new DateTime(), $page->getCompany());

        $internships = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Job')
            ->findAllActiveByCompanyAndType($page->getCompany(), 'internship');

        $vacancies = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Job')
            ->findAllActiveByCompanyAndType($page->getCompany(), 'vacancy');

        return new ViewModel(
            array(
                'logoPath'    => $logoPath,
                'page'        => $page,
                'events'      => $events,
                'internships' => $internships,
                'vacancies'   => $vacancies,
                'language'    => $language,
            )
        );
    }

    public function fileAction()
    {
        $filePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.file_path') . '/' . $this->getParam('name');

        if ($this->getParam('name') == '' || !file_exists($filePath)) {
            return $this->notFoundAction();
        }

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'inline; filename="' . $this->getParam('name') . '"',
                'Content-Type'        => mime_content_type($filePath),
                'Content-Length'      => filesize($filePath),
            )
        );
        $this->getResponse()->setHeaders($headers);

        $handle = fopen($filePath, 'r');
        $data = fread($handle, filesize($filePath));
        fclose($handle);

        return new ViewModel(
            array(
                'data' => $data,
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        $result = array();

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();

            $pages = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Company\Page')
                ->findAllActiveBySearch($this->getCurrentAcademicYear(), $data['query'], $data['sector']);

            foreach ($pages as $page) {
                $item = (object) array();
                $item->name = $page->getCompany()->getName();
                $item->logo = $page->getCompany()->getLogo();
                $item->slug = $page->getCompany()->getSlug();
                $result[] = $item;
            }
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    /**
     * @return Page|null
     */
    private function getPageEntity()
    {
        $page = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Page')
            ->findOneActiveBySlug($this->getParam('company', 0), $this->getCurrentAcademicYear());

        if (!($page instanceof Page)) {
            $this->flashMessenger()->error(
                'Error',
                'No page was found!'
            );

            $this->redirect()->toRoute(
                'br_career_company',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $page;
    }
}
