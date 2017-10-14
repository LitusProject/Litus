<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Hannes Vandecasteele <hannes.vandecasteele@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace SportBundle\Controller\Run;

use Zend\View\Model\ViewModel,
    SportBundle\Entity\Runner;

/**
 * TombolaController
 *
 * @author Hannes Vandecasteele <hannes.vandecasteele@vtk.be>
 */
class TombolaController extends \SportBundle\Component\Controller\RunController
{
    /**
     * @param  integer $startTime
     * @return array
     */
    private function generateHappyHours($startTime)
    {
        $optionsArray = array();
        for ($i = 0; $i < 8; $i++) {
            $startInterval = ($startTime + 3 * $i) % 24;
            if ($startInterval < 10) {
                $startInterval = 0 . $startInterval;
            }

            $endInterval = ($startTime + 3 * ($i + 1)) % 24;
            if ($endInterval < 10) {
                $endInterval = 0 . $endInterval;
            }

            $optionKey = $startInterval . $endInterval;
            $optionValue = $startInterval . ':00 - ' . $endInterval . ':00';

            $optionsArray[$optionKey] = $optionValue;
        }

        $runners = $this->getEntityManager()
            ->getRepository('SportBundle\Entity\Runner')
            ->findAllByAcademicYearQuery($this->getCurrentAcademicYear())->getResult();

        return $this->cleanHappyHoursArray($optionsArray, $runners);
    }

    private function cleanHappyHoursArray(array $optionsArray, array $runners)
    {
        $countArray = array();
        foreach ($optionsArray as $key => $value) {
            $countArray[$key] = 0;
        }
        foreach ($runners as $runner) {
            $happyHour = $runner->getHappyHour();
            if (isset($countArray[$happyHour])) {
                $countArray[$happyHour]++;
            }
        }
        
        $returnArray = array();
        foreach ($optionsArray as $key => $value) {
            if ($countArray[$key] < 40) {
                $returnArray[$key] = $value;
            }
        }
        return $returnArray;
    }

    public function indexAction()
    {
        $happyHours = $this->generateHappyHours(20);
        $form = $this->getForm('sport_tombola_add', array(
            'happyHours' => $happyHours,
        ));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $runner = new Runner('', '', $this->getCurrentAcademicYear());

                $runner->setHappyHour($formData['information']['happy_hour']);
                $runner->setRunnerIdentification($formData['information']['university_identification']);

                if (null !== $runner) {
                    try {
                        $this->getEntityManager()->persist($runner);
                        $this->getEntityManager()->flush();
                    } catch (\Exception $e) {
                        $this->flashMessenger()->error(
                            'Error',
                            'This runner is already registered for the tombola.'
                        );
                        
                        $this->redirect()->toRoute(
                            'sport_run_tombola',
                            array(
                                'action' => 'index',
                            )
                        );

                        return new ViewModel(
                            array(
                                'form' => $form,
                            )
                        );
                    }

                    $this->flashMessenger()->success(
                        'Success',
                        'The runner was successfully registerd for the tombola!'
                    );
                    
                    $this->redirect()->toRoute(
                        'sport_run_index',
                        array(
                            'action' => 'index',
                        )
                    );
                }
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function getNameAction()
    {
        $this->initAjax();
        
        if (8 == strlen($this->getParam('university_identification'))) {
            $runner = $this->getEntityManager()
                    ->getRepository('SportBundle\Entity\Runner')
                    ->findOneByRunnerIdentification($this->getParam('university_identification'));

            if (null === $runner) {
                $runner = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Academic')
                    ->findOneByUniversityIdentification($this->getParam('university_identification'));
                $department = null;
            } else {
                $department = $runner->getDepartment();
                if (null !== $department) {
                    $department = $department->getId();
                }
            }

            if (null !== $runner) {
                return new ViewModel(
                    array(
                        'result' => (object) array(
                            'status' => 'success',
                            'firstName' => $runner->getFirstName(),
                            'lastName' => $runner->getLastName(),
                            'department' => $department,
                        ),
                    )
                );
            }
        }

        return new ViewModel();
    }
}
