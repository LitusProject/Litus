<?php

namespace BrBundle\Hydrator\Cv;

use BrBundle\Entity\Cv\Experience as CvExperienceEntity;
use BrBundle\Entity\Cv\Language as CvLanguageEntity;
use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;
use CommonBundle\Entity\General\Address as AddressEntity;

/**
 * This hydrator hydrates/extracts Cv entry data.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Entry extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            throw new InvalidObjectException('Cannot create an entry');
        }

        $person = $object->getAcademic();

        $object->setFirstName($person->getFirstName())
            ->setLastName($person->getLastName())
            ->setBirthday($person->getBirthDay())
            ->setSex($person->getSex())
            ->setPhoneNumber($person->getPhoneNumber())
            ->setEmail($data['personal']['email'])
            ->setPriorStudy($data['studies']['prior_degree'])
            ->setPriorGrade($data['studies']['prior_grade'] * 100)
            ->setStudy(
                $this->getEntityManager()
                    ->getRepository('SyllabusBundle\Entity\Study')
                    ->findOneById($data['studies']['degree'])
            )
            ->setGrade($data['studies']['grade'] * 100)
            ->setBachelorStart($data['studies']['bachelor_start'])
            ->setBachelorEnd($data['studies']['bachelor_end'])
            ->setMasterStart($data['studies']['master_start'])
            ->setMasterEnd($data['studies']['master_end'])
            ->setAdditionalDiplomas($data['studies']['additional_diplomas'])
            ->setErasmusPeriod($data['erasmus']['period'])
            ->setErasmusLocation($data['erasmus']['location'])
            ->setLanguageExtra($data['languages_extra']['extra'])
            ->setComputerSkills($data['capabilities']['computer_skills'])
            ->setThesisSummary($data['thesis']['summary'])
            ->setMobilityEurope($data['future']['mobility_europe'])
            ->setMobilityWorld($data['future']['mobility_world'])
            ->setHobbies($data['profile']['hobbies'])
            ->setAbout($data['profile']['about']);

        $address = $object->getAddress();
        if ($address === null) {
            $address = new AddressEntity();
            $object->setAddress($address);
        }

        $address->setStreet($person->getSecondaryAddress()->getStreet())
            ->setNumber($person->getSecondaryAddress()->getNumber())
            ->setMailbox($person->getSecondaryAddress()->getMailbox())
            ->setPostal($person->getSecondaryAddress()->getPostal())
            ->setCity($person->getSecondaryAddress()->getCity())
            ->setCountry($person->getSecondaryAddress()->getCountryCode());

        $languages = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Cv\Language')
            ->findByEntry($object);

        foreach ($languages as $language) {
            $this->getEntityManager()->remove($language);
        }
        $this->getEntityManager()->flush();

        foreach ($data['languages'] as $languageData) {
            if (!isset($languageData['language_name']) || $languageData['language_name'] === '') {
                continue;
            }

            $language = new CvLanguageEntity(
                $object,
                $languageData['language_name'],
                $languageData['language_written'],
                $languageData['language_oral']
            );

            $this->getEntityManager()->persist($language);
        }

        $experiences = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Cv\Experience')
            ->findByEntry($object);

        foreach ($experiences as $experience) {
            $this->getEntityManager()->remove($experience);
        }
        $this->getEntityManager()->flush();

        foreach ($data['capabilities']['experiences'] as $experienceData) {
            if (!isset($experienceData['experience_function']) || $experienceData['experience_function'] === '') {
                continue;
            }

            $experience = new CvExperienceEntity(
                $object,
                $experienceData['experience_function'],
                $experienceData['experience_type'],
                $experienceData['experience_start'],
                $experienceData['experience_end']
            );

            $this->getEntityManager()->persist($experience);
        }

        return $object;
    }

    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = array();

        $data['personal']['email'] = $object->getEmail();

        $data['studies']['prior_degree'] = $object->getPriorStudy();
        $data['studies']['prior_grade'] = $object->getPriorGrade() / 100;
        $data['studies']['degree'] = $object->getStudy()->getId();
        $data['studies']['grade'] = $object->getGrade() / 100;
        $data['studies']['bachelor_start'] = $object->getBachelorStart();
        $data['studies']['bachelor_end'] = $object->getBachelorEnd();
        $data['studies']['master_start'] = $object->getMasterStart();
        $data['studies']['master_end'] = $object->getMasterEnd();
        $data['studies']['additional_diplomas'] = $object->getAdditionalDiplomas();

        $data['erasmus']['period'] = $object->getErasmusPeriod();
        $data['erasmus']['location'] = $object->getErasmusLocation();

        $data['languages_extra']['extra'] = $object->getLanguageExtra();
        foreach ($object->getLanguages() as $language) {
            $data['languages'][] = array(
                'language_name'    => $language->getName(),
                'language_oral'    => $language->getOralSkillCode(),
                'language_written' => $language->getWrittenSkillCode(),
            );
        }

        foreach ($object->getExperiences() as $experience) {
            $data['capabilities']['experiences'][] = array(
                'experience_type'     => $experience->getType(),
                'experience_function' => $experience->getFunction(),
                'experience_start'    => $experience->getStartYear(),
                'experience_end'      => $experience->getEndYear(),
            );
        }

        $data['capabilities']['computer_skills'] = $object->getComputerSkills();

        $data['thesis']['summary'] = $object->getThesisSummary();

        $data['future']['mobility_europe'] = $object->getMobilityEurope();
        $data['future']['mobility_world'] = $object->getMobilityWorld();

        $data['profile']['hobbies'] = $object->getHobbies();
        $data['profile']['about'] = $object->getAbout();

        return $data;
    }
}
