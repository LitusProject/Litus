<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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

namespace BrBundle\Component\Document\Pdf;

use BrBundle\Entity\Cv\Util,
    BrBundle\Entity\Cv\Entry,
    CommonBundle\Component\Util\File\TmpFile,
    CommonBundle\Component\Util\Xml\Generator,
    CommonBundle\Component\Util\Xml\Object,
    CommonBundle\Entity\General\AcademicYear,
    Doctrine\ORM\EntityManager;

/**
 * Generates the CV Book for one academic year.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class CvBook extends \CommonBundle\Component\Document\Generator\Pdf
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $_entityManager;

    /**
     * @var \CommonBundle\Entity\General\AcademicYear
     */
    private $_year;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \CommonBundle\Entity\General\AcademicYear $year The academic year for which to generate the book.
     */
    public function __construct(EntityManager $entityManager, AcademicYear $year, TmpFile $file)
    {
        $filePath = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.pdf_generator_path');

        parent::__construct(
            $entityManager,
            $filePath . '/cv/cvbook.xsl',
            $file->getFilename()
        );

        $this->_entityManager = $entityManager;
        $this->_year = $year;
    }

    protected function generateXml(TmpFile $tmpFile)
    {
        $xml = new Generator($tmpFile);

        // Generate the xml
        $data = Util::getGrouped($this->_entityManager, $this->_year);

        // Process each cv
        $cvs = array();
        foreach ($data as $studyData) {
            foreach ($studyData['entries'] as $entry) {
                $cvs[] = $this->_generateCv($entry);
            }
        }

        $xml->append(
            new Object(
                'cvbook',
                null,
                $cvs
            )
        );
    }

    private function _generateCv(Entry $cv) {

        $picturePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.profile_path');

        return new Object(
            'cv',
            array(
                'id'        => $cv->getId(),
                'firstname' => $cv->getFirstName(),
                'lastname'  => $cv->getLastName(),
                'email'     => $cv->getEmail(),
                'phone'     => $cv->getPhoneNumber(),
                'img'       => $picturePath . '/' . $cv->getAcademic()->getPhotoPath(),
            ),
            $this->_getSections($cv)
        );
    }

    private function _getSections(Entry $cv)
    {
        $result = array();

        // The address
        $result[] = new Object(
            'address',
            $this->_getAddressArray($cv),
            null
        );

        // Studies
        $result[] = new Object(
            'section',
            array(
                'title' => 'Studies',
            ),
            array(
                new Object(
                    'sec-special-studies',
                    null,
                    array(
                        new Object(
                            'study',
                            array(
                                'start'         => (string) $cv->getMasterStart(),
                                'end'           => (string) $cv->getMasterEnd(),
                                'percentage'    => (string) ($cv->getGrade() / 100),
                            ),
                            array(
                                new Object(
                                    'title',
                                    null,
                                    $cv->getStudy()->getFullTitle()
                                )
                            )
                        ),
                        new Object(
                            'study',
                            array(
                                'start'         => (string) $cv->getBachelorStart(),
                                'end'           => (string) $cv->getBachelorEnd(),
                                'percentage'    => (string) ($cv->getPriorGrade() / 100),
                            ),
                            array(
                                new Object(
                                    'title',
                                    null,
                                    $cv->getPriorStudy()
                                )
                            )
                        )
                    )
                )
            )
        );

        // Erasmus
        if ((null !== $cv->getErasmusLocation() && '' !== $cv->getErasmusLocation() )
            || (null !== $cv->getErasmusPeriod() && '' !== $cv->getErasmusPeriod() ))
        {
            $result[] = new Object(
                'section',
                array(
                    'title' => 'Erasmus',
                ),
                array(
                    new Object(
                        'sec-special-erasmus',
                        null,
                        array(
                            new Object(
                                'location',
                                null,
                                $cv->getErasmusLocation()
                            ),
                            new Object(
                                'period',
                                null,
                                $cv->getErasmusPeriod()
                            ),
                        )
                    )
                )
            );
        }

        // Languages
        $result[] = new Object(
            'section',
            array(
                'title' => 'Languages',
            ),
            array(
                new Object(
                    'sec-special-languages',
                    null,
                    $this->_getLanguageArray($cv)
                ),
                new Object(
                    'subsection',
                    array(
                        'title' => 'Additional Info',
                    ),
                    array(
                        new Object(
                            'content',
                            null,
                            $cv->getLanguageExtra()
                        )
                    )
                ),
            )
        );

        // Capabilities
        $result[] = new Object(
            'section',
            array(
                'title' => 'Capabilities',
            ),
            array(
                new Object(
                    'subsection',
                    array(
                        'title' => 'Computer Skills',
                    ),
                    new Object(
                        'content',
                        null,
                        $cv->getComputerSkills()
                    )
                ),
                new Object(
                    'subsection',
                    array(
                        'title' => 'Experiences',
                    ),
                    new Object(
                        'content',
                        null,
                        $cv->getExperiences()
                    )
                )
            )
        );

        // Thesis
        $result[] = new Object(
            'section',
            array(
                'title' => 'Thesis',
            ),
            array(
                new Object(
                    'content',
                    null,
                    $cv->getThesisSummary()
                )
            )
        );

        // Career
        $result[] = new Object(
            'section',
            array(
                'title' => 'Career',
            ),
            array(
                new Object(
                    'subsection',
                    array(
                        'title' => 'Future Interest',
                    ),
                    new Object(
                        'content',
                        null,
                        $cv->getFutureInterest()
                    )
                ),
                new Object(
                    'subsection',
                    array(
                        'title' => 'Mobility in Europe',
                    ),
                    new Object(
                        'content',
                        null,
                        $cv->getMobilityEurope()
                    )
                ),
                new Object(
                    'subsection',
                    array(
                        'title' => 'Mobility in the World',
                    ),
                    new Object(
                        'content',
                        null,
                        $cv->getMobilityWorld()
                    )
                ),
                new Object(
                    'subsection',
                    array(
                        'title' => 'Career Expectations',
                    ),
                    new Object(
                        'content',
                        null,
                        $cv->getCareerExpectations()
                    )
                ),
            )
        );

        // Personal
        $result[] = new Object(
            'section',
            array(
                'title' => 'Personal',
            ),
            array(
                new Object(
                    'subsection',
                    array(
                        'title' => 'Hobbies',
                    ),
                    new Object(
                        'content',
                        null,
                        $cv->getHobbies()
                    )
                ),
                new Object(
                    'subsection',
                    array(
                        'title' => 'About Myself',
                    ),
                    new Object(
                        'content',
                        null,
                        $cv->getAbout()
                    )
                )
            )
        );

        return $result;
    }

    private function _getAddressArray(Entry $cv)
    {
        $result = array(
            'street'    => $cv->getAddress()->getStreet(),
            'nr'        => $cv->getAddress()->getNumber(),
            'postal'    => $cv->getAddress()->getPostal(),
            'city'      => $cv->getAddress()->getCity(),
            'country'   => $cv->getAddress()->getCountry(),
        );
        if (null !== $cv->getAddress()->getMailbox() && '' !== $cv->getAddress()->getMailbox())
            $result['bus'] = $cv->getAddress()->getMailbox();
        return $result;
    }

    private function _getLanguageArray(Entry $cv)
    {
        $languages = array();
        foreach ($cv->getLanguages() as $language) {
            $languages[] = new Object(
                'language',
                array(
                    'name'      => $language->getName(),
                    'oral'      => $language->getOralSkill(),
                    'written'   => $language->getWrittenSkill(),
                ),
                null
            );
        }
        return $languages;
    }
}
