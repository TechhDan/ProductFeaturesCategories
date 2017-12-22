/**
* 2017 WebDevOverture
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
*  @copyright 2017 WebDevOverture
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of WebDevOverture
*
*/
$(document).ready(function() {
	$( "#features-content" ).click(function() {

		// On click of category
		$('.select2-results__option').on('click', function() {
			if ($(this).find('ul').is(':visible')) {
		    	$(this).find('ul').hide();
			} else {
				$(this).find('ul').show();
			}
		});

		$('.select2-search__field').on('input', function() {
			$('.select2-results__option > ul').show();
		});

	});
});