<?php
/*
* This file is part of the edge package.
*
* (c) Amin Alizade <motammem@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Mtm\PersianDateBundle\Test\Form\Type;


use Mtm\PersianDate\DateTime;
use Mtm\PersianDateBundle\Form\PersianDateType;
use Symfony\Component\Form\Test\TypeTestCase;

class PersianDateTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = array(
            'day' => '19',
            'month' => '9',
            'year' => '1370',
        );

        $type = new PersianDateType();
        $form = $this->factory->create($type);

        $persianDate = new DateTime();
        $persianDate->setDate(1370,9,19);
        $object = $persianDate->getOriginalDateTime();
        $object->setTime(0, 0, 0);

        $timeZone = $object->getTimezone();
        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($object, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
