<?php

namespace SyllabusBundle\Component\Parser;

use CommonBundle\Component\Redis\Client as RedisClient;
use CommonBundle\Component\Util\AcademicYear;
use CommonBundle\Entity\User\Person\Academic;
use CommonBundle\Entity\User\Status\University as UniversityStatus;
use Doctrine\ORM\EntityManager;
use Laminas\Http\Client as HttpClient;
use Laminas\Mail\Transport\TransportInterface;
use RuntimeException;
use SimpleXMLElement;
use SyllabusBundle\Entity\Study as StudyEntity;
use SyllabusBundle\Entity\Study\Combination;
use SyllabusBundle\Entity\Study\ModuleGroup;
use SyllabusBundle\Entity\Study\SubjectMap;
use SyllabusBundle\Entity\Subject;
use SyllabusBundle\Entity\Subject\ProfMap;

/**
 * Study
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Study
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var TransportInterface
     */
    private $mailTransport;

    /**
     * @var mixed
     */
    private $redisClient;

    /**
     * @var string
     */
    private $redisChannel;

    /**
     * @var \CommonBundle\Entity\General\AcademicYear
     */
    private $academicYear;

    /**
     * @var array
     */
    private $subjectCache;

    /**
     * @var array
     */
    private $profCache;

    /**
     * @var array
     */
    private $activeCombinations;

    /**
     * @param EntityManager      $entityManager
     * @param TransportInterface $mailTransport
     * @param RedisClient        $redisClient
     * @param string             $redisChannel
     */
    public function __construct(EntityManager $entityManager, TransportInterface $mailTransport, RedisClient $redisClient, $redisChannel)
    {
        $this->entityManager = $entityManager;
        $this->mailTransport = $mailTransport;
        $this->redisClient = $redisClient;
        $this->redisChannel = $redisChannel;

        $this->academicYear = $this->getAcademicYear();
        $this->activeCombinations = array();
    }

    /**
     * @return void
     */
    public function update()
    {
        $isEnabled = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('syllabus.enable_update');

        if (!$isEnabled) {
            return;
        }

        $urls = $this->getUrls();

        $this->callback('progress', 1);

        $counter = 0;

        $this->cleanUpAcademicYear();
        $this->callback('cleanup', '');

        foreach ($urls as $url) {
            $counter++;
            $this->getEntityManager()->clear();
            $this->academicYear = $this->getAcademicYear();

            $this->callback('load_xml', substr($url, strrpos($url, '/') + 1));

            $xml = loadXmlFromUrl($url);

            $this->subjectCache = array();
            $this->profCache = array();

            $this->callback('create_combinations', (string) $xml->data->programma->titel);
            $combinations = $this->createCombinations($xml->data->programma);
            $this->callback('create_module_groups', (string) $xml->data->programma->titel);
            $groups = $this->createModuleGroups($xml->data->programma, (string) $xml->data->programma->doceertaal->code);
            $this->connectModuleGroups($combinations, $groups);

            $this->callback('saving_data', (string) $xml->data->programma->titel);

            $this->getEntityManager()->flush();

            $this->callback('progress', round($counter / count($urls) * 100, 4));
        }

        $this->postCleanUpAcademicYear();

        $this->callback('done');
    }

    /**
     * @param  SimpleXMLElement $xml
     * @return array
     */
    private function createCombinations(SimpleXMLElement $xml)
    {
        $combinations = array();

        $mainTitle = (string) $xml->titel;

        foreach ($xml->fases->children() as $phase) {
            $phaseNumber = (int) $phase->attributes()->code;
            if (count($phase->toegestane_combinaties->children()) == 0) {
                $externalId = $phaseNumber . ((int) $xml->attributes()->id);
                $combination = $this->createCombination($phaseNumber, $externalId, $mainTitle);

                $combinations['_' . $externalId] = array(
                    'entity' => $combination,
                    'groups' => array(),
                );
            } else {
                foreach ($phase->toegestane_combinaties->children() as $data) {
                    $externalId = $phaseNumber . ((int) $data->attributes()->id);
                    $title = (string) $data->titel;
                    $title = str_replace(array('Hoofdrichting', 'Nevenrichting', 'Minor', 'Major'), '', $title);
                    $title = preg_replace('/\(\s*Afstudeerrichting[^)]+\)/i', '', $title);
                    $titleParts = explode('+', $title);
                    $title = '';
                    foreach ($titleParts as $part) {
                        $title .= ucfirst(trim($part)) . ' + ';
                    }
                    $title = trim($title, " \t\n\r\0\x0B+");
                    $combination = $this->createCombination($phaseNumber, $externalId, $mainTitle . ' - ' . $title);

                    $combinations['_' . $externalId] = array(
                        'entity' => $combination,
                        'groups' => array(),
                    );

                    foreach ($data->modulegroepen->children() as $group) {
                        $combinations['_' . $externalId]['groups'][] = (int) $group->attributes()->id;
                    }
                }
            }
        }

        return $combinations;
    }

    /**
     * @param  integer $phase
     * @param  integer $id
     * @param  string  $title
     * @return Combination
     */
    private function createCombination($phase, $id, $title)
    {
        $combination = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Study\Combination')
            ->findOneByExternalId($id);

        if ($combination === null) {
            $combination = new Combination();
            $this->getEntityManager()->persist($combination);
        }

        $combination->setPhase($phase)
            ->setExternalId($id)
            ->setTitle(html_entity_decode(trim($title)));

        $study = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Study')
            ->findOneByCombinationAndAcademicYear($combination, $this->academicYear);

        if ($study === null) {
            $study = new StudyEntity();
            $study->setCombination($combination)
                ->setAcademicYear($this->academicYear);

            $this->getEntityManager()->persist($study);
        }

        $this->activeCombinations[] = $combination->getId();

        return $combination;
    }

    /**
     * @param  SimpleXMLElement $xml
     * @param  string           $language
     * @param  array            $parents
     * @return array
     */
    private function createModuleGroups(SimpleXMLElement $xml, $language, $parents = array())
    {
        $groups = array();

        foreach ($xml->modulegroep as $group) {
            if ($group->tonen_in_programmagids != 'True') {
                continue;
            }

            $phases = explode(',', $group->attributes()->fases);
            $currentParents = array();

            foreach ($phases as $phase) {
                $externalId = $phase . (int) $group->attributes()->id;
                $moduleGroup = $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Study\ModuleGroup')
                    ->findOneByExternalId($externalId);

                if ($moduleGroup === null) {
                    $moduleGroup = new ModuleGroup();
                    $this->getEntityManager()->persist($moduleGroup);
                }

                $moduleGroup->setExternalId($externalId)
                    ->setTitle(html_entity_decode(trim($group->titel)))
                    ->setPhase($phase)
                    ->setLanguage(trim($language))
                    ->setMandatory($group->attributes()->verplicht == 'True');

                if ((int) $group->attributes()->niveau > 1) {
                    if (isset($parents[$phase])) {
                        $moduleGroup->setParent($parents[$phase]);
                    } elseif (count($group->opleidingsonderdelen->children()) > 0) {
                        throw new RuntimeException('Module group ' . $externalId . ' has no parents.');
                    }
                }

                $currentParents[$phase] = $moduleGroup;
                $groups['_' . $externalId] = $moduleGroup;
            }

            if (count($group->opleidingsonderdelen->children()) > 0) {
                $this->createSubjects($group->opleidingsonderdelen, $currentParents);
            }

            $groups = array_merge($groups, $this->createModuleGroups($group, $language, $currentParents));
        }

        return $groups;
    }

    /**
     * @param  SimpleXMLElement $xml
     * @param  array            $parents
     * @return void
     */
    private function createSubjects(SimpleXMLElement $xml, $parents = array())
    {
        foreach ($xml->children() as $data) {
            $this->callback('create_subject', html_entity_decode(trim((string) $data->titel)));

            $code = trim((string) $data->attributes()->code);

            $subject = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\Subject')
                ->findOneByCode($code);

            if ($subject === null) {
                if (isset($this->subjectCache[$code])) {
                    $subject = $this->subjectCache[$code];
                } else {
                    $subject = new Subject();
                    $this->getEntityManager()->persist($subject);
                }
            }

            $subject->setCode($code)
                ->setName(html_entity_decode(trim((string) $data->titel)))
                ->setSemester((int) $data->aanbodperiode)
                ->setCredits((int) $data->studiepunten);

            $this->subjectCache[$code] = $subject;

            $this->createProfs($data->docenten, $subject);

            $mandatory = $data->attributes()->verplicht == 'True';

            foreach ($data->fases->children() as $phase) {
                $phase = (int) $phase;
                if (!isset($parents[$phase])) {
                    throw new RuntimeException('Subject ' . $code . ' has no parents.');
                }
                $map = new SubjectMap($parents[$phase], $subject, $mandatory, $this->academicYear);
                $this->getEntityManager()->persist($map);
            }
        }
    }

    /**
     * @param  SimpleXMLElement $xml
     * @param  Subject          $subject
     * @return void
     */
    private function createProfs(SimpleXMLElement $xml, Subject $subject)
    {
        $maps = array();

        foreach ($xml->children() as $data) {
            $identification = 'u' . substr(trim($data->attributes()->persno), 1);
            $prof = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneByUniversityIdentification($identification);

            if ($prof === null) {
                if (isset($this->profCache[$identification])) {
                    $prof = $this->profCache[$identification];
                } else {
                    $info = $this->getProfInfo($identification);
                    if ($info === null) {
                        continue;
                    }

                    $prof = new Academic();
                    $this->getEntityManager()->persist($prof);

                    $prof->setUsername($identification)
                        ->setRoles(
                            array(
                                $this->getEntityManager()
                                    ->getRepository('CommonBundle\Entity\Acl\Role')
                                    ->findOneByName('prof'),
                            )
                        )
                        ->setFirstName(trim($data->voornaam))
                        ->setLastName(trim($data->familienaam))
                        ->setEmail($info['email'])
                        ->setPersonalEmail($info['email'])
                        ->setUniversityEmail($info['email'])
                        ->setUniversityIdentification($identification)
                        ->activate($this->getEntityManager(), $this->mailTransport, true);
                }
            } else {
                $info = $this->getProfInfo($identification);
                if ($info === null) {
                    continue;
                }

                $prof->setFirstName(trim($data->voornaam))
                    ->setLastName(trim($data->familienaam))
                    ->setEmail($info['email'])
                    ->setPersonalEmail($info['email'])
                    ->setUniversityEmail($info['email']);
            }

            if (!isset($this->profCache[$identification])) {
                $this->profCache[$identification] = $prof;
            }

            if ($prof->canHaveUniversityStatus($this->academicYear)) {
                $prof->addUniversityStatus(
                    new UniversityStatus(
                        $prof,
                        'professor',
                        $this->academicYear
                    )
                );
            }

            $map = $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\Subject\ProfMap')
                ->findOneBySubjectAndProfAndAcademicYear($subject, $prof, $this->academicYear);

            if ($map === null) {
                if (!isset($maps[$prof->getUniversityIdentification()])) {
                    $map = new ProfMap($subject, $prof, $this->academicYear);
                    $this->getEntityManager()->persist($map);
                    $maps[$prof->getUniversityIdentification()] = $map;
                }
            }
        }
    }

    /**
     * @param  string $identification
     * @return array|null
     */
    private function getProfInfo($identification)
    {
        try {
            $url = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('syllabus.persons_api_url');

            $client = new HttpClient(
                $url,
                array(
                    'timeout' => 5,
                )
            );
            $client->setParameterGet(
                array(
                    '$format' => 'json',
                    '$filter' => 'userId eq \'' . $identification . '\'',
                    '$select' => 'preferredMailAddress',
                )
            );

            $data = json_decode($client->send()->getBody(), true)['d'];
            if (count($data['results']) == 1) {
                return array(
                    'email' => $data['results'][0]['preferredMailAddress'],
                );
            }

            return null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * @param  array $combinations
     * @param  array $moduleGroups
     * @return void
     */
    private function connectModuleGroups($combinations, $moduleGroups)
    {
        foreach ($combinations as $combination) {
            $groups = array();

            if (count($combination['groups']) == 0) {
                foreach ($moduleGroups as $group) {
                    if ($group->getPhase() == $combination['entity']->getPhase() && $group->getParent() === null) {
                        $groups[] = $group;
                        break;
                    }
                }
            } else {
                foreach ($combination['groups'] as $id) {
                    if (isset($moduleGroups['_' . $combination['entity']->getPhase() . $id])) {
                        $groups[] = $moduleGroups['_' . $combination['entity']->getPhase() . $id];
                    }
                }

                $generalGroups = $this->getGeneralMandatoryGroups($combination['entity']->getPhase(), $moduleGroups);
                if (count($generalGroups) > 0) {
                    $groups = array_merge($groups, $generalGroups);
                }
            }

            $combination['entity']->setModuleGroups($groups);
        }
    }

    /**
     * @param  integer $phase
     * @param  array   $moduleGroups
     * @return array
     */
    private function getGeneralMandatoryGroups($phase, $moduleGroups)
    {
        $groups = array();

        //for each child node in the same phase check if branch is fully mandatory
        foreach ($moduleGroups as $group) {
            if ($phase == $group->getPhase() && count($group->getChildren()) == 0) {
                if ($this->isFullMandatoryBranch($group)) {
                    $groups[] = $group;
                } else {
                }
            }
        }

        return $groups;
    }

    /**
     * @param  ModuleGroup $group
     * @return boolean
     */
    private function isFullMandatoryBranch($group)
    {
        $result = false;
        if ($group->isMandatory()) {
            if ($group->getParent() !== null) {
                $result = $this->isFullMandatoryBranch($group->getParent());
            } else {
                return true;
            }
        }

        return $result;
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @return void
     */
    private function cleanUpAcademicYear()
    {
        $mapping = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Study\SubjectMap')
            ->findByAcademicYear($this->academicYear);

        foreach ($mapping as $map) {
            $this->getEntityManager()->remove($map);
        }

        $mapping = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Subject\ProfMap')
            ->findByAcademicYear($this->academicYear);

        foreach ($mapping as $map) {
            $this->getEntityManager()->remove($map);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @return void
     */
    private function postCleanUpAcademicYear()
    {
        $studies = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Study')
            ->findByAcademicYear($this->academicYear);

        foreach ($studies as $study) {
            if (!in_array($study->getCombination()->getId(), $this->activeCombinations)) {
                $this->getEntityManager()->remove($study);
            }
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @param string      $type
     * @param string|null $extra
     */
    private function callback($type, $extra = null)
    {
        $this->redisClient->publish(
            $this->redisClient->getChannelName($this->redisChannel),
            $this->redisClient->serialize(
                array(
                    'type'  => $type,
                    'extra' => trim($extra),
                )
            )
        );
    }

    /**
     * @return array
     */
    private function getUrls()
    {
        $url = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('syllabus.root_xml');

        $url = str_replace('{{ year }}', $this->academicYear->getStartDate()->format('Y'), $url);

        $departments = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('syllabus.department_ids')
        );

        $this->callback('load_xml', substr($url, strrpos($url, '/') + 1));

        $studies = array();

        $root = loadXmlFromUrl($url);
        foreach ($root->data->children() as $organization) {
            foreach ($organization->children() as $department) {
                if (in_array($department->attributes()->id, $departments)) {
                    foreach ($department->kwalificatie->children() as $classification) {
                        foreach ($classification->graad as $grade) {
                            foreach ($grade->opleidingen->children() as $study) {
                                $studies[] = array(
                                    'id'       => (string) $study->attributes()->id,
                                    'language' => $study->taal,
                                );
                            }
                        }
                    }
                }
            }
        }

        $departmentUrl = str_replace(
            '{{ year }}',
            $this->academicYear->getStartDate()->format('Y'),
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('syllabus.department_url')
        );

        $studyUrl = str_replace(
            '{{ year }}',
            $this->academicYear->getStartDate()->format('Y'),
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('syllabus.study_url')
        );

        $urls = array();
        foreach ($studies as $study) {
            $url = str_replace(
                array(
                    '{{ language }}',
                    '{{ id }}',
                ),
                array(
                    $study['language'],
                    $study['id'],
                ),
                $departmentUrl
            );

            $this->callback('load_xml', substr($url, strrpos($url, '/') + 1));

            $xml = loadXmlFromUrl($url);
            foreach ($xml->data->opleiding->programmas->children() as $study) {
                $urls[] = str_replace(
                    array(
                        '{{ language }}',
                        '{{ id }}',
                    ),
                    array(
                        strtolower((string) $study->taal->code),
                        (string) $study->attributes()->id,
                    ),
                    $studyUrl
                );
            }
        }

        return $urls;
    }

    function loadXmlFromUrl($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $xmlString = curl_exec($ch);
        if ($xmlString === false) {
            throw new Exception("cURL error: " . curl_error($ch));
        }
        curl_close($ch);

        return simplexml_load_string($xmlString);
    }

    /**
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    private function getAcademicYear()
    {
        return AcademicYear::getOrganizationYear($this->getEntityManager());
    }
}
