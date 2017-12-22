<?php
/**
* 2016-2017 WebDevOverture
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
*  @copyright 2016-2017 WebDevOverture
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of WebDevOverture
*/

require_once _PS_MODULE_DIR_.'productfeaturescategories/classes/FeatureCategory.php';

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
                    $custom_feature_categories = $this->removeEmptyFC(
                        FeatureCategory::getCustomFeatureCategories((int)$this->context->language->id),
                        $features
                    );
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

    public function removeEmptyFC($feature_categories, $features)
    {
        // Get features on list
        $fc_array = array();
        foreach ($features as $feature) {
            $fc_array[] = (int)$feature['category'];
        }
        $fc_array = array_unique($fc_array);

        // Remove empty feature categories
        foreach ($feature_categories as $key => $feature_category) {
            if (!in_array((int)$feature_category['id_feature_category'], $fc_array)) {
                unset($feature_categories[$key]);
            }
        }
        return $feature_categories;
    }
}
