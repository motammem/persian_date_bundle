<?php
/*
* This file is part of the edge package.
*
* (c) Amin Alizade <motammem@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Mtm\PersianDateBundle\Twig;


use Mtm\PersianDate\Factory;

class PersianDateExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('persian_date', array($this, 'persianDateFormat')),
        );
    }

    public function persianDateFormat(\DateTime $dateTime, $format)
    {
        $persianDate = Factory::buildFromOriginalDateTime($dateTime);

        return $persianDate->format($format);
    }

    public function getName()
    {
        return 'mtm_persian_date_twig_extension';
    }

}