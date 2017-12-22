<?php
/**
* 2016 WebDevOverture
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@webdevoverture.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade WebDevOverture to newer
* versions in the future. If you wish to customize WebDevOverture for your
* needs please refer to http://www.webdevoverture.com for more information.
*
*  @author    WebDevOverture <contact@webdevoverture.com>
*  @copyright 2016 WebDevOverture
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of WebDevOverture
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__).'/classes/FeatureCategory.php';

class ProductFeaturesCategories extends Module
{
    protected $html;

    public function __construct()
    {
        $this->name = 'productfeaturescategories';
        $this->tab = 'administration';
        $this->version = '1.0.7';
        $this->author = 'WebDevOverture';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->module_key = '49bcce601ef67d24e37ad197b598fc89';
        parent::__construct();
        $this->displayName = $this->l('Product Feature Categories');
        $this->description = $this->l(
            'Organize catalog features into categories for improved administration and data-sheet browsing.'
        );
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        Configuration::updateValue('PRODUCTFEATURESCATEGORIES_PRODUCT_FOOTER', false);
        Configuration::updateValue('PRODUCTFEATURESCATEGORIES_PRODUCT_TABS', true);

        $install = include(dirname(__FILE__).'/sql/install.php');

        return $install &&
            $this->installFixture() &&
            $this->installTab() &&
            $this->installOverride() &&
            parent::install() &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('header') &&
            $this->registerHook('displayFooterProduct');
    }

    public function installFixture()
    {
        $Fixture = new FeatureCategory();
        // Languages
        $languages = Language::getLanguages();
        foreach ($languages as $language) {
            $Fixture->name[$language['id_lang']] = 'Default';
        }
        // Shops
        Shop::addTableAssociation(FeatureCategory::$definition['table'], array('type' => 'shop'));
        if ($Fixture->add()) {
            return true;
        }
        return false;
    }

    public function installTab()
    {
        $new_tab = new Tab();
        $new_tab->class_name = 'AdminProductFeaturesCategories';
        $new_tab->id_parent = Tab::getIdFromClassName('AdminCatalog');
        $new_tab->module = $this->name;
        $languages = Language::getLanguages();
        foreach ($languages as $language) {
            $new_tab->name[$language['id_lang']] = 'Product Feature Categories';
        }
        try {
            $new_tab->add();
        } catch (Exception $e) {
            $this->_errors[] = 'Unable to add Tab';
            return false;
        }
        return true;
    }

    public function installOverride()
    {
        $dir = _PS_ROOT_DIR_.'/override/controllers/admin/templates/products';
 
        if (!file_exists($dir) && !is_dir($dir)) {
            @mkdir(_PS_ROOT_DIR_.'/override/controllers/admin/templates/products', 0755);
        }
         
        $fileData = Tools::file_get_contents(dirname(__FILE__).'/views/templates/admin/products/features.tpl');
        $filePath = _PS_ROOT_DIR_.'/override/controllers/admin/templates/products/features.tpl';
         
        if (!file_put_contents($filePath, $fileData)) {
            return false;
        }
        return true;
    }

    public function uninstallOverride()
    {
        $file = _PS_ROOT_DIR_.'/override/controllers/admin/templates/products/features.tpl';

        if (file_exists($file)) {
            if (!unlink($file)) {
                $this->_errors[] = 'Unable to delete features.tpl override';
                return false;
            }
        }
        return true;
    }

    public function uninstall()
    {
        Configuration::deleteByName('PRODUCTFEATURESCATEGORIES_PRODUCT_FOOTER');
        Configuration::deleteByName('PRODUCTFEATURESCATEGORIES_PRODUCT_TABS');
        $uninstall = include(dirname(__FILE__).'/sql/uninstall.php');

        return $this->uninstallTab() &&
            $this->uninstallOverride() &&
            $uninstall &&
            parent::uninstall() &&
            $this->cleanUpAfterPrestashop();
    }

    public function uninstallTab()
    {
        // Uninstall Tab
        $tab = new Tab((int)Tab::getIdFromClassName('AdminProductFeaturesCategories'));
        try {
            $tab->delete();
        } catch (Exception $e) {
            $this->_errors[] = 'Unable to delete Tab';
            return false;
        }
        return true;
    }

    public function cleanUpAfterPrestashop()
    {
        $file = _PS_ROOT_DIR_.'/override/classes/Feature.php';
        $content = Tools::file_get_contents($file);
        $pattern = '/\'table\'(.*)\);/s';
        $result = preg_replace($pattern, '', $content);
        if (!file_put_contents($file, $result)) {
            $this->_errors[] = 'Unable to clean up Feature.php file';
            return false;
        }
        return true;
    }

    private function displayInformation()
    {
        $this->context->smarty->assign(array(
            'productfeaturescategories_cabinet' => Media::getMediaPath(
                _PS_MODULE_DIR_.$this->name.'/views/img/cabinet.png'
            )
        ));
        return $this->display(__FILE__, 'info.tpl');
    }

    public function getContent()
    {
        // Post process for update/add & configuration forms
        if (Tools::isSubmit('submit'.$this->name.'configuration')) {
            $this->postProcess();
        }

        $this->html .= $this->displayInformation();
        return $this->html.$this->renderConfigurationForm();
    }

    public function renderConfigurationForm()
    {
        $fields_form = array();
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs'
            ),
            'input' => array(
                array(
                    'type' => 'switch',
                    'label' => $this->l('Custom Data Sheet'),
                    'desc' => $this->l('Display categorized data sheet in the front-office product footer.').
                        '<br>'.$this->l('Hide the original features view via CSS or product.tpl'),
                    'name' => 'PRODUCTFEATURESCATEGORIES_PRODUCT_FOOTER',
                    'is_bool' => true,
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
                    )
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Tabs Style'),
                    'desc' => $this->l('Catagorize features into tabs. Only works if Custom Data Sheet is enabled.'),
                    'name' => 'PRODUCTFEATURESCATEGORIES_PRODUCT_TABS',
                    'is_bool' => true,
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
                    )
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            )
        );
         
        $helper = new HelperForm();
         
        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
         
        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
         
        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit'.$this->name.'configuration';
        $helper->toolbar_btn = array(
            'save' =>
            array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                '&token='.Tools::getAdminTokenLite('AdminModules'),
            ),
            'back' => array(
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );
         
        // Load current value
        $helper->fields_value['PRODUCTFEATURESCATEGORIES_PRODUCT_FOOTER'] =
            Configuration::get('PRODUCTFEATURESCATEGORIES_PRODUCT_FOOTER');
        $helper->fields_value['PRODUCTFEATURESCATEGORIES_PRODUCT_TABS'] =
            Configuration::get('PRODUCTFEATURESCATEGORIES_PRODUCT_TABS');
         
        return $helper->generateForm($fields_form);
    }

    protected function postProcess()
    {
        if (Tools::isSubmit('submit'.$this->name.'configuration')) {
            Configuration::updateValue(
                'PRODUCTFEATURESCATEGORIES_PRODUCT_FOOTER',
                Tools::getValue('PRODUCTFEATURESCATEGORIES_PRODUCT_FOOTER')
            );
            Configuration::updateValue(
                'PRODUCTFEATURESCATEGORIES_PRODUCT_TABS',
                Tools::getValue('PRODUCTFEATURESCATEGORIES_PRODUCT_TABS')
            );
        }
        $this->html .= $this->displayConfirmation($this->l('Settings updated'));
    }

    public function hookHeader()
    {
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            if (Configuration::get('PRODUCTFEATURESCATEGORIES_PRODUCT_FOOTER')) {
                $this->context->controller->addCSS($this->_path.'/views/css/front_1.7.css');
            }
        } else {
            if (Configuration::get('PRODUCTFEATURESCATEGORIES_PRODUCT_FOOTER')) {
                $this->context->controller->addCSS($this->_path.'/views/css/front.css');
            }
        }
    }

    public function hookDisplayBackOfficeHeader($params)
    {
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $this->context->controller->addJS($this->_path . 'views/js/productfeaturescategories_17.js');
            $this->context->controller->addCSS($this->_path. 'views/css/productfeaturescategories_17.css');
        }
    }

    public function hookDisplayFooterProduct()
    {
        if (Configuration::get('PRODUCTFEATURESCATEGORIES_PRODUCT_FOOTER')) {
            $features = $this->addFeatureCategories(
                Product::getFrontFeaturesStatic($this->context->language->id, (int)Tools::getValue('id_product'))
            );
            $categories = $this->getAssocFeatureCategories($features);
            $this->context->smarty->assign(
                array(
                    'style' => Configuration::get('PRODUCTFEATURESCATEGORIES_PRODUCT_TABS'),
                    'features' => $features,
                    'fc_categories' => $categories
                )
            );
            if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
                return $this->display(__FILE__, 'product_features_1.7.tpl');
            } else {
                return $this->display(__FILE__, 'product_features.tpl');
            }
        }
        return false;
    }

    private function getAssocFeatureCategories($features)
    {
        $categories = array();
        foreach ($features as $feature) {
            $categories[] = (int)$feature['id_feature_category'];
        }
        if (!empty($categories)) {
            $categories = FeatureCategory::getFeatureCategories(
                array_unique($categories),
                $this->context->language->id
            );
            foreach ($categories as &$category) {
                $category['id_feature_category'] = (int)$category['id_feature_category'];
            }
        }
        return $categories;
    }

    private function addFeatureCategories($features)
    {
        foreach ($features as &$feature) {
                $feature['id_feature_category'] =
                    (int)FeatureCategory::getFeatureCategoryByFeatureId($feature['id_feature']);
        }
        return $features;
    }
}
