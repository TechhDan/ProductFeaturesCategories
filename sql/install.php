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

$sql = array();

$sql[] = 'ALTER TABLE `'._DB_PREFIX_.'feature` ADD category INT(10) UNSIGNED DEFAULT 0';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'feature_category` (
    `id_feature_category` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_shop` INT(10) UNSIGNED NOT NULL DEFAULT 1,
    `position` INT(10) UNSIGNED NOT NULL DEFAULT \'0\',
    PRIMARY KEY (`id_feature_category`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'feature_category_lang` (
    `id_feature_category` INT(10) UNSIGNED NOT NULL,
    `id_lang` INT(10) UNSIGNED NOT NULL,
    `name` VARCHAR(128) NOT NULL,
    PRIMARY KEY (`id_feature_category`, `id_lang`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8; ';

foreach ($sql as $query) {
    try {
        if (Db::getInstance()->execute($query) == false) {
            $this->_errors[] = Tools::displayError('Unknown SQL error while installing');
            return false;
        }
    } catch (Exception $e) {
        $this->_errors[] = Tools::displayError('SQL Error: ' . $e->getMessage(), false);
        return false;
    }
}
