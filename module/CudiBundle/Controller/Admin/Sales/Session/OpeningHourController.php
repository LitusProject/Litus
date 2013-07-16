<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Controller\Admin\Sales\Session;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CudiBundle\Entity\Sale\Session\OpeningHour\OpeningHour,
    CudiBundle\Entity\Sale\Session\OpeningHour\Translation,
    CudiBundle\Form\Admin\Sales\Session\OpeningHour\Add as AddForm,
    CudiBundle\Form\Admin\Sales\Session\OpeningHour\Edit as EditForm,
    Zend\View\Model\ViewModel;

/**
 * OpeningHourController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class OpeningHourController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Session\OpeningHour\OpeningHour')
                ->findAllActive(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function oldAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Session\OpeningHour')
                ->findAllOld(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = new AddForm($this->getEntityManager());

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $openingHour = new OpeningHour(
                    \DateTime::createFromFormat('d#m#Y H#i', $formData['start']),
                    \DateTime::createFromFormat('d#m#Y H#i', $formData['end']),
                    $this->getAuthentication()->getPersonObject()
                );
                $this->getEntityManager()->persist($openingHour);

                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();

                foreach($languages as $language) {
                    if ('' != $formData['comment_' . $language->getAbbrev()]) {
                        $translation = new Translation(
                            $openingHour,
                            $language,
                            $formData['comment_' . $language->getAbbrev()]
                        );

                        $this->getEntityManager()->persist($translation);
                    }
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The opening hour was successfully added!'
                    )
                );

                $this->redirect()->toRoute(
                    'cudi_admin_sales_session_openinghour',
                    array(
                        'action' => 'manage'
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function editAction()
    {
        if (!($openingHour = $this->_getOpeningHour()))
            return new ViewModel();

        $form = new EditForm($openingHour, $this->getEntityManager());

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $openingHour->setStart(\DateTime::createFromFormat('d#m#Y H#i', $formData['start']))
                    ->setEnd(\DateTime::createFromFormat('d#m#Y H#i', $formData['end']));

                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();

                foreach($languages as $language) {
                    $translation = $openingHour->getTranslation($language, false);

                    if (null !== $translation) {
                        $translation->setComment($formData['comment_' . $language->getAbbrev()]);
                    } else {
                        if ('' != $formData['comment_' . $language->getAbbrev()]) {
                            $translation = new Translation(
                                $openingHour,
                                $language,
                                $formData['comment_' . $language->getAbbrev()]
                            );

                            $this->getEntityManager()->persist($translation);
                        }
                    }
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The opening hour was successfully updated!'
                    )
                );

                $this->redirect()->toRoute(
                    'cudi_admin_sales_session_openinghour',
                    array(
                        'action' => 'manage'
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($openingHour = $this->_getOpeningHour()))
            return new ViewModel();

        $this->getEntityManager()->remove($openingHour);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    private function _getOpeningHour()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the opening hour!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_session_openinghour',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $openingHour = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Session\OpeningHour\OpeningHour')
            ->findOneById($this->getParam('id'));

        if (null === $openingHour) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No opening hour with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_admin_sales_session_openinghour',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $openingHour;
    }
}