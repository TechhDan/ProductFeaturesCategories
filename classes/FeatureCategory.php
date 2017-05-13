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
    public $position;
    public $name;

    /**
     * @see ObjectModule::$definition
     */
    public static $definition = array(
        'table' => 'feature_category',
        'primary' => 'id_feature_category',
        'multilang' => true,
        'multishop'=> true,
        'fields' => array(
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 128),
        ),
    );

    public function add($autodate = true, $null_values = false)
    {
        $this->position = self::getLastPosition(); 
        return parent::add($autodate, true);
    }

    public function update($null_values = false)
    {
        if (parent::update($null_values)) {
            return $this->cleanPositions();
        }
        return false;
    }

    public function delete()
    {
        if (parent::delete()) {
            return $this->cleanPositions();
        }
        return false;
    }

    public static function getLastPosition()
    {
        $sql = 'SELECT MAX(position) + 1 FROM `'._DB_PREFIX_.'feature_category`';

        return Db::getInstance()->getValue($sql);
    }

    public function updatePosition($way, $position)
    {
        if (!$res = Db::getInstance()->executeS(
            'SELECT fc.`id_feature_category`, fc.`position`
            FROM `'._DB_PREFIX_.'feature_category` fc
            ORDER BY fc.`position` ASC'
        )) {
            return false;
        }

        foreach($res as $feature_category) {
            if ((int)$feature_category['id_feature_category'] == (int)$this->id) {
                $moved_fc = $feature_category;
            }
        }

        if (!isset($moved_fc) || !isset($position)) {
            return false;
        }

        $result = Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'feature_category`
            SET `position`= `position` '.($way ? '- 1' : '+ 1').'
            WHERE `position` '.($way ? '> '.(int)$moved_fc['position'].
            ' AND `position` <= '.(int)$position : '< '.(int)$moved_fc['position'].
            ' AND `position` >= '.(int)$position)
        );

        $result &= Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'feature_category`
            SET `position` = '.(int)$position.'
            WHERE `id_feature_category` = '.(int)$moved_fc['id_feature_category']);

        return $result;
    }

    public static function cleanPositions()
    {
        $sql = 'SELECT `id_feature_category`
            FROM `'._DB_PREFIX_.'feature_category`
            ORDER BY `position`';
        $result = Db::getInstance()->executeS($sql);

        for ($i = 0, $total = count($result); $i < $total; ++$i) {
            $sql = 'UPDATE `'._DB_PREFIX_.'feature_category`
                SET `position` = ' .(int)$i.'
                WHERE `id_feature_category` = '.(int)$result[$i]['id_feature_category'];
            Db::getInstance()->execute($sql);
        }
        return true;
    }

    public static function getFeatureCategories($categories, $id_lang)
    {
        $feature_categories = Db::getInstance()->ExecuteS(
            'SELECT fcl.`name`, fcl.`id_feature_category`
            FROM `'._DB_PREFIX_.'feature_category_lang` fcl
            INNER JOIN `'._DB_PREFIX_.'feature_category` fc ON fc.`id_feature_category` = fcl.`id_feature_category`
            WHERE fcl.`id_lang` = '.$id_lang.' AND fcl.`id_feature_category` IN ('.implode(', ', $categories).') 
            ORDER BY fc.`position`'
        );

        return $feature_categories;
    }

    public static function getFeatureCategoryByFeatureId($feature_id)
    {
        $sql = Db::getInstance()->getValue(
            'SELECT `category` FROM `'._DB_PREFIX_.'feature` fc
            WHERE `id_feature` = '.(int)$feature_id
        );
        return $sql;
    }

    public static function getCategoryNamesAndIdsAll($id_lang)
    {
        $sql = Db::getInstance()->ExecuteS(
            'SELECT fcl.`name`, fcl.`id_feature_category` FROM `'._DB_PREFIX_.'feature_category_lang` fcl
            INNER JOIN `'._DB_PREFIX_.'feature_category` fc ON fc.`id_feature_category` = fcl.`id_feature_category`
            WHERE id_lang = '.(int)$id_lang.' ORDER BY fc.`position`'
        );
        return $sql;
    }

    public static function getCategoryNamesAndIdsGroup($id_lang, $shops)
    {
        $sql = Db::getInstance()->ExecuteS(
            'SELECT DISTINCT fcl.`name`, fcl.`id_feature_category` FROM `'._DB_PREFIX_.'feature_category_lang` fcl
            LEFT JOIN `'._DB_PREFIX_.'feature_category_shop` fcp ON fcp.`id_feature_category` = fcl.`id_feature_category`
            LEFT JOIN `'._DB_PREFIX_.'feature_category` fc ON fc.`id_feature_category` = fcl.`id_feature_category`
            WHERE id_lang = '.(int)$id_lang.' AND fcp.`id_shop` IN ('.implode(', ', $shops).')
            ORDER BY fc.`position`'
        );
        return $sql;
    }

    public static function getCategoryNamesAndIdsShop($id_lang, $id_shop)
    {
        $sql = Db::getInstance()->ExecuteS(
            'SELECT fcl.`name`, fcl.`id_feature_category` FROM `'._DB_PREFIX_.'feature_category_lang` fcl
            LEFT JOIN `'._DB_PREFIX_.'feature_category_shop` fcp ON fcp.`id_feature_category` = fcl.`id_feature_category`
            WHERE id_lang = '.(int)$id_lang.' AND fcp.`id_shop` = '.(int)$id_shop
        );
        return $sql;
    }

    public static function getCustomFeatureCategories($id_lang)
    {
        $sql = Db::getInstance()->ExecuteS(
            'SELECT fcl.`name`, fcl.`id_feature_category`
            FROM `'._DB_PREFIX_.'feature_category_lang` fcl
            INNER JOIN `'._DB_PREFIX_.'feature_category` fc ON fc.id_feature_category = fcl.id_feature_category
            WHERE fcl.`id_lang` = '.(int)$id_lang.' ORDER BY fc.`position`'
        );
        return $sql;
    }
}
