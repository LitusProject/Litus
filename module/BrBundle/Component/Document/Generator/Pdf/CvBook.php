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
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Component\Document\Generator\Pdf;

use BrBundle\Entity\Cv\Util,
    BrBundle\Entity\Cv\Entry,
    CommonBundle\Component\Util\File\TmpFile,
    CommonBundle\Component\Util\Xml\Generator,
    CommonBundle\Component\Util\Xml\Object,
    CommonBundle\Entity\General\AcademicYear,
    Doctrine\ORM\EntityManager,
    Zend\Mvc\I18n\Translator;

/**
 * Generates the CV Book for one academic year.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class CvBook extends \CommonBundle\Component\Document\Generator\Pdf
{
    /**
     * @var AcademicYear
     */
    private $_year;

    /**
     * @var Translator
     */
    private $_translator;

    /**
     * @param EntityManager $entityManager The EntityManager instance
     * @param AcademicYear  $year          The academic year for which to generate the book.
     * @param TmpFile       $file          The file to write to
     * @param Translator    $translator    The translator
     */
    public function __construct(EntityManager $entityManager, AcademicYear $year, TmpFile $file, Translator $translator)
    {
        $filePath = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.pdf_generator_path');

        parent::__construct(
            $entityManager,
            $filePath . '/cv/cvbook.xsl',
            $file->getFilename()
        );

        $this->_year = $year;
        $this->_translator = $translator;
    }

    protected function generateXml(TmpFile $tmpFile)
    {
        $xml = new Generator($tmpFile);

        $data = Util::getGrouped($this->getEntityManager(), $this->_year);

        $groups = array();

        foreach ($data as $studyData)
            $groups[] = $this->_generateGroup($studyData['name'], $studyData['entries']);

        $organization_logo = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('organization_logo');

        $foreword = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.cv_book_foreword');

        $xml->append(
            new Object(
                'cvbook',
                array(
                    'logo' => $organization_logo,
                    'index' => $this->_translator->translate('Alphabetical Index'),
                    'toc' => $this->_translator->translate('Table of Contents'),
                ),
                array(
                    new Object(
                        'foreword',
                        array(
                            'title' => $this->_translator->translate('Foreword'),
                        ),
                        Object::fromString($foreword)
                    ),
                    new Object(
                        'cvs',
                        null,
                        $groups
                    )
                )
            )
        );
    }

    private function _generateGroup($groupName, $entries)
    {
        $cvs = array();
        foreach ($entries as $entry)
            $cvs[] = $this->_generateCv($entry);

        return new Object(
            'cvgroup',
            array(
                'name' => $groupName,
            ),
            $cvs
        );
    }

    private function _generateCv(Entry $cv)
    {
        $picturePath = 'public' . $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.profile_path');

        return new Object(
            'cv',
            array(
                'id'        => $cv->getId(),
                'firstname' => $cv->getFirstName(),
                'lastname'  => $cv->getLastName(),
                'birthday'  => $cv->getBirthDay()->format('d/m/Y'),
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

        $result[] = new Object(
            'address',
            $this->_getAddressArray($cv),
            null
        );

        $result[] = new Object(
            'section',
            array(
                'title' => $this->_translator->translate('Studies'),
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

        if (
            (null !== $cv->getErasmusLocation() && '' !== $cv->getErasmusLocation())
            || (null !== $cv->getErasmusPeriod() && '' !== $cv->getErasmusPeriod())
        ) {
            $result[] = new Object(
                'section',
                array(
                    'title' => $this->_translator->translate('Erasmus'),
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

        $result[] = new Object(
            'section',
            array(
                'title' => $this->_translator->translate('Languages'),
            ),
            array(
                new Object(
                    'sec-special-languages',
                    array(
                        'oral' => $this->_translator->translate('Oral Skills'),
                        'written' => $this->_translator->translate('Written Skills'),
                    ),
                    $this->_getLanguageArray($cv)
                ),
                new Object(
                    'subsection',
                    array(
                        'title' => $this->_translator->translate('Additional Info'),
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

        $result[] = new Object(
            'section',
            array(
                'title' => $this->_translator->translate('Capabilities'),
            ),
            array(
                new Object(
                    'subsection',
                    array(
                        'title' => $this->_translator->translate('Computer Skills'),
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
                        'title' => $this->_translator->translate('Experiences'),
                    ),
                    new Object(
                        'content',
                        null,
                        $cv->getExperiences()
                    )
                )
            )
        );

        $result[] = new Object(
            'section',
            array(
                'title' => $this->_translator->translate('Thesis'),
            ),
            array(
                new Object(
                    'content',
                    null,
                    $cv->getThesisSummary()
                )
            )
        );

        $result[] = new Object(
            'section',
            array(
                'title' => $this->_translator->translate('Career'),
            ),
            array(
                new Object(
                    'subsection',
                    array(
                        'title' => $this->_translator->translate('Future Interest'),
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
                        'title' => $this->_translator->translate('Mobility in Europe'),
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
                        'title' => $this->_translator->translate('Mobility in the World'),
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
                        'title' => $this->_translator->translate('Career Expectations'),
                    ),
                    new Object(
                        'content',
                        null,
                        $cv->getCareerExpectations()
                    )
                ),
            )
        );

        $result[] = new Object(
            'section',
            array(
                'title' => $this->_translator->translate('Personal'),
            ),
            array(
                new Object(
                    'subsection',
                    array(
                        'title' => $this->_translator->translate('Hobbies'),
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
                        'title' => $this->_translator->translate('About Myself'),
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
                    'oral'      => $this->_translator->translate($language->getOralSkill()),
                    'written'   => $this->_translator->translate($language->getWrittenSkill()),
                ),
                null
            );
        }

        return $languages;
    }
}
