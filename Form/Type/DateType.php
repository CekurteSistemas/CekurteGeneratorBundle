<?php

namespace Cekurte\GeneratorBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Form date type.
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 2.0
 */
class DateType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'widget' => 'single_text',
            'format' => $this->getDatePattern(),
            'attr'   => array(
                'class'              => 'datepicker',
                'data-date-format'   => $this->getDateWidgetPattern(),
                'data-date-language' => self::getCurrentLocale(),
            ),
        ));
    }

    /**
     * Get a date field pattern
     *
     * @param string|null $locale
     * @return string
     */
    protected function getDatePattern($locale = null)
    {
        return self::getFormatter()->getPattern();
    }

    /**
     * Get a date field pattern
     *
     * @param string|null $locale
     * @return string
     */
    protected function getDateWidgetPattern($locale = null)
    {
        return strtolower($this->getDatePattern($locale));
    }

    /**
     * Get a instance of IntlDateFormatter
     *
     * @static
     *
     * @param string|null $locale
     * @return \IntlDateFormatter
     */
    public static function getFormatter($locale = null)
    {
        return new \IntlDateFormatter(
            is_null($locale) ? \Locale::getDefault() : $locale,
            self::getFormatterDateType(),
            self::getFormatterTimeType()
        );
    }

    /**
     * Get a default date formatter
     *
     * @static
     *
     * @return string
     */
    public static function getFormatterDateType()
    {
        return \IntlDateFormatter::SHORT;
    }

    /**
     * Get a default time formatter
     *
     * @static
     *
     * @return string
     */
    public static function getFormatterTimeType()
    {
        return \IntlDateFormatter::NONE;
    }

    /**
     * Get the current locale language
     *
     * @param string|null $locale
     * @return string
     */
    protected function getCurrentLocale($locale = null)
    {
        return str_replace('_', '-', is_null($locale) ? \Locale::getDefault() : $locale);
    }

    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return 'date';
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'ck_date';
    }
}
