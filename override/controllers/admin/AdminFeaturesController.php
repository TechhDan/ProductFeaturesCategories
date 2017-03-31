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

class AdminFeaturesController extends AdminFeaturesControllerCore
{

    public function __construct()
    {
        $this->table = 'feature';
        $this->className = 'Feature';
        $this->list_id = 'feature';
        $this->identifier = 'id_feature';
        $this->lang = true;

        $this->fields_list = array(
            'id_feature' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'width' => 'auto',
                'filter_key' => 'b!name'
            ),
            'value' => array(
                'title' => $this->l('Values'),
                'orderby' => false,
                'search' => false,
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'position' => array(
                'title' => $this->l('Position'),
                'filter_key' => 'a!position',
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'position' => 'position'
            ),
            'category' => array(
                'title' => $this->l('Category'),
                'width' => 'auto',
                'class' => 'fixed-width-xs',
            )
        );

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?')
            )
        );
        AdminController::__construct();
    }

    public function renderForm()
    {
        $links = Db::getInstance()->ExecuteS('
            SELECT `category_name` FROM '._DB_PREFIX_.'productfeaturescategories
        ');
        if (Tools::getValue('id_feature')) {
            $default = Db::getInstance()->ExecuteS(
                'SELECT `category` FROM '._DB_PREFIX_.'feature_lang
                WHERE id_feature = ' . (int)Tools::getValue('id_feature')
            );
            if ($default[0]['category'] == null || $default[0]['category'] == 'Default') {
                array_unshift($links, array('category_name' => 'Default'));
            } else {
                $links = array_merge(array(array('category_name' => $default[0]['category'])), $links);
                $links = array_merge($links, array(array('category_name' => 'Default')));
                $links = array_unique($links, SORT_REGULAR);
            }
        } else {
            array_unshift($links, array('category_name' => 'Default'));
        }
        $this->toolbar_title = $this->l('Add a new feature');
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Feature'),
                'icon' => 'icon-info-sign'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'lang' => true,
                    'size' => 33,
                    'hint' => $this->l('Invalid characters:').' <>;=#{}',
                    'required' => true
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Category'),
                    'name' => 'category',
                    'lang' => true,
                    'options' => array(
                        'query' => $links,
                        'name' => 'category_name',
                        'id' => 'category_name'
                    )
                ),
            )
        );
        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->l('Shop association'),
                'name' => 'checkBoxShopAsso',
            );
        }
        $this->fields_form['submit'] = array(
            'title' => $this->l('Save'),
        );
        return AdminController::renderForm();
    }
}
