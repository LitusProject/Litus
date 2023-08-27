<?php

namespace CudiBundle\Controller;

use CommonBundle\Component\Controller\Exception\HasNoAccessException;
use CommonBundle\Entity\User\Person\Academic;
use CudiBundle\Entity\Deal;
use CudiBundle\Entity\Retail;
use Laminas\Mail\Message;
use Laminas\View\Model\ViewModel;

/**
 * RetailController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class RetailController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function overviewAction()
    {
        $enableRetail = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.retail_enabled');

        if ($enableRetail == 0) {
            throw new HasNoAccessException(
                'The retail section is closed'
            );
        }

        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            $this->redirect()->toRoute('common_auth',
                array(
                    'redirect' => urlencode($this->getRequest()->getRequestUri()),
                ));
        }
        $bookSearchForm = $this->getForm('cudi_retail_search_book', array('language' => $this->getLanguage()));

        $myDeals = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Deal')
            ->findAllByBuyerQuery($academic->getId())->getResult();

        $retails = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Retail')
            ->FindAllQuery()->getResult();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            $bookSearchForm->setData($formData);

            if ($formData['search_string'] === '') {
                $retails = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Retail')
                    ->findAllQuery()->getResult();
            } elseif ($bookSearchForm->isValid()) {
                $formData = $bookSearchForm->getData();

                $retails = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Retail')
                    ->findAllByTitle($formData['search_string']);
            } else {
                $this->flashMessenger()->error(
                    'Error',
                    'The given search query was invalid!'
                );
            }
        }

        foreach ($myDeals as $deal) {
            if (in_array($deal->getRetail(), $retails)) {
                unset($retails[array_keys($retails, $deal->getRetail())[0]]);
            }
        }

        $retailOverviewText = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.retail_overview_text')
        )[$this->getLanguage()->getAbbrev()];


        return new ViewModel(
            array(
                'bookSearchForm'     => $bookSearchForm,
                'searchResults'      => $retails,
                'retailOverviewText' => $retailOverviewText,
            )
        );
    }

    public function recommendedRetailsAction()
    {
        $enableRetail = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.retail_enabled');

        if ($enableRetail == 0) {
            throw new HasNoAccessException(
                'The retail section is closed'
            );
        }

        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            $this->redirect()->toRoute('common_auth',
                array(
                    'redirect' => urlencode($this->getRequest()->getRequestUri()),
                ));
        }

        $bookSearchForm = $this->getForm('cudi_retail_search_book', array('language' => $this->getLanguage()));

        $myDeals = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Deal')
            ->findAllByBuyerQuery($academic->getId())->getResult();

        $retails = $this->getRecommendedRetails();

        foreach ($myDeals as $deal) {
            if (in_array($deal->getRetail(), $retails)) {
                unset($retails[array_keys($retails, $deal->getRetail())[0]]);
            }
        }

        $retailOverviewText = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.retail_overview_text')
        )[$this->getLanguage()->getAbbrev()];


        return new ViewModel(
            array(
                'bookSearchForm'     => $bookSearchForm,
                'searchResults'      => $retails,
                'retailOverviewText' => $retailOverviewText,
            )
        );
    }

    public function dealAction()
    {
        $enableRetail = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.retail_enabled');

        if ($enableRetail == 0) {
            throw new HasNoAccessException(
                'The retail section is closed'
            );
        }

        $this->initAjax();

        $retail = $this->getRetailEntity();
        if ($retail === null) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error'),
                )
            );
        }


        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error'),
                )
            );
        }

        $enquiredDeals = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Deal')
            ->findAllByRetail($retail->getId());
        $alreadyEnquiredByAcademic = false;
        foreach ($enquiredDeals as $deal) {
            $alreadyEnquiredByAcademic = $alreadyEnquiredByAcademic || $deal->getBuyer() === $academic;
        }

        if ($alreadyEnquiredByAcademic === true) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error'),
                )
            );
        }

        $deal = new Deal($retail, $academic);
        $this->getEntityManager()->persist($deal);
        $this->getEntityManager()->flush();

        $this->sendMail($retail, $academic);

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                ),
            )
        );
    }

    public function myDealsAction()
    {
        $enableRetail = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.retail_enabled');

        if ($enableRetail == 0) {
            throw new HasNoAccessException(
                'The retail section is closed'
            );
        }

        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            $this->redirect()->toRoute('common_auth',
                array(
                    'redirect' => urlencode($this->getRequest()->getRequestUri()),
                ));
        }

        $myDeals = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Deal')
            ->findAllByBuyerQuery($academic->getId())->getResult();

        $retailMyDealsText = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.retail_my_deals_text')
        )[$this->getLanguage()->getAbbrev()];

        return new ViewModel(
            array(
                'myDeals'           => $myDeals,
                'retailMyDealsText' => $retailMyDealsText

            )
        );
    }

    public function myRetailsAction()
    {
        $enableRetail = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.retail_enabled');

        if ($enableRetail == 0) {
            throw new HasNoAccessException(
                'The retail section is closed'
            );
        }

        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            $this->redirect()->toRoute('common_auth',
                array(
                    'redirect' => urlencode($this->getRequest()->getRequestUri()),
                ));
        }

        $addForm = $this->getForm('cudi_retail_add');
        $editForm = $this->getForm('cudi_retail_edit');

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            $isEdit = $data['submit'] == 'Save' || $data['submit'] == 'Opslaan';

            if ($isEdit) {
                $editForm->setData($data);

                if ($editForm->isValid()) {
                    $retail = $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Retail')
                        ->findOneById($data['retailId']);
                    $editForm->hydrateObject($retail);
                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->success(
                        'Success',
                        'The retail was successfully edited!'
                    );

                    $this->redirect()->toRoute(
                        'cudi_retail',
                        array(
                            'action' => 'myRetails',
                        )
                    );
                }
            } else {
                $addForm->setData($data);

                if ($addForm->isValid()) {
                    $article = $this->getEntityManager()
                        ->getRepository('CudiBundle\Entity\Article')
                        ->findOneById($data['article']['id']);
                    $retail = new Retail($article, $academic);

                    $this->getEntityManager()->persist(
                        $addForm->hydrateObject($retail)
                    );

                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->success(
                        'Success',
                        'The retail was successfully added!'
                    );

                    $this->redirect()->toRoute(
                        'cudi_retail',
                        array(
                            'action' => 'myRetails',
                        )
                    );

                    return new ViewModel();
                }
            }
        }

        $retails = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Retail')
            ->findAllByOwnerQuery($academic->getId())
            ->getResult();

        $retailMyRetailsText = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.retail_my_retails_text')
        )[$this->getLanguage()->getAbbrev()];

        return new ViewModel(
            array(
                'retails'             => $retails,
                'addForm'             => $addForm,
                'editForm'            => $editForm,
                'retailMyRetailsText' => $retailMyRetailsText
            )
        );
    }

    public function deleteRetailAction()
    {
        $enableRetail = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.retail_enabled');

        if ($enableRetail == 0) {
            throw new HasNoAccessException(
                'The retail section is closed'
            );
        }

        $this->initAjax();


        if ($this->getRequest()->isPost()) {
            $academic = $this->getAcademicEntity();
            if ($academic === null) {
                $this->redirect()->toRoute('common_auth',
                    array(
                        'redirect' => urlencode($this->getRequest()->getRequestUri()),
                    ));
            }

            $retail = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Retail')
                ->findOneById($this->getParam('id'));

            if ($retail->getOwner() !== $academic) {
                return new ViewModel(
                    array(
                        'result' => (object) array('status' => 'error'),
                    )
                );
            }

            $associatedDeals = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Deal')
                ->findAllByRetail($retail->getId());

            foreach ($associatedDeals as $deal) {
                $this->getEntityManager()->remove($deal);
            }
            $this->getEntityManager()->remove($retail);
            $this->getEntityManager()->flush();
        }

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function deleteDealAction()
    {
        $enableRetail = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.retail_enabled');

        if ($enableRetail == 0) {
            throw new HasNoAccessException(
                'The retail section is closed'
            );
        }

        $this->initAjax();

        if ($this->getRequest()->isPost()) {
            $academic = $this->getAcademicEntity();
            if ($academic === null) {
                $this->redirect()->toRoute('common_auth',
                    array(
                        'redirect' => urlencode($this->getRequest()->getRequestUri()),
                    ));
            }

            $deal = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Deal')
                ->findOneById($this->getParam('id'));
            if ($deal->getBuyer() !== $academic) {
                return new ViewModel(
                    array(
                        'result' => (object) array('status' => 'error'),
                    )
                );
            }

            $this->getEntityManager()->remove($deal);
            $this->getEntityManager()->flush();
        }

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function getRecommendedRetails():array
    {

        $enableRetail = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.retail_enabled');

        if ($enableRetail == 0) {
            throw new HasNoAccessException(
                'The retail section is closed'
            );
        }

        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            $this->redirect()->toRoute('common_auth',
                array(
                    'redirect' => urlencode($this->getRequest()->getRequestUri()),
                ));
        }

        $academicYear = $this->getCurrentAcademicYear();
        $subjects = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Syllabus\Enrollment\Subject')
            ->findAllByAcademicAndAcademicYear($academic, $academicYear);

        $articles = array();
        foreach ($subjects as $subject) {
            $subjectArticleMaps = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Article\SubjectMap')
                ->findAllBySubjectAndAcademicYear($subject->getSubject(), $academicYear);

            $allowedRetailTypes = unserialize(
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.retail_allowed_types')
            );

            foreach ($subjectArticleMaps as $articleMap) {
                $article = $articleMap->getArticle();
                $saleArticle = $this->getEntityManager()
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findOneByArticle($article, $academicYear);

                if (in_array($article->getType(), $allowedRetailTypes) && $saleArticle) {
                    array_push($articles, $article);
                }
            }
        }

        $retails = array();
        foreach ($articles as $article) {
            $retail = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Retail')
                ->findAllByArticle($article->getId());

            $retails = array_merge($retails, $retail);
        }

        return $retails;
    }

    public function articleTypeaheadAction()
    {
        $articles = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article')
            ->findAllByTitleAndAcademicYearQuery($this->getParam('string'), $this->getCurrentAcademicYear())->getResult();


        $allowedRetailTypes = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.retail_allowed_types')
        );

        $result = array();
        foreach ($articles as $saleArticle) {
            $article = $saleArticle->getMainArticle();
            if (in_array($article->getType(), $allowedRetailTypes)) {
                $item = (object) array();
                $item->id = $article->getId();
                $item->value = $article->getTitle();
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
     * @return Academic|null
     */
    private function getAcademicEntity()
    {
        if (!$this->getAuthentication()->isAuthenticated()) {
            return null;
        }

        $academic = $this->getAuthentication()->getPersonObject();

        if (!($academic instanceof Academic)) {
            return null;
        }

        return $academic;
    }

