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

namespace BrBundle\Entity\Invoice;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Invoice\ManualInvoice")
 * @ORM\Table(name="br.invoices_manual")
 */
class ManualInvoice extends \BrBundle\Entity\Invoice
{
    public function hasContract()
    {
        return false;
    }

    public function getCompany()
    {
        return null;
    }

    /**
     * @return DateTime
     */
    public function getExpirationTime()
    {
        $expireTime = 'P' . $this->getOrder()->getContract()->getPaymentDays() . 'D';

        return $this->getCreationTime()->add(new DateInterval($expireTime));
    }
}
