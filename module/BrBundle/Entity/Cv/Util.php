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

namespace BrBundle\Entity\Cv;

use BrBundle\Entity\Cv\Entry;
use CommonBundle\Component\Util\Xml\Node;
use CommonBundle\Entity\General\AcademicYear;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\I18n\Translator;

/**
 * A Util class providing functions to retrieve the cv book data in a common way.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Util
{
    public static function getGrouped(EntityManager $entityManager, AcademicYear $academicYear)
    {
        $groups = $entityManager
            ->getRepository('SyllabusBundle\Entity\Group')
            ->findAllCvBook();

        $result = array();
        foreach ($groups as $group) {
            $entries = $entityManager
                ->getRepository('BrBundle\Entity\Cv\Entry')
                ->findAllByGroupAndAcademicYear($group, $academicYear);

            if (count($entries) > 0) {
                $result[] = array(
                    'id'      => 'group-' . $group->getId(),
                    'name'    => $group->getName(),
                    'entries' => $entries,
                );
            }
        }

        $cvStudies = $entityManager
            ->getRepository('BrBundle\Entity\Cv\Entry')
            ->findAllUngroupedStudies($academicYear);

        foreach ($cvStudies as $study) {
            $entries = $entityManager
                ->getRepository('BrBundle\Entity\Cv\Entry')
                ->findAllByStudy($study);

            if (count($entries) > 0) {
                $result[] = array(
                    'id'      => 'study-' . $study->getId(),
                    'name'    => $study->getTitle(),
                    'entries' => $entries,
                );
            }
        }

        return $result;
    }

    public static function getCvXML(EntityManager $entityManager, Entry $cv, Translator $translator)
    {
        $picturePath = 'public' . $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.profile_path');
        $phoneNumber = $cv->getPhoneNumber();

        $monthsEnglish = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' );
        $monthsDutch = array('Januari', 'Februari', 'Maart', 'April', 'Mei', 'Juni', 'Juli', 'Augustus', 'September', 'Oktober', 'November', 'December' );
        $birthday = $cv->getBirthDay()->format('d F Y');
        $birthday = str_ireplace($monthsEnglish, $monthsDutch, $birthday);

        return new Node(
            'cv',
            array(
                'id'        => $cv->getId(),
                'firstname' => $cv->getFirstName(),
                'lastname'  => $cv->getLastName(),
                'birthday'  => $birthday,
                'email'     => $cv->getEmail(),
                'phone'     => substr($phoneNumber, 0,3) . " (0)" . substr($phoneNumber, 3,3) . " " . substr($phoneNumber, 6,2) . " " . substr($phoneNumber, 8,2) . " " . substr($phoneNumber, 10,2),
                'img'       => $picturePath . '/' . $cv->getAcademic()->getPhotoPath(),
            ),
            Util::getSections($cv, $translator)
        );
    }

    private static function getSections(Entry $cv, Translator $translator)
    {
        $result = array();

        $result[] = new Node(
            'address',
            Util::getAddressArray($cv),
            null
        );

        $result[] = new Node(
            'subsection',
            array(
                'title' => $translator->translate('About Myself'),
            ),
            new Node(
                'content',
                null,
                $cv->getAbout()
            )
        );

        //Grades may be 0 in the database
        $masterGrade = (string) ($cv->getGrade() / 100);
        if ($cv->getGrade() == 0) {
            $masterGrade = "-";
        }
        $bachelorGrade = (string) ($cv->getPriorGrade() / 100);
        if ($cv->getPriorGrade() == 0) {
            $bachelorGrade = "-";
        }

        $result[] = new Node(
            'section',
            array(
                'title' => $translator->translate('Studies'),
            ),
            array(
                new Node(
                    'sec-special-studies',
                    array(
                        'start_master'      => (string) $cv->getMasterStart(),
                        'end_master'        => (string) $cv->getMasterEnd(),
                        'percentage_master' => $masterGrade,
                        'title_master'      => $cv->getStudy()->getTitle(),
                        'start_bach'        => (string) $cv->getBachelorStart(),
                        'end_bach'          => (string) $cv->getBachelorEnd(),
                        'percentage_bach'   => $bachelorGrade,
                        'title_bach'        => $cv->getPriorStudy(),
                    ),
                    null
                ),
                new Node(
                    'subsection',
                    array(
                        'title' => $translator->translate('Additional Diplomas'),
                    ),
                    new Node(
                        'content',
                        null,
                        $cv->getAdditionalDiplomas()
                    )
                ),
            )
        );

        if (
            (null !== $cv->getErasmusLocation() && '' !== $cv->getErasmusLocation())
            || (null !== $cv->getErasmusPeriod() && '' !== $cv->getErasmusPeriod())
        ) {
            $result[] = new Node(
                'section',
                array(
                    'title' => $translator->translate('Erasmus'),
                ),
                array(
                    new Node(
                        'sec-special-erasmus',
                        null,
                        array(
                            new Node(
                                'location',
                                null,
                                $cv->getErasmusLocation()
                            ),
                            new Node(
                                'period',
                                null,
                                $cv->getErasmusPeriod()
                            ),
                        )
                    ),
                )
            );
        }

        $index = 1;
        $languages = array();
        foreach ($cv->getLanguages() as $language) {
            $tmp = array(
                'name' . $index    => $language->getName(),
                'oral' . $index    => $translator->translate($language->getOralSkill()),
                'written' . $index => $translator->translate($language->getWrittenSkill()),
            );
            $languages = array_merge($languages,$tmp);
            $index++;
        }

        $result[] = new Node(
            'section',
            array(
                'title' => $translator->translate('Languages'),
            ),
            array(
                new Node(
                    'sec-special-languages',
                    array_merge(
                        array(
                        'oral'    => $translator->translate('Oral Skills'),
                        'written' => $translator->translate('Written Skills'),
                        ),
                        $languages
                    ),
                    null
                ),
                new Node(
                    'subsection',
                    array(
                        'title' => $translator->translate('Additional Info'),
                    ),
                    array(
                        new Node(
                            'content',
                            null,
                            $cv->getLanguageExtra()
                        ),
                    )
                ),
            )
        );

        if ($cv->hasOldExperiences()) {
            $result[] = new Node(
                'section',
                array(
                    'title' => $translator->translate('Capabilities'),
                ),
                array(
                    new Node(
                        'subsection',
                        array(
                            'title' => $translator->translate('Computer Skills'),
                        ),
                        new Node(
                            'content',
                            null,
                            $cv->getComputerSkills()
                        )
                    ),
                    new Node(
                        'subsection',
                        array(
                            'title' => $translator->translate('Experiences'),
                        ),
                        new Node(
                            'content',
                            null,
                            $cv->getExperiences()
                        )
                    ),
                )
            );
        } else {
            $index = 1;
            $experiences = array();
            foreach ($cv->getExperiences() as $experience) {
                $tmp = array(
                    'experience_type' . $index     => $translator->translate($experience->getType()),
                    'experience_function' . $index => $experience->getFunction(),
                    'experience_start' . $index    => strval($experience->getStartYear()),
                    'experience_end' . $index      => strval($experience->getEndYear()),
                );
                $experiences = array_merge($experiences,$tmp);
                $index++;
            }

            $result[] = new Node(
                'section',
                array(
                    'title' => $translator->translate('Capabilities'),
                ),
                array(
                    new Node(
                        'subsection',
                        array(
                            'title' => $translator->translate('Experiences'),
                        ),
                        new Node(
                            'sec-special-experiences',
                            $experiences,
                            null
                        )
                    ),
                    new Node(
                        'subsection',
                        array(
                            'title' => $translator->translate('Computer Skills'),
                        ),
                        new Node(
                            'content',
                            null,
                            $cv->getComputerSkills()
                        )
                    ),
                )
            );
        }

        $result[] = new Node(
            'section',
            array(
                'title' => $translator->translate('Thesis'),
            ),
            array(
                new Node(
                    'content',
                    null,
                    $cv->getThesisSummary()
                ),
            )
        );

        $result[] = new Node(
            'section',
            array(
                'title' => $translator->translate('Career'),
            ),
            array(
                new Node(
                    'sec-special-career',
                    array(
                        'InterestHeader'  => $translator->translate('Future Interest'),
                        'EuropeHeader'    => $translator->translate('Mobility in Europe'),
                        'WorldHeader'     => $translator->translate('Mobility in the World'),
                        'InterestContent' => $cv->getFutureInterest(),
                        'EuropeContent'   => $translator->translate($cv->getMobilityEurope()),
                        'WorldContent'    => $translator->translate($cv->getMobilityWorld()),
                        ),
                    null
                    ),

                new Node(
                    'subsection',
                    array(
                        'title' => $translator->translate('Career Expectations'),
                    ),
                    new Node(
                        'content',
                        null,
                        $cv->getCareerExpectations()
                    )
                ),
            )
        );

        $result[] = new Node(
            'section',
            array(
                'title' => $translator->translate('Hobbies'),
            ),
            new Node(
                'content',
                null,
                $cv->getHobbies()
            )
        );

        return $result;
    }

    private static function getAddressArray(Entry $cv)
    {
        $result = array(
            'street'  => $cv->getAddress()->getStreet(),
            'nr'      => $cv->getAddress()->getNumber(),
            'postal'  => $cv->getAddress()->getPostal(),
            'city'    => $cv->getAddress()->getCity(),
            'country' => $cv->getAddress()->getCountry(),
        );
        if (null !== $cv->getAddress()->getMailbox() && '' !== $cv->getAddress()->getMailbox()) {
            $result['bus'] = $cv->getAddress()->getMailbox();
        }

        return $result;
    }
}