/**
     * @return Retail
     */
    private function getRetailEntity()
    {
        $academic = $this->getAcademicEntity();
        if ($academic === null) {
            return null;
        }

        $postData = $this->getRequest()->getPost();
        $id = $postData['id'];
        if ($id === null || !is_numeric($id)) {
            return null;
        }

        return $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Retail')
            ->findOneById($id);
    }

    private function sendMail($retail, $academic)
    {
        $mailAddress = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.mail');

        $mailName = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.mail_name');

        $language = $retail->getOwner()->getLanguage();
        if ($language === null) {
            $language = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Language')
                ->findOneByAbbrev('en');
        }

        $mailData = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.retail_enquired_mail')
        );

        $message = $mailData[$language->getAbbrev()]['content'];
        $subject = $mailData[$language->getAbbrev()]['subject'];

        $mail = new Message();
        $mail->setEncoding('UTF-8')
            ->setBody(
                str_replace(
                    array('{{ book }}', '{{ name }}', '{{ email }}'),
                    array($retail->getArticle()->getTitle(), $academic->getFullName(), $academic->getEmail()),
                    $message
                )
            )
            ->setFrom($mailAddress, $mailName)
            ->addTo($retail->getOwner()->getEmail(), $retail->getOwner()->getFullName())
            ->setSubject(str_replace('{{ book }}', $retail->getArticle()->getTitle(), $subject));

        if (getenv('APPLICATION_ENV') != 'development') {
            $this->getMailTransport()->send($mail);
        }
    }
}
