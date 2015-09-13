<?php
/*
* This file is part of the edge package.
*
* (c) Amin Alizade <motammem@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Mtm\PersianDateBundle\Form\Transformer;


use Mtm\PersianDate\Factory;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\DataTransformer\BaseDateTimeTransformer;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToArrayTransformer;

class DateViewTransformer extends BaseDateTimeTransformer
{
    protected $pad;

    protected $fields;

    /**
     * Constructor.
     *
     * @param string $inputTimezone The input timezone
     * @param string $outputTimezone The output timezone
     * @param array $fields The date fields
     * @param bool $pad Whether to use padding
     *
     * @throws UnexpectedTypeException if a timezone is not a string
     */
    public function __construct($inputTimezone = null, $outputTimezone = null, array $fields = null, $pad = false)
    {
        parent::__construct($inputTimezone, $outputTimezone);

        if (null === $fields) {
            $fields = array('year', 'month', 'day', 'hour', 'minute', 'second');
        }

        $this->fields = $fields;
        $this->pad = (bool)$pad;
    }

    public function transform($dateTime)
    {
        if (null === $dateTime) {
            return array_intersect_key(array(
                'year' => '',
                'month' => '',
                'day' => '',
                'hour' => '',
                'minute' => '',
                'second' => '',
            ), array_flip($this->fields));
        }
        $dateTime = Factory::buildFromOriginalDateTime($dateTime);

        if (!$dateTime instanceof \DateTime && !$dateTime instanceof \DateTimeInterface) {
            throw new TransformationFailedException('Expected a \DateTime or \DateTimeInterface.');
        }

        if ($this->inputTimezone !== $this->outputTimezone) {
            if (!$dateTime instanceof \DateTimeImmutable) {
                $dateTime = clone $dateTime;
            }

            try {
                $dateTime = $dateTime->setTimezone(new \DateTimeZone($this->outputTimezone));
            } catch (\Exception $e) {
                throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
            }
        }

        $result = array_intersect_key(array(
            'year' => $dateTime->format('Y'),
            'month' => $dateTime->format('m'),
            'day' => $dateTime->format('d'),
            'hour' => $dateTime->format('H'),
            'minute' => $dateTime->format('i'),
            'second' => $dateTime->format('s'),
        ), array_flip($this->fields));

        if (!$this->pad) {
            foreach ($result as &$entry) {
                // remove leading zeros
                $entry = (string)(int)$entry;
            }
            // unset reference to keep scope clear
            unset($entry);
        }

        return $result;
    }

    public function reverseTransform($value)
    {
        if (null === $value) {
            return;
        }

        if (!is_array($value)) {
            throw new TransformationFailedException('Expected an array.');
        }

        if ('' === implode('', $value)) {
            return;
        }
        $emptyFields = array();

        foreach ($this->fields as $field) {
            if (!isset($value[$field])) {
                $emptyFields[] = $field;
            }
        }

        if (count($emptyFields) > 0) {
            throw new TransformationFailedException(
                sprintf('The fields "%s" should not be empty', implode('", "', $emptyFields)
                ));
        }

        if (isset($value['month']) && !ctype_digit((string)$value['month'])) {
            throw new TransformationFailedException('This month is invalid');
        }

        if (isset($value['day']) && !ctype_digit((string)$value['day'])) {
            throw new TransformationFailedException('This day is invalid');
        }

        if (isset($value['year']) && !ctype_digit((string)$value['year'])) {
            throw new TransformationFailedException('This year is invalid');
        }
        if (!empty($value['month']) && !empty($value['day']) && !empty($value['year']) && false === checkdate($value['month'], $value['day'], $value['year'])) {
            throw new TransformationFailedException('This is an invalid date');
        }

        try {

            $persianDate = Factory::buildFromExactDate(
                empty($value['hour']) ? '0' : $value['hour'],
                empty($value['minute']) ? '0' : $value['minute'],
                empty($value['second']) ? '0' : $value['second'],
                empty($value['month']) ? '1' : $value['month'],
                empty($value['day']) ? '1' : $value['day'],
                empty($value['year']) ? '1970' : $value['year']
            );

            $persianDate->setTimezone(new \DateTimeZone($this->outputTimezone));
            $dateTime = $persianDate->getOriginalDateTime();

            if ($this->inputTimezone !== $this->outputTimezone) {
                $dateTime->setTimezone(new \DateTimeZone($this->inputTimezone));
            }

        } catch (\Exception $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }

        return $dateTime;


    }

}