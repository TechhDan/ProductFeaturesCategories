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

class AdminProductsController extends AdminProductsControllerCore
{
    public function initFormFeatures($obj)
    {
        if (!$this->default_form_language) {
            $this->getLanguages();
        }
        $data = $this->createTemplate($this->tpl_form);
        $data->assign('default_form_language', $this->default_form_language);
        $data->assign('languages', $this->_languages);
        if (!Feature::isFeatureActive()) {
            $this->displayWarning(
                $this->l('This feature has been disabled. ').
                ' <a href="index.php?tab=AdminPerformance&token='.
                Tools::getAdminTokenLite('AdminPerformance').
                '#featuresDetachables">'.$this->l('Performances').'</a>'
            );
        } else {
            if ($obj->id) {
                if ($this->product_exists_in_shop) {
                    $features = Feature::getFeatures(
                        $this->context->language->id,
                        (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP)
                    );
                    foreach ($features as $k => $tab_features) {
                        $features[$k]['current_item'] = false;
                        $features[$k]['val'] = array();
                        $custom = true;
                        foreach ($obj->getFeatures() as $tab_products) {
                            if ($tab_products['id_feature'] == $tab_features['id_feature']) {
                                $features[$k]['current_item'] = $tab_products['id_feature_value'];
                            }
                        }
                        $features[$k]['featureValues'] = FeatureValue::getFeatureValuesWithLang(
                            $this->context->language->id,
                            (int)$tab_features['id_feature']
                        );
                        if (count($features[$k]['featureValues'])) {
                            foreach ($features[$k]['featureValues'] as $value) {
                                if ($features[$k]['current_item'] == $value['id_feature_value']) {
                                    $custom = false;
                                }
                            }
                        }
                        if ($custom) {
                            $feature_values_lang = FeatureValue::getFeatureValueLang($features[$k]['current_item']);
                            foreach ($feature_values_lang as $feature_value) {
                                $features[$k]['val'][$feature_value['id_lang']] = $feature_value;
                            }
                        }
                    }
                    $custom_feature_categories = $this->getCustomFeatureCategories();
                    $data->assign('custom_feature_categories', $custom_feature_categories);
                    $data->assign('available_features', $features);
                    $data->assign('product', $obj);
                    $data->assign('link', $this->context->link);
                    $data->assign('default_form_language', $this->default_form_language);
                } else {
                    $this->displayWarning($this->l('You must save the product in this shop before adding features.'));
                }
            } else {
                $this->displayWarning($this->l('You must save this product before adding features.'));
            }
        }
        $this->tpl_form_vars['custom_form'] = $data->fetch();
    }

    public function getCustomFeatureCategories()
    {
        $id_lang = (int)$this->context->language->id;
        $id_shop = (int)$this->context->shop->id;

        $sql = Db::getInstance()->ExecuteS(
            'SELECT '._DB_PREFIX_.'feature_category_lang.`name`, '._DB_PREFIX_.'feature_category.`id_feature_category`
            FROM `'._DB_PREFIX_.'feature_category` fc
            INNER JOIN `'._DB_PREFIX_.'feature_category_lang` fcl ON fc.`id_feature_category` = fcl.`id_feature_category`
            WHERE fcl.`id_lang` = '.$id_lang.' AND fc.`id_shop` = '.$id_shop
        );

        return $sql;
    }
}
