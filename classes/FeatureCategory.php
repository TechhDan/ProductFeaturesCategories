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

class FeatureCategory extends ObjectModel
{
    public $id_feature_category;
    public $id_shop;
    public $position;
    public $name;

    /**
     * @see ObjectModule::$definition
     */
    public static $definition = array(
        'table' => 'feature_category',
        'primary' => 'id_feature_category',
        'multilang' => true,
        'fields' => array(
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 128),
        ),
    );

    public static function getFeatureCategories()
    {
        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;

        $feature_categories = Db::getInstance()->ExecuteS(
            'SELECT fcl.`name`, fc.`id_feature_category`, fc.`position`
            FROM `'._DB_PREFIX_.'feature_category` fc
            INNER JOIN `'._DB_PREFIX_.'feature_category_lang` fcl ON fc.`id_feature_category` = fcl.`id_feature_category`
            WHERE fcl.`id_lang` = '.$id_lang.' AND fc.`id_shop` = '.$id_shop
        );

        return $feature_categories;
    }
}
