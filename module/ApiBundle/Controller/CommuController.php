<?php

namespace ApiBundle\Controller;

use Laminas\View\Model\ViewModel;

/**
 * CommuController
 */
class CommuController extends \ApiBundle\Component\Controller\ActionController\ApiController
{

    /**
     * input: {
     *      "key": "api key",
     *      "week_start_date": "YYYYMMDD"
     * }
     */
    public function getCudiOpeningHoursAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->error(405, 'This endpoint can only be accessed through POST');
        }

        $week_start_date_str = $this->getRequest()->getPost('week_start_date');

        $openingHours = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Session\OpeningHour')
            ->findWeek($week_start_date_str);

        $count = 0;
        $result = array();
        foreach ($openingHours as $openingHour) {
            $count++;
            $result[] = array(
                'startDate' => $openingHour->getStart()->format('c'),
                'endDate'   => $openingHour->getEnd()->format('c'),
            );
        }

        return new ViewModel(
            array(
                'result' => (object) array(
                    'status' => 'success',
                    'opening_hours' => $result,
                ),
            )
        );
    }
}