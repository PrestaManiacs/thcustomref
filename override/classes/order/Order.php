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
        $output = '';
        $thcustomref = 'thcustomref';
        if (Module::isInstalled($thcustomref) && Module::isEnabled($thcustomref)) {
            if (Configuration::get('THCUSTOMREF_LIVE_MODE')) {
                if (Configuration::get('THCUSTOMREF_PREFIX_ENABLE')) {
                    $output .= Configuration::get('THCUSTOMREF_PREFIX');
                }

                $module = Module::getInstanceByName($thcustomref);
                $max_id = $module->getMaxIdOrder(true);

                if ($increment_number = Configuration::get('THCUSTOMREF_NEXT_INCREMENT_NUMBER')) {
                    if ($increment_number > $max_id) {
                        $max_id = $increment_number;

                        if ($module->setAutoIncrement($max_id)) {
                            Configuration::updateValue('THCUSTOMREF_NEXT_INCREMENT_NUMBER', null);
                        }
                    }
                }

                $output .= sprintf('%0' . Configuration::get('THCUSTOMREF_DIGITS_NUMBER') . 'd', $max_id);

                if (Configuration::get('THCUSTOMREF_SUFFIX_ENABLE')) {
                    $output .= Configuration::get('THCUSTOMREF_SUFFIX');
                }

                return $output;
            }
        }

        return parent::generateReference();
    }
}
