<?php
/**
* 2007-2019 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Clearcachecrontask extends Module
{
    protected $config_form = false;
    protected $support_url = 'https://addons.prestashop.com/contact-form.php?id_product=32440';

    public function __construct()
    {
        $this->name = 'clearcachecrontask';
        $this->tab = 'administration';
        $this->version = '1.1.3';
        $this->author = 'Mathieu Thollet';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->module_key = '20e60c84c41e49d96da3417f5393fe93';
        parent::__construct();

        $this->displayName = $this->l('Clear Cache Cron Task Scheduled');
        $this->description = $this->l('Automatic task to clear your prestashop cache');
        $this->confirmUninstall = $this->l('Are you sure to uninstall this module ?');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        Configuration::updateGlobalValue('CLEARCACHECRONTASK_TOKEN', uniqid().uniqid());
        Configuration::updateGlobalValue('CLEARCACHECRONTASK_FACETED', false);
        Configuration::updateGlobalValue('CLEARCACHECRONTASK_FACETED_PRICES', false);
        return parent::install();
    }

    public function uninstall()
    {
        Configuration::deleteByName('CLEARCACHECRONTASK_TOKEN');
        Configuration::deleteByName('CLEARCACHECRONTASK_FACETED');
        Configuration::deleteByName('CLEARCACHECRONTASK_FACETED_PRICES');
        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $output = '';
        if (((bool)Tools::isSubmit('submitClearCacheCronTaskModule')) == true) {
            $this->postProcess();
            $output = $this->displayConfirmation($this->l('Settings have been updated.'));
        }
        $this->context->smarty->assign('module_dir', $this->_path);
        $this->context->smarty->assign('url', Tools::getProtocol(Tools::usingSecureMode()).$_SERVER['HTTP_HOST'].$this->getPathUri() . 'cron.php');
        $this->context->smarty->assign('token', Configuration::get('CLEARCACHECRONTASK_TOKEN'));
        $this->context->smarty->assign('support_url', $this->support_url);
        return $output .
            $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl') .
            $this->renderForm() .
            $this->context->smarty->fetch($this->local_path.'views/templates/admin/support.tpl');
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

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitClearCacheCronTaskModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array('fields_value' => $this->getConfigFormValues());

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
                        'name' => 'CLEARCACHECRONTASK_FACETED',
                        'label' => version_compare(_PS_VERSION_, '1.7', '<') ? $this->l('Rebuilt Layered Navigation Block module index') : $this->l('Rebuilt Faceted Search module index'),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'faceted_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'faceted_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'name' => 'CLEARCACHECRONTASK_FACETED_PRICES',
                        'label' => version_compare(_PS_VERSION_, '1.7', '<') ? $this->l('Rebuilt Layered Navigation Block module prices index') : $this->l('Rebuilt Faceted Search module prices index'),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'faceted_prices_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'faceted_prices_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
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
            'CLEARCACHECRONTASK_FACETED' => Configuration::get('CLEARCACHECRONTASK_FACETED', false),
            'CLEARCACHECRONTASK_FACETED_PRICES' => Configuration::get('CLEARCACHECRONTASK_FACETED_PRICES', false),
        );
    }


    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();
        foreach (array_keys($form_values) as $key) {
            Configuration::updateGlobalValue($key, Tools::getValue($key));
        }
    }


    /**
     * Checks the cron token
     * @param $token
     * @return bool
     */
    public function checkToken($token)
    {
        return Configuration::get('CLEARCACHECRONTASK_TOKEN') == trim(urldecode($token));
    }


    /**
     * Clear caches
     */
    public function clearCaches($echo = true)
    {
        if (is_callable(array('Tools', 'clearSf2Cache'))) {
            echo $echo ? '<br/>' . $this->l('Clear Symfony Cache') : '';
            Tools::clearSf2Cache();
        }
        echo $echo ? '<br/>' . $this->l('Clear Smarty Cache') : '';
        Tools::clearSmartyCache();
        echo $echo ? '<br/>' . $this->l('Clear XML Cache') : '';
        Tools::clearXMLCache();
        echo $echo ? '<br/>' . $this->l('Clear Media Cache') : '';
        Media::clearCache();
        echo $echo ? '<br/>' . $this->l('Generate Index') : '';
        Tools::generateIndex();
        if (Configuration::get('CLEARCACHECRONTASK_FACETED', false)) {
            if (Module::isInstalled('blocklayered')) {
                include_once(dirname(__FILE__).'/../blocklayered/blocklayered.php');
                echo $echo ? '<br/>' . $this->l('Block Layered attributes index') : '';
                $blockLayered = new BlockLayered();
                $blockLayered->indexAttribute();
                echo $echo ? '<br/>' . $this->l('Block Layered URLs index') : '';
                $blockLayered->indexUrl();
            }
            if (Module::isInstalled('ps_facetedsearch')) {
                include_once(dirname(__FILE__).'/../ps_facetedsearch/ps_facetedsearch.php');
                echo $echo ? '<br/>' . $this->l('Faceted Search attributes index') : '';
                $psFacetedsearch = new Ps_Facetedsearch();
                $psFacetedsearch->indexAttribute();
            }
        }
        if (Configuration::get('CLEARCACHECRONTASK_FACETED_PRICES', false)) {
            if (Module::isInstalled('blocklayered')) {
                include_once(dirname(__FILE__).'/../blocklayered/blocklayered.php');
                echo $echo ? '<br/>' . $this->l('Block Layered full prices index') : '';
                BlockLayered::fullPricesIndexProcess();
            }
            if (Module::isInstalled('ps_facetedsearch')) {
                include_once(dirname(__FILE__).'/../ps_facetedsearch/ps_facetedsearch.php');
                echo $echo ? '<br/>' . $this->l('Faceted Search full prices index') : '';
                Ps_Facetedsearch::fullPricesIndexProcess();
            }
        }
        echo $echo ? '<br/>' . $this->l('Done !') : '';
    }
}
