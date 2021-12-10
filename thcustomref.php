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

if (!defined('_PS_VERSION_')) {
    exit;
}

class Thcustomref extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'thcustomref';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Presta Maniacs';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Custom Order Reference');
        $this->description = $this->l('Fully customizable module for your order reference.');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        if (!parent::install() || !$this->registerHooks()) {
            return false;
        }

        return  true;
    }

    public function registerHooks()
    {
        if (!$this->registerHook('actionAdminControllerSetMedia')) {
            return false;
        }
        return true;
    }

    public function uninstall()
    {
        $form_values = $this->getConfigFormValues();
        foreach (array_keys($form_values) as $key) {
            Configuration::deleteByName($key);
        }

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $message = '';
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitThcustomrefModule')) == true) {
            $this->postProcess();
            if (count($this->_errors)) {
                $message = $this->displayError($this->_errors);
            } else {
                $message = $this->displayConfirmation($this->l('Successfully saved!'));
            }
        }

        $results = $this->getMaxIdOrder(true);

        $value = '';
        if (Configuration::get('THCUSTOMREF_PREFIX_ENABLE')) {
            $value .= Configuration::get('THCUSTOMREF_PREFIX');
        }
        $value .= sprintf('%0'.Configuration::get('THCUSTOMREF_DIGITS_NUMBER').'d', $results);
        if (Configuration::get('THCUSTOMREF_SUFFIX_ENABLE')) {
            $value .= Configuration::get('THCUSTOMREF_SUFFIX');
        }

        $output1 = $this->renderForm();

        $this->context->smarty->assign(array(
            'module_dir' => $this->_path,
            'THCUSTOMREF_DEFAULT' => $value,
            'renderForm' => $output1
        ));
        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');
        return $message.$output;
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitThcustomrefModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable module'),
                        'name' => 'THCUSTOMREF_LIVE_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'th_title',
                        'name' => 'Reference customization',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Number of digits'),
                        'name' => 'THCUSTOMREF_DIGITS_NUMBER',
                        'required' => true,
                        'col' => 2,
                        'class' => 'fixed-width-xl'
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Number to use'),
                        'name' => 'THCUSTOMREF_NUMBER_TO_USE',
                        'options' => array(
                            'query' => array(
                                array(
                                    'option_value' => 0,
                                    'option_title' => $this->l('ID Order')
                                ),
                                array(
                                    'option_value' => 1,
                                    'option_title' => $this->l('Specified number')
                                )
                            ),
                            'id' => 'option_value',
                            'name' => 'option_title'
                        )
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Next increment number'),
                        'name' => 'THCUSTOMREF_NEXT_INCREMENT_NUMBER',
                        'col' => 2,
                        'class' => 'fixed-width-xl',
                        'required' => true
                    ),
                    array(
                        'type' => 'th_sub_title',
                        'name' => 'Prefix',
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable prefix'),
                        'name' => 'THCUSTOMREF_PREFIX_ENABLE',
                        'is_bool' => true,
                        'desc' => $this->l('Activate or deactivate prefix'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Prefix'),
                        'name' => 'THCUSTOMREF_PREFIX',
                        'col' => 2,
                        'class' => 'fixed-width-xl'
                    ),
                    array(
                        'type' => 'th_sub_title',
                        'name' => 'Suffix',
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable suffix'),
                        'name' => 'THCUSTOMREF_SUFFIX_ENABLE',
                        'is_bool' => true,
                        'desc' => $this->l('Activate or deactivate suffix'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Suffix'),
                        'name' => 'THCUSTOMREF_SUFFIX',
                        'col' => 2,
                        'class' => 'fixed-width-xl'
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'THCUSTOMREF_LIVE_MODE' => Tools::getValue('THCUSTOMREF_LIVE_MODE', Configuration::get('THCUSTOMREF_LIVE_MODE')),
            'THCUSTOMREF_PREFIX' => Tools::getValue('THCUSTOMREF_PREFIX', Configuration::get('THCUSTOMREF_PREFIX')),
            'THCUSTOMREF_PREFIX_ENABLE' => Tools::getValue('THCUSTOMREF_PREFIX_ENABLE', Configuration::get('THCUSTOMREF_PREFIX_ENABLE')),
            'THCUSTOMREF_SUFFIX' => Tools::getValue('THCUSTOMREF_SUFFIX', Configuration::get('THCUSTOMREF_SUFFIX')),
            'THCUSTOMREF_SUFFIX_ENABLE' => Tools::getValue('THCUSTOMREF_SUFFIX_ENABLE', Configuration::get('THCUSTOMREF_SUFFIX_ENABLE')),
            'THCUSTOMREF_DIGITS_NUMBER' => Tools::getValue('THCUSTOMREF_DIGITS_NUMBER', Configuration::get('THCUSTOMREF_DIGITS_NUMBER')),
            'THCUSTOMREF_NEXT_INCREMENT_NUMBER' => Tools::getValue('THCUSTOMREF_NEXT_INCREMENT_NUMBER', Configuration::get('THCUSTOMREF_NEXT_INCREMENT_NUMBER')),
            'THCUSTOMREF_NUMBER_TO_USE' => Tools::getValue('THCUSTOMREF_NUMBER_TO_USE', Configuration::get('THCUSTOMREF_NUMBER_TO_USE')),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $update_value = 1;
        $form_values = $this->getConfigFormValues();

        $count = 0;

        $results = Tools::getValue('THCUSTOMREF_NUMBER_TO_USE') == 0 ? $this->getMaxIdOrder() : Configuration::get('THCUSTOMREF_NEXT_INCREMENT_NUMBER_HIDDEN');

        if (Tools::getValue('THCUSTOMREF_PREFIX_ENABLE')) {
            $count += Tools::strlen(Tools::getValue('THCUSTOMREF_PREFIX'));
        }

        if (Tools::getValue('THCUSTOMREF_SUFFIX_ENABLE')) {
            $count += Tools::strlen(Tools::getValue('THCUSTOMREF_SUFFIX'));
        }

        if (!Validate::isInt(Tools::getValue('THCUSTOMREF_DIGITS_NUMBER')) || (Tools::getValue('THCUSTOMREF_DIGITS_NUMBER') < Tools::strlen($results))) {
            $this->_errors[] = 'Number of digits value it\'s not ok!';
        }

        if (!$this->_errors) {
            if ($count + Tools::getValue('THCUSTOMREF_DIGITS_NUMBER') > 9) {
                $this->_errors[] = 'Prefix ('.Tools::strlen(Tools::getValue('THCUSTOMREF_PREFIX')).') + suffix ('.Tools::strlen(Tools::getValue('THCUSTOMREF_SUFFIX')).') + number of digits ('.Tools::getValue('THCUSTOMREF_DIGITS_NUMBER').') cannot be grather then 9';
            }
        }
        // de facut update in timp real la next increment number
        // de verificat next increment number cand este null si exista hidden

        if (!$this->_errors) {
            foreach (array_keys($form_values) as $key) {
                if ($key == 'THCUSTOMREF_DIGITS_NUMBER') {
                    if (!Validate::isInt(Tools::getValue($key)) || Tools::getValue($key) > 5) {
                        $this->_errors[] = 'Number of digits value it\'s not ok';
                        $update_value = 0;
                    }
                } elseif ($key == 'THCUSTOMREF_SUFFIX') {
                    if (empty(Tools::getValue($key)) && Tools::getValue('THCUSTOMREF_SUFFIX_ENABLE')) {
                        $this->_errors[] = 'Suffix cannot be empty while it is enabled';
                        $update_value = 0;
                    }
                } elseif ($key == 'THCUSTOMREF_PREFIX') {
                    if (empty(Tools::getValue($key)) && Tools::getValue('THCUSTOMREF_PREFIX_ENABLE')) {
                        $this->_errors[] = 'Prefix cannot be empty while it is enabled';
                        $update_value = 0;
                    }
                } else if ($key == 'THCUSTOMREF_NUMBER_TO_USE' && Tools::getValue('THCUSTOMREF_NUMBER_TO_USE') == 1) {
                    if (!Validate::isInt(Tools::getValue('THCUSTOMREF_NEXT_INCREMENT_NUMBER')) || Tools::getValue('THCUSTOMREF_NEXT_INCREMENT_NUMBER') <= Configuration::get('THCUSTOMREF_NEXT_INCREMENT_NUMBER_HIDDEN')) {
                        $this->_errors[] = 'Next increment number value can\'t be lower or equal than '.Configuration::get('THCUSTOMREF_NEXT_INCREMENT_NUMBER_HIDDEN');
                        $update_value = 0;
                    }
                }

                if ($update_value) {
                    if ($key == 'THCUSTOMREF_NUMBER_TO_USE' && Tools::getValue('THCUSTOMREF_NUMBER_TO_USE') == 1) {
                        Configuration::updateValue('THCUSTOMREF_NEXT_INCREMENT_NUMBER_HIDDEN', Tools::getValue('THCUSTOMREF_NEXT_INCREMENT_NUMBER'));
                    }
                    Configuration::updateValue($key, Tools::getValue($key));
                }
            }
        }
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookActionAdminControllerSetMedia()
    {
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJQuery();
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');

            $results = $this->getMaxIdOrder(true);

            Media::addJsDef(array(
                'THCUSTOMREF_LAST_ID_ORDER' => $results
            ));
        }
    }

    public function getMaxIdOrder($increment = false)
    {
        $sql = 'SELECT MAX(`id_order`) FROM `'._DB_PREFIX_.'orders`' ;
        if ($increment) {
            return Db::getInstance()->getValue($sql) + 1;
        }

        return Db::getInstance()->getValue($sql);
    }

    public function setAutoIncrement($max_id)
    {
        $sql = 'ALTER TABLE `'._DB_PREFIX_.'orders` MODIFY `id_order` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT='.$max_id;
        return Db::getInstance()->execute($sql);
    }
}
