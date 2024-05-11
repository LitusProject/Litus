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
     * input: {
     *      "key": "api key",
     *      "company": "company name",
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
