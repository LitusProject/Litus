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

namespace CommonBundle\Component\Validator;

/**
 * Fixes a deprecation bug in Zend's email address validator.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class EmailAddress extends \Zend\Validator\EmailAddress
{
    /**
     * Safely convert UTF-8 encoded domain name to ASCII
     *
     * @param string $email  the UTF-8 encoded email
     * @return string
     */
    protected function idnToAscii($email)
    {
        if (extension_loaded('intl')) {
            if (defined('INTL_IDNA_VARIANT_UTS46')) {
                return (idn_to_ascii($email, 0, INTL_IDNA_VARIANT_UTS46) ?: $email);
            }
            return (idn_to_ascii($email) ?: $email);
        }
        return $email;
    }

    /**
     * Safely convert ASCII encoded domain name to UTF-8
     *
     * @param string $email the ASCII encoded email
     * @return string
     */
    protected function idnToUtf8($email)
    {
        if (strlen($email) == 0) {
            return $email;
        }

        if (extension_loaded('intl')) {
            // The documentation does not clarify what kind of failure
            // can happen in idn_to_utf8. One can assume if the source
            // is not IDN encoded, it would fail, but it usually returns
            // the source string in those cases.
            // But not when the source string is long enough.
            // Thus we default to source string ourselves.
            if (defined('INTL_IDNA_VARIANT_UTS46')) {
                return idn_to_utf8($email, 0, INTL_IDNA_VARIANT_UTS46) ?: $email;
            }
            return idn_to_utf8($email) ?: $email;
        }
        return $email;
    }
 }
