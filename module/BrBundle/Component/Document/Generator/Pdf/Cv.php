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

use BrBundle\Entity\Cv\Entry,
    BrBundle\Entity\Cv\Util,
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
class Cv extends \CommonBundle\Component\Document\Generator\Pdf
{
    /**
     * @var Entry
     */
    private $entry;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * @param EntityManager $entityManager The EntityManager instance
     * @param AcademicYear  $year          The academic year for which to generate the book.
     * @param Academic      $academic      The academic for which to generate the book.
     * @param TmpFile       $file          The file to write to
     * @param Translator    $translator    The translator
     */
    public function __construct(EntityManager $entityManager, AcademicYear $year, Entry $entry, TmpFile $file, Translator $translator)
    {
        $filePath = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.pdf_generator_path');

        parent::__construct(
            $entityManager,
            $filePath . '/cv/singlecv.xsl',
            $file->getFilename()
        );

        $this->entry = $entry;

        $this->translator = $translator;
    }

    protected function generateXml(TmpFile $tmpFile)
    {
        $xml = new Generator($tmpFile);

        $xml->append(
            $this->generateCv($this->entry)
        );
    }

    private function generateCv(Entry $cv)
    {
        $picturePath = 'public' . $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.profile_path');
        $phoneNumber = $cv->getPhoneNumber();

        $monthsEnglish = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' );
        $monthsDutch = array('Januari', 'Februari', 'Maart', 'April', 'Mei', 'Juni', 'Juli', 'Augustus', 'September', 'Oktober', 'November', 'December' );
        $birthday = $cv->getBirthDay()->format('d F Y');
        $birthday = str_ireplace($monthsEnglish, $monthsDutch, $birthday);

        return new Object(
            'singlecv',
            null,
            new Object(
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
                $this->getSections($cv)
            )
        );
    }

    private function getSections(Entry $cv)
    {
        $result = array();

        $result[] = new Object(
            'address',
            $this->getAddressArray($cv),
            null
        );

        $result[] = new Object(
            'section',
            array(
                'title' => $this->translator->translate('Studies'),
            ),
            array(
                new Object(
                    'sec-special-studies',
                    array(
                        'start_master'          => (string) $cv->getMasterStart(),
                        'end_master'            => (string) $cv->getMasterEnd(),
                        'percentage_master'     => (string) ($cv->getGrade() / 100),
                        'title_master'          => $cv->getStudy()->getTitle(),
                        'start_bach'            => (string) $cv->getBachelorStart(),
                        'end_bach'              => (string) $cv->getBachelorEnd(),
                        'percentage_bach'       => (string) ($cv->getPriorGrade() / 100),
                        'title_bach'            => $cv->getPriorStudy(),
                    ),
                    null
                ),
            )
        );

        if (
            (null !== $cv->getErasmusLocation() && '' !== $cv->getErasmusLocation())
            || (null !== $cv->getErasmusPeriod() && '' !== $cv->getErasmusPeriod())
        ) {
            $result[] = new Object(
                'section',
                array(
                    'title' => $this->translator->translate('Erasmus'),
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
                    ),
                )
            );
        }

        $index = 1;
        $languages = array();
        foreach ($cv->getLanguages() as $language) {
            $tmp = array(
                'name' . $index      => $language->getName(),
                'oral' . $index      => $this->translator->translate($language->getOralSkill()),
                'written' . $index   => $this->translator->translate($language->getWrittenSkill()),
            );
            $languages = array_merge($languages,$tmp);
            $index++;
        }

        $result[] = new Object(
            'section',
            array(
                'title' => $this->translator->translate('Languages'),
            ),
            array(
                new Object(
                    'sec-special-languages',
                    array_merge(
                        array(
                        'oral' => $this->translator->translate('Oral Skills'),
                        'written' => $this->translator->translate('Written Skills'),
                        ),
                        $languages
                    ),
                    null
                ),
                new Object(
                    'subsection',
                    array(
                        'title' => $this->translator->translate('Additional Info'),
                    ),
                    array(
                        new Object(
                            'content',
                            null,
                            $cv->getLanguageExtra()
                        ),
                    )
                ),
            )
        );

        if ($cv->hasOldExperiences()) {
            $result[] = new Object(
                'section',
                array(
                    'title' => $this->translator->translate('Capabilities'),
                ),
                array(
                    new Object(
                        'subsection',
                        array(
                            'title' => $this->translator->translate('Computer Skills'),
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
                            'title' => $this->translator->translate('Experiences'),
                        ),
                        new Object(
                            'content',
                            null,
                            $cv->getExperiences()
                        )
                    ),
                )
            );
        } else {
            $result[] = new Object(
                'section',
                array(
                    'title' => $this->translator->translate('Capabilities'),
                ),
                array(
                    new Object(
                        'subsection',
                        array(
                            'title' => $this->translator->translate('Computer Skills'),
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
                            'title' => $this->translator->translate('Experiences'),
                        ),
                        new Object(
                            'content',
                            null,
                            null
                        )
                    ),
                )
            );
        }

        $result[] = new Object(
            'section',
            array(
                'title' => $this->translator->translate('Thesis'),
            ),
            array(
                new Object(
                    'content',
                    null,
                    $cv->getThesisSummary()
                ),
            )
        );

        $result[] = new Object(
            'section',
            array(
                'title' => $this->translator->translate('Career'),
            ),
            array(
                new Object(
                    'sec-special-career',
                    array(
                        'InterestHeader' => $this->translator->translate('Future Interest'),
                        'EuropeHeader' => $this->translator->translate('Mobility in Europe'),
                        'WorldHeader' => $this->translator->translate('Mobility in the World'),
                        'InterestContent' => $cv->getFutureInterest(),
                        'EuropeContent' => $cv->getMobilityEurope(),
                        'WorldContent' => $cv->getMobilityWorld(),
                        ),
                    null
                    ),

                new Object(
                    'subsection',
                    array(
                        'title' => $this->translator->translate('Career Expectations'),
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
                'title' => $this->translator->translate('Personal'),
            ),
            array(
                new Object(
                    'subsection',
                    array(
                        'title' => $this->translator->translate('Hobbies'),
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
                        'title' => $this->translator->translate('About Myself'),
                    ),
                    new Object(
                        'content',
                        null,
                        $cv->getAbout()
                    )
                ),
            )
        );

        return $result;
    }

    private function getAddressArray(Entry $cv)
    {
        $result = array(
            'street'    => $cv->getAddress()->getStreet(),
            'nr'        => $cv->getAddress()->getNumber(),
            'postal'    => $cv->getAddress()->getPostal(),
            'city'      => $cv->getAddress()->getCity(),
            'country'   => $cv->getAddress()->getCountry(),
        );
        if (null !== $cv->getAddress()->getMailbox() && '' !== $cv->getAddress()->getMailbox()) {
            $result['bus'] = $cv->getAddress()->getMailbox();
        }

        return $result;
    }
}
