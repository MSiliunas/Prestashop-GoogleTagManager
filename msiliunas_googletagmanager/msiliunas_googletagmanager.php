<?php
if (!defined('_PS_VERSION_')) {
    exit;
}


class MSiliunas_GoogleTagManager extends Module
{
    const ID_KEY = 'MSILIUNAS_GOOGLETAGMANAGER_ID';

    public function __construct()
    {
        $this->name = 'msiliunas_googletagmanager';
        $this->version = '1.0.0';
        $this->tab = 'analytics';
        $this->author = 'Marijus Siliunas';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Google Tag Manager');
        $this->description = $this->l('Integrates Google tag manager.');
        $this->ps_versions_compliancy = array('min' => '1.7.1.0', 'max' => _PS_VERSION_);

        if (!Configuration::get(self::ID_KEY)) {
            $this->warning = $this->l('No id provided.');
        }
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        return (parent::install() &&
            $this->registerHook('displayHeader') &&
            $this->registerHook('displayAfterBodyOpeningTag') &&
            Configuration::updateValue(self::ID_KEY, ''));
    }

    public function uninstall()
    {
        return parent::uninstall() && Configuration::deleteByName(self::ID_KEY);
    }

    public function hookDisplayHeader($params)
    {
        $this->context->smarty->assign([
            'gtm_id' => Configuration::get(self::ID_KEY),
        ]);
        return $this->display(__FILE__, 'header.tpl');
    }

    public function hookDisplayAfterBodyOpeningTag($params)
    {
        $this->context->smarty->assign([
            'gtm_id' => Configuration::get(self::ID_KEY),
        ]);
        return $this->display(__FILE__, 'body.tpl');
    }


    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('submit' . $this->name)) {
            $gtmId = strval(Tools::getValue(self::ID_KEY));

            if (!$gtmId || empty($gtmId)) {
                $output .= $this->displayError($this->l('No id provided.'));
            } else {
                Configuration::updateValue(self::ID_KEY, $gtmId);
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }

        return $output . $this->displayForm();
    }

    public function displayForm()
    {

        // Get default language
        $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');

        // Init Fields form array
        $fieldsForm[0]['form'] = [
            'legend' => [
                'title' => $this->l('Settings'),
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Google Tag Manager ID'),
                    'name' => self::ID_KEY,
                    'size' => 20,
                    'required' => true
                ]
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            ]
        ];

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        // Language
        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = [
            'save' => [
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name .
                    '&token=' . Tools::getAdminTokenLite('AdminModules'),
            ],
            'back' => [
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            ]
        ];

        // Load current value
        $helper->fields_value[self::ID_KEY] = Configuration::get(self::ID_KEY);

        return $helper->generateForm($fieldsForm);
    }
}
    
    