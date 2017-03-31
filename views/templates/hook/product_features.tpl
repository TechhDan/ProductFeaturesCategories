{*
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
*  @author WebDevOverture <contact@webdevoverture.com>
*  @copyright  2016 WebDevOverture
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of WebDevOverture
*}
{if $features}
	{if $style}
	<h3 class="page-product-heading">{l s='Data sheet' mod='productfeaturescategories'}</h3>
	<!-- Tabs style -->
	<section class="page-product-box">
		<ul class="nav nav-tabs">
			{foreach from=$categories key=i item=category}
				{if $category == 'Default'}
					<li {if $i == 0}class="active"{/if}>
						<a data-toggle="tab" href="#dynamic_tabs_default">{l s='General' mod='productfeaturescategories'}</a>
					</li>
				{else}
					<li {if $i == 0}class="active"{/if}>
						<a data-toggle="tab" href="#dynamic_tabs_{$category|replace:' ':'_'|escape:'html':'UTF-8'}">{$category|escape:'html':'UTF-8'}</a>
					</li>
				{/if}
			{/foreach}
		</ul>

		<div class="tab-content">
			<!-- Default -->
			{foreach from=$categories key=i item=category}
				{if $category == 'Default'}
			<div id="dynamic_tabs_default" class="tab-pane fade in {if $i == 0}active{/if}">
				<table class="table-data-sheet">
					{foreach from=$features item=feature}
						{if $feature.category == null || $feature.category == '' || $feature.category == 'Default'}
							<tr class="{cycle values="odd, even"}">
								<td>{$feature.name|escape:'html':'UTF-8'}</td>
								<td>{$feature.value|escape:'html':'UTF-8'}</td>
							</tr>
						{/if}
					{/foreach}
				</table>
			</div>
				{/if}
			{/foreach}

			<!-- Loop -->
			{foreach from=$categories key=i item=category}
				<div id="dynamic_tabs_{$category|replace:' ':'_'|escape:'html':'UTF-8'}" class="tab-pane fade in {if $i == 0}active{/if}">
					<table class="table-data-sheet">
						{foreach from=$features item=feature}
							{if $feature.category == $category && $feature.category != 'Default'}
								<tr class="{cycle values="odd, even"}">
									<td>{$feature.name|escape:'html':'UTF-8'}</td>
									<td>{$feature.value|escape:'html':'UTF-8'}</td>
								</tr>
							{/if}
						{/foreach}
					</table>
				</div>
			{/foreach}
		</div>
	</section>

	{else}
	<!-- Table style -->
	<section class="page-product-box">
		<h3 class="page-product-heading">{l s='Data sheet' mod='productfeaturescategories'}</h3>
		<table class="table-data-sheet">
			<!-- Default -->
			{foreach from=$categories item=category}
				{if $category == 'Default'}
					<tr>
						<th colspan="2" id="pfc_table_body"><strong>{l s='General' mod='productfeaturescategories'}</strong></th>
					</tr>
				{/if}
			{/foreach}
			{foreach from=$features item=feature}
				{if $feature.category == null || $feature.category == '' || $feature.category == 'Default'}
					<tr class="{cycle values="odd, even"}">
						<td>{$feature.name|escape:'html':'UTF-8'}</td>
						<td>{$feature.value|escape:'html':'UTF-8'}</td>
					</tr>
				{/if}
			{/foreach}

			<!-- Custom Categories -->
			{foreach from=$categories item=category}
				{if $category != 'Default'}
					<tr>
						<th colspan="2" id="pfc_table_header"><strong>{$category|escape:'html':'UTF-8'}</strong></th>
					</tr>
					{foreach from=$features item=feature}
						{if $feature.category == $category}
							<tr class="{cycle values="odd, even"}">
								<td>{$feature.name|escape:'html':'UTF-8'}</td>
								<td>{$feature.value|escape:'html':'UTF-8'}</td>
							</tr>
						{/if}
					{/foreach}
				{/if}
			{/foreach}
		</table>
	</section>
	{/if}
{/if}