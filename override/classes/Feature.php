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

class Feature extends FeatureCore
{
    public $category;

    public static $definition = array(
        'table' => 'feature',
        'primary' => 'id_feature',
        'multilang' => true,
        'fields' => array(
            'position' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt',
            ),
            'name' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isGenericName',
                'required' => true,
                'size' => 128,
            ),
            'category' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt',
                'required' => false,
            ),
        ),
    );

    public static function getCategoryIdByFeatureId($id_feature)
    {
        $id_category = Db::getInstance()->getValue(
            'SELECT category FROM '._DB_PREFIX_.'feature
            WHERE id_feature = ' . (int)$id_feature
        );
        return $id_category;
    }

    public function update($nullValues = false)
    {
        $result = parent::update($nullValues);

        if (version_compare(_PS_VERSION_, '1.6.0.14', '<=') && $result) {
            $result &= ObjectModel::update($nullValues);
        }
        return $result;
    }
}
