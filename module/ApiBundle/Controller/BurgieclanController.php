<?php

namespace ApiBundle\Controller;

use ApiBundle\Component\Controller\ActionController\ApiController;
use Laminas\View\Model\ViewModel;
use SecretaryBundle\Entity\Syllabus\Enrollment\Subject;

/**
 * BurgieclanController
 * This controller contains endpoints used in Burgieclan ({@link https://github.com/VTKLeuven/burgieclan}).
 */
class BurgieclanController extends ApiController
{
    /**
     * output:
     * {
     *     "0": {
     *         "name": "Course 1",
     *         "code": "H0XXXX",
     *         "credits": 5,
     *         "semester": 2
     *     },
     *      "1": {
     *         "name": "Course 2",
     *         "code": "G0XXXX",
     *         "credits": 4,
     *         "semester": 2,
     *     },
     *     ...
     * },
 * }
     */
    public function getCoursesAction()
    {
        $this->initJson();
        $accessToken = $this->getAccessToken();
        if ($accessToken === null) {
            return $this->error(401, 'The access token is not valid');
        }

        $person = $accessToken->getPerson();

        if ($person === null) {
            return $this->error(404, 'The person was not found');
        }

        $currentYear = $this->getCurrentAcademicYear();
        $enrollments = $this->getEntityManager()
            ->getRepository(Subject::class)
            ->findAllByAcademicAndAcademicYear($person, $currentYear);

        $result = array();
        foreach ($enrollments as $enrollment) {
            assert($enrollment instanceof Subject);
            $subject = $enrollment->getSubject();
            $result[] = array(
                'name' => $subject->getName(),
                'code' => $subject->getCode(),
                'credits' => $subject->getCredits(),
                'semester' => $subject->getSemester(),
            );
        }

        return new ViewModel(
            array(
                'result' => (object)$result,
            )
        );
    }
}
