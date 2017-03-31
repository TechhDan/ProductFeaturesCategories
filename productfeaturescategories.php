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

class ProductFeaturesCategories extends Module
{
    protected $html;

    public function __construct()
    {
        $this->name = 'productfeaturescategories';
        $this->tab = 'administration';
        $this->version = '1.0.4';
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

        include(dirname(__FILE__).'/sql/install.php');
        $this->installOverride();

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('displayFooterProduct');
    }

    public function installOverride()
    {
        $dir = _PS_ROOT_DIR_.'/override/controllers/admin/templates/products';
 
        if (!file_exists($dir) && !is_dir($dir)) {
            @mkdir(_PS_ROOT_DIR_.'/override/controllers/admin/templates/products', 0755);
        }
         
        $fileData = Tools::file_get_contents(dirname(__FILE__).'/views/templates/admin/products/features.tpl');
        $filePath = _PS_ROOT_DIR_.'/override/controllers/admin/templates/products/features.tpl';
         
        file_put_contents($filePath, $fileData);
    }

    public function uninstall()
    {
        Configuration::deleteByName('PRODUCTFEATURESCATEGORIES_PRODUCT_FOOTER');
        Configuration::deleteByName('PRODUCTFEATURESCATEGORIES_PRODUCT_TABS');

        include(dirname(__FILE__).'/sql/uninstall.php');

        return parent::uninstall();
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
        if (Tools::getValue('addNewFeatureCategory') !== false ||
            Tools::getValue('updateproductfeaturescategories') !== false) {
            return $this->renderForm();
        }

        if (Tools::isSubmit($this->name.'submitCategory') ||
            Tools::isSubmit('submit'.$this->name.'configuration')) {
            $this->postProcess();
        }

        if (Tools::getValue('deleteproductfeaturescategories') !== false) {
            $this->deleteEntry();
        }

        $this->html .= $this->displayInformation();
        return $this->html.$this->generateFeatureCategoriesList().$this->renderConfigurationForm();
    }

