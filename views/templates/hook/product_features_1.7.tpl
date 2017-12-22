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
	<!-- Tabs style -->
	<div class="tabs">
		<h3 id="pfc_data_sheet_heading">{l s='Data sheet' mod='productfeaturescategories'}</h3>
			<ul class="nav nav-tabs">
				{foreach from=$fc_categories key=i item=fc_category}
					<li {if $i == 0}class="nav-item"{else}class="nav-item"{/if}>
						<a {if $i == 0}class="active nav-link"{else}class="nav-link"{/if} data-toggle="tab" href="#pfc_tabs_{$fc_category.id_feature_category|escape:'html':'UTF-8'}">{$fc_category.name|escape:'html':'UTF-8'}</a>
					</li>
				{/foreach}
			</ul>

			<div class="tab-content">
				<!-- Loop -->
				{foreach from=$fc_categories key=i item=fc_category}
					<div id="pfc_tabs_{$fc_category.id_feature_category|escape:'html':'UTF-8'}" class="tab-pane fade in {if $i == 0}active{/if} product-features">
						<dl class="data-sheet">
							{foreach from=$features item=feature}
								{if $feature.id_feature_category == $fc_category.id_feature_category}
									<dt class="name">{$feature.name|escape:'html':'UTF-8'}</dt>
									<dd class="value">{$feature.value|escape:'html':'UTF-8'}</dd>
								{/if}
							{/foreach}
						</dl>
					</div>
				{/foreach}
			</div>
	</div>

	{else}
	<!-- Table style -->
	<div class="tabs product-features">
		<h3 id="pfc_data_sheet_heading">{l s='Data sheet' mod='productfeaturescategories'}</h3>
		
			<!-- Custom Categories -->
			{foreach from=$fc_categories item=fc_category}
				<h3 class="h6">{$fc_category.name|escape:'html':'UTF-8'}</h3>
				<dl class="data-sheet">
				{foreach from=$features item=feature}
					{if $feature.id_feature_category == $fc_category.id_feature_category}
						<dt class="name">{$feature.name|escape:'html':'UTF-8'}</dt>
						<dd class="value">{$feature.value|escape:'html':'UTF-8'}</dd>
					{/if}
				{/foreach}
				</dl>
			{/foreach}
		</dl>
	</div>
	{/if}
{/if}