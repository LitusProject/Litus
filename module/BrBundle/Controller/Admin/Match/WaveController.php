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

namespace BrBundle\Controller\Admin\Match;

use BrBundle\Entity\Company;
use BrBundle\Entity\Match\Wave\CompanyWave;
use BrBundle\Entity\Match\Wave;
use Doctrine\ORM\ORMException;
use Laminas\View\Model\ViewModel;

/**
 * WaveController
 *
 * @author Robin Wroblowski <robin.wroblowski@vtk.be>
 */
class WaveController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $waves = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match\Wave')
            ->findAll();

        $paginator = $this->paginator()->createFromArray(
            $waves,
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'em'                => $this->getEntityManager(),
            )
        );
    }

    public function viewAction()
    {
        $wave = $this->getWaveEntity();
        if ($wave === null) {
            return new ViewModel();
        }
        $result = array();
        foreach ($wave->getCompanyWaves() as $cw){
            $item = (object) array();
            $item->id = $cw->getId();
            $item->company = $cw->getCompany()->getName();
            $item->matches = $cw->getMatches();
            $result[] = $item;
        }


        return new ViewModel(
            array(
                'result' => $result,
                'wave' => $wave,
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('br_match_wave_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $wave = $form->hydrateObject(new Wave());

                $this->getEntityManager()->persist($wave);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The wave was succesfully created!'
                );

                $this->redirect()->toRoute(
                    'br_admin_match_wave',
                    array(
                        'action' => 'manage',
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
        $wave = $this->getWaveEntity();
        if ($wave === null) {
            return new ViewModel();
        }

        $form = $this->getForm('br_match_wave_edit', array('wave' => $wave));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The wave was succesfully updated!'
                );

                $this->redirect()->toRoute(
                    'br_admin_match_wave',
                    array(
                        'action' => 'manage',
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
        $wave = $this->getWaveEntity();
        if ($wave === null) {
            return new ViewModel();
        }

        foreach($wave->getCompanyWaves() as $cw){
            foreach($cw->getMatches() as $map){
                $map->getMatch()->setWave(null);
                $this->getEntityManager()->remove($map);
            }
            $this->getEntityManager()->remove($cw);
        }
        $this->getEntityManager()->flush();
        $this->getEntityManager()->remove($wave);

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }


    public function generateWavesAction()
    {

        $wave = $this->getWaveEntity();
        if ($wave === null ) {
            return new ViewModel();
        }

        if (count($wave->getCompanyWaves()) > 0){
            $this->flashMessenger()->error(
                'Error',
                'The wave was already generated!'
            );


            $this->redirect()->toRoute(
                'br_admin_match_wave',
                array(
                    'action' => 'manage',
                )
            );

            return new ViewModel();        }

        $companies = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findAll();

        $nbTopMatches = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.wave_nb_top_matches');

        foreach($companies as $company){
            $profileMaps = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Match\Profile\ProfileCompanyMap')
                ->findByCompany($company);
            if (count($profileMaps) > 0){
                $CW = $this->makeCompanyWave($company, $nbTopMatches, $wave);
                $companyWaves[] = $CW;
            }
        }
        $this->getEntityManager()->flush();

        $this->flashMessenger()->success(
            'Success',
            'The wave was generated!'
        );

        $this->redirect()->toRoute(
            'br_admin_match_wave',
            array(
                'action' => 'manage',
            )
        );
        return new ViewModel();
    }

    /**
     * @return Wave|null
     */
    private function getWaveEntity()
    {
        $wave = $this->getEntityById('BrBundle\Entity\Match\Wave');

        if (!($wave instanceof Wave)) {
            $this->flashMessenger()->error(
                'Error',
                'No wave was found!'
            );

            $this->redirect()->toRoute(
                'br_admin_match_wave',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $wave;
    }

    /**
     * @param Company $company
     * @param int $nb
     * @param Wave $wave
     * @return CompanyWave
     * @throws ORMException
     */
    private function makeCompanyWave(Company $company, int $nb, Wave $wave){
        $matcheeMaps = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Match\MatcheeMap\CompanyMatcheeMap')
            ->findByCompany($company);

        usort($matcheeMaps, function($a, $b) {
            $am = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Match')
                ->findOneByCompanyMatchee($a);

            $bm = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Match')
                ->findOneByCompanyMatchee($b);
            return $am->getMatchPercentage() - $bm->getMatchPercentage();
        });

        $cw = new CompanyWave($wave, $company);
        $this->getEntityManager()->persist($cw);
        $this->getEntityManager()->flush();

        $i = 0; // index of highest match
        $n = 0; // number of matches in this wave
        $sizeMM = sizeof($matcheeMaps);

        while ($i < $sizeMM && $n<$nb){
            $matchee = $matcheeMaps[$i];
            $match = $this->getEntityManager()
                ->getRepository('BrBundle\Entity\Match')
                ->findOneByCompanyMatchee($matchee);

            if (!is_null($match->getWave())){
                $i += 1;
                continue;
            }
            $n += 1;
            $map = new Wave\WaveMatchMap($match, $cw);
            $this->getEntityManager()->persist($map);
            $cw->addMatch($map);
            $match->setWave($map);
            $i += 1;
        }


        return $cw;

    }
}
