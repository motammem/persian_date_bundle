<?php

namespace Mtm\PersianDateBundle\Form;

use Mtm\PersianDate\DateTime;
use Mtm\PersianDateBundle\Form\Transformer\DateViewTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PersianDateType extends DateType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->resetViewTransformers();
        $builder->remove('month');
        $builder->add('month', 'choice', array(
            'choices' => $this->getMonthChoices()
        ));
        $builder->addViewTransformer(new DateViewTransformer());

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array(
            'years' => $this->getYearChoices(),
        ));
    }


    public function getName()
    {
        return 'mtm_persian_date_type';
    }


    private function getMonthChoices()
    {
        return array_combine(range(1, 12), array(
            'فروردین',
            'اردیبهشت',
            'خرداد',
            'تیر',
            'مرداد',
            'شهریور',
            'مهر',
            'آبان',
            'آذر',
            'دی',
            'بهمن',
            'اسفند',
        ));
    }

    private function getYearChoices()
    {
        $date = new DateTime();
        $currentYear = (int)$date->format("Y");
        $years = range($currentYear - 50, $currentYear + 50);
        return array_combine($years, $years);
    }

}