    private function deleteEntry()
    {
        if ((bool)Tools::getValue('id_productfeaturescategories')) {

            $original = Db::getInstance()->ExecuteS(
                'SELECT `category_name` FROM '._DB_PREFIX_.'productfeaturescategories
                WHERE id_productfeaturescategories = ' . (int)Tools::getValue('id_productfeaturescategories')
            );
            if ($original) {
                $original = $original[0]['category_name'];

                Db::getInstance()->Execute('
                UPDATE '._DB_PREFIX_.'feature_lang 
                SET category = null
                WHERE category = \''.pSQL($original).'\' 
            ');
            }

            Db::getInstance()->Execute('
                DELETE FROM '._DB_PREFIX_.'productfeaturescategories 
                WHERE id_productfeaturescategories = '.(int)Tools::getValue('id_productfeaturescategories').'
            ');
            $this->html .= $this->displayConfirmation($this->l('Settings updated'));
        }
    }

    private function generateFeatureCategoriesList()
    {
        $id_lang = (int)$this->context->language->id;
        $id_shop = (int)$this->context->shop->id;

        $links = Db::getInstance()->ExecuteS(
            'SELECT '._DB_PREFIX_.'feature_category_lang.`name`, '._DB_PREFIX_.'feature_category.`id_feature_category`
            FROM `'._DB_PREFIX_.'feature_category` fc
            INNER JOIN `'._DB_PREFIX_.'feature_category_lang` fcl ON fc.`id_feature_category` = fcl.`id_feature_category`
            WHERE fcl.`id_lang` = '.$id_lang.' AND fc.`id_shop` = '.$id_shop
        );

        $field_list = array(
            'id_feature_category' => array(
                'title' => $this->l('Id'),
                'width' => 140,
                'type' => 'text',
                'position' => 'position'
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'width' => 140,
                'type' => 'text',
            ),
        );
        $helper = new HelperList();
        $helper->class = 'FeatureCategory';
        $helper->table = 'feature_category';
        $helper->bulk_actions = true;
        $helper->tpl_vars['show_filters'] = true;
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->identifier = 'id_feature_category';
        $helper->actions = array('edit', 'delete');
        $helper->show_toolbar = true;
        $helper->module = $this;
        $helper->title = 'Feature Categories';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->toolbar_btn = array(
           'add' => array(
            'desc' => $this->l('Add new feature category'),
            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&addNewFeatureCategory'.
            '&token='.Tools::getAdminTokenLite('AdminModules'),
            'class' => 'process-icon-new'
           )
        );
        return $helper->generateList($links, $field_list);
    }

    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Category Feature'),
                    'icon' => 'icon-envelope'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Feature Category'),
                        'name' => 'FEATURE_CATEGORY_NAME',
                        'required' => true
                    ),
                    array(
                        'type' => 'hidden',
                        'label' => 'none',
                        'name' => 'CATEGORY_ID',
                        'required' => false
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
                'back' => array(
                    'title' => $this->l('Back')
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->class = 'ProductFeaturesCategories';
        $helper->table = 'productfeaturescategories';
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ?
        Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->id = (int)Tools::getValue('id_productfeaturescategories');
        $helper->identifier = $this->identifier;
        $helper->submit_action = $this->name.'submitCategory';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).
        '&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues($helper->id),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form));
    }

    public function getConfigFieldsValues($id)
    {
        if ((bool)Tools::getValue('id_productfeaturescategories')) {
            $category_id = Tools::getValue('id_productfeaturescategories');
        } else {
            $category_id = null;
        }
        if ($id !== false) {
            $data = Db::getInstance()->getRow(
                'SELECT * FROM '._DB_PREFIX_.'productfeaturescategories WHERE id_productfeaturescategories = '. $id
            );
            return array(
                'FEATURE_CATEGORY_NAME' => $data['category_name'],
                'CATEGORY_ID' => $category_id
            );
        } else {
            return array(
                'FEATURE_CATEGORY_NAME' => '',
                'CATEGORY_ID' => $category_id
            );
        }
        
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
        if (Tools::isSubmit($this->name.'submitCategory') && (bool)Tools::getValue('FEATURE_CATEGORY_NAME')) {
            if ((bool)Tools::getValue('CATEGORY_ID')) {
                $original = Db::getInstance()->ExecuteS(
                    'SELECT `category_name` FROM '._DB_PREFIX_.'productfeaturescategories
                    WHERE id_productfeaturescategories = ' . (int)Tools::getValue('CATEGORY_ID')
                );
                $original = $original[0]['category_name'];

                Db::getInstance()->Execute('
                    UPDATE '._DB_PREFIX_.'feature_lang 
                    SET category = \''.pSQL(Tools::getValue('FEATURE_CATEGORY_NAME')).'\' 
                    WHERE category = \''.pSQL($original).'\' 
                ');

                Db::getInstance()->Execute('
                    UPDATE '._DB_PREFIX_.'productfeaturescategories 
                    SET category_name = \''.pSQL(Tools::getValue('FEATURE_CATEGORY_NAME')).'\' 
                    WHERE id_productfeaturescategories = '.(int)Tools::getValue('CATEGORY_ID').'
                ');
            } else {
                Db::getInstance()->Execute('
                INSERT INTO '._DB_PREFIX_.'productfeaturescategories (category_name)
                VALUES (\''.pSQL(Tools::getValue('FEATURE_CATEGORY_NAME')).'\')
                ');
            }
            
        }
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

    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        if (Configuration::get('PRODUCTFEATURESCATEGORIES_PRODUCT_FOOTER')) {
            $this->context->controller->addCSS($this->_path.'/views/css/front.css');
        }
    }

    public function hookDisplayFooterProduct()
    {
        if (Configuration::get('PRODUCTFEATURESCATEGORIES_PRODUCT_FOOTER')) {
            $features = $this->getFrontFeatures($this->context->language->id, (int)Tools::getValue('id_product'));
            $this->context->smarty->assign(
                array(
                    'style' => Configuration::get('PRODUCTFEATURESCATEGORIES_PRODUCT_TABS'),
                    'features' => $features,
                    'categories' => $this->getFeatureCategories($features)
                )
            );
            return $this->display(__FILE__, 'product_features.tpl');
        }
        return false;
        
    }

    private function getFrontFeatures($id_lang, $id_product)
    {

        if (!Feature::isFeatureActive()) {
            return array();
        }
        $features = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT name, value, pf.id_feature, fl.category
            FROM '._DB_PREFIX_.'feature_product pf
            LEFT JOIN '._DB_PREFIX_.'feature_lang fl ON (fl.id_feature = pf.id_feature AND fl.id_lang = '.(int)$id_lang.')
            LEFT JOIN '._DB_PREFIX_.'feature_value_lang fvl ON (fvl.id_feature_value = pf.id_feature_value AND fvl.id_lang = '.(int)$id_lang.')
            LEFT JOIN '._DB_PREFIX_.'feature f ON (f.id_feature = pf.id_feature AND fl.id_lang = '.(int)$id_lang.')
            '.Shop::addSqlAssociation('feature', 'f').'
            WHERE pf.id_product = '.(int)$id_product.'
            ORDER BY f.position ASC'
        );
        return $features;
    }

    private function getFeatureCategories($features)
    {
        $categories = array();
        foreach ($features as $value) {
            if ($value['category'] != null) {
                $categories[] = $value['category'];
            } elseif ($value['category'] == null || $value['category'] == '') {
                $categories[] = 'Default';
            }
        }
        $categories = array_values(array_unique($categories));
        for ($x = 0; $x < count($categories); $x++) {
            if ($categories[$x] == 'Default') {
                unset($categories[$x]);
                array_unshift($categories, 'Default');
            }
        }
        return $categories;
    }
}
