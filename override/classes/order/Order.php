<?php
/**
 * 2006-2021 THECON SRL
 *
 * NOTICE OF LICENSE
 *
 * DISCLAIMER
 *
 * YOU ARE NOT ALLOWED TO REDISTRIBUTE OR RESELL THIS FILE OR ANY OTHER FILE
 * USED BY THIS MODULE.
 *
 * @author    THECON SRL <contact@thecon.ro>
 * @copyright 2006-2021 THECON SRL
 * @license   Commercial
 */

class Order extends OrderCore
{
    public static function generateReference()
    {
        $thcustomref = 'thcustomref';
        if (Module::isInstalled($thcustomref) && Module::isEnabled($thcustomref)) {
            $output = '';
            
            if (Configuration::get('THCUSTOMREF_LIVE_MODE')) {
                if (Configuration::get('THCUSTOMREF_PREFIX_ENABLE')) {
                    $output .= Configuration::get('THCUSTOMREF_PREFIX');
                }

                $module = Module::getInstanceByName($thcustomref);
                $number = $module->getMaxIdOrder(true);

                if (Configuration::get('THCUSTOMREF_NUMBER_TO_USE')) {
                    $number_hidden = Configuration::get('THCUSTOMREF_NEXT_INCREMENT_NUMBER_HIDDEN');
                    if ($number_hidden > $number) {
                        $number = $number_hidden;
                        Configuration::updateValue('THCUSTOMREF_NEXT_INCREMENT_NUMBER_HIDDEN', $number_hidden + 1);
                    }
                }

                if (Configuration::get('THCUSTOMREF_NEXT_INCREMENT_NUMBER')) {
                    Configuration::updateValue('THCUSTOMREF_NEXT_INCREMENT_NUMBER', null);
                }

                $output .= sprintf('%0' . Configuration::get('THCUSTOMREF_DIGITS_NUMBER') . 'd', $number);

                if (Configuration::get('THCUSTOMREF_SUFFIX_ENABLE')) {
                    $output .= Configuration::get('THCUSTOMREF_SUFFIX');
                }

                return $output;
            }
        }

        return parent::generateReference();
    }
}
