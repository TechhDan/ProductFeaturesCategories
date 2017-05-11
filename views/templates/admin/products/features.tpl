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
<script>
function toggleFeatures(target) {
	var getStyle = document.getElementsByClassName(target)[0].style.display;
	if (getStyle == 'none') {
		[].forEach.call(document.querySelectorAll('.' + target), function(el) {
			el.style.display = '';
		});
	} else {
		[].forEach.call(document.querySelectorAll('.' + target), function(el) {
			el.style.display = 'none';
		});
	}
}
</script>
{if isset($product->id)}
<div id="product-features" class="panel product-tab">
	<input type="hidden" name="submitted_tabs[]" value="Features" />
	<h3>{l s='Assign features to this product' mod='productfeaturescategories'}</h3>

	<div class="alert alert-info">
		{l s='You can specify a value for each relevant feature regarding this product. Empty fields will not be displayed.' mod='productfeaturescategories'}<br/>
		{l s='You can either create a specific value, or select among the existing pre-defined values you\'ve previously added.' mod='productfeaturescategories'}
	</div>

	<table class="table">
		<thead>
			<tr>
				<th><span class="title_box">{l s='Feature' mod='productfeaturescategories'}</span></th>
				<th><span class="title_box">{l s='Pre-defined value' mod='productfeaturescategories'}</span></th>
				<th><span class="title_box"><u>{l s='or' mod='productfeaturescategories'}</u> {l s='Customized value' mod='productfeaturescategories'}</span></th>
			</tr>
		</thead>

		<tbody>
			<!--******************** Loop ********************-->
			{foreach from=$custom_feature_categories item=custom_category}
				<tr>
					<td colspan="3"><a class="btn btn-primary" onClick="toggleFeatures('hide_{$custom_category['name']|replace:' ':'_'|escape:'htmlall':'UTF-8'}')" href="#" style="display: block; width: 100%">{$custom_category['name']|escape:'htmlall':'UTF-8'}</button></td>
				</tr>
				{foreach from=$available_features item=available_feature}
				{if ($available_feature.category == $custom_category['id_feature_category'])
				OR (
					($available_feature.category == 0 AND $custom_category['id_feature_category'] == 1) ||
					($available_feature.category == null AND $custom_category['id_feature_category'] == 1)
				)}
				<tr>
					<td class="hide_{$custom_category['name']|replace:' ':'_'|escape:'htmlall':'UTF-8'}">{$available_feature.name|escape:'htmlall':'UTF-8'}</td>
					<td class="hide_{$custom_category['name']|replace:' ':'_'|escape:'htmlall':'UTF-8'}">
					{if sizeof($available_feature.featureValues)}
						<select id="feature_{$available_feature.id_feature|escape:'htmlall':'UTF-8'}_value" name="feature_{$available_feature.id_feature|escape:'htmlall':'UTF-8'}_value"
							onchange="$('.custom_{$available_feature.id_feature|escape:'htmlall':'UTF-8'}_').val('');">
							<option value="0">---</option>
							{foreach from=$available_feature.featureValues item=value}
							<option value="{$value.id_feature_value|escape:'htmlall':'UTF-8'}"{if $available_feature.current_item == $value.id_feature_value}selected="selected"{/if} >
								{$value.value|truncate:40|escape:'htmlall':'UTF-8'}
							</option>
							{/foreach}
						</select>
					{else}
						<input type="hidden" name="feature_{$available_feature.id_feature|escape:'htmlall':'UTF-8'}_value" value="0" />
						<span>{l s='N/A' mod='productfeaturescategories'} -
							<a href="{$link->getAdminLink('AdminFeatures')|escape:'html':'UTF-8'}&amp;addfeature_value&amp;id_feature={$available_feature.id_feature|escape:'htmlall':'UTF-8'}"
						 	class="confirm_leave btn btn-link"><i class="icon-plus-sign"></i> {l s='Add pre-defined values first' mod='productfeaturescategories'} <i class="icon-external-link-sign"></i></a>
						</span>
					{/if}
					</td>
					<td class="hide_{$custom_category['name']|replace:' ':'_'|escape:'htmlall':'UTF-8'}">

					<div class="row lang-0" style='display: none;'>
						<div class="col-lg-9">
							<textarea class="custom_{$available_feature.id_feature|escape:'htmlall':'UTF-8'}_ALL textarea-autosize"	name="custom_{$available_feature.id_feature|escape:'htmlall':'UTF-8'}_ALL"
									cols="40" style='background-color:#CCF'	rows="1" onkeyup="{foreach from=$languages key=k item=language}$('.custom_{$available_feature.id_feature|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}').val($(this).val());{/foreach}" >{$available_feature.val[1].value|escape:'html':'UTF-8'|default:""}</textarea>

						</div>
						{if $languages|count > 1}
							<div class="col-lg-3">
								<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
											{l s='ALL' mod='productfeaturescategories'}
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu">
									{foreach from=$languages item=language}
										<li>
											<a href="javascript:void(0);" onclick="restore_lng($(this),{$language.id_lang|escape:'htmlall':'UTF-8'});">{$language.iso_code|escape:'htmlall':'UTF-8'}</a>
										</li>
									{/foreach}
								</ul>
							</div>
						{/if}
					</div>

					{foreach from=$languages key=k item=language}
						{if $languages|count > 1}
						<div class="row translatable-field lang-{$language.id_lang|escape:'htmlall':'UTF-8'}">
							<div class="col-lg-9">
							{/if}
							<textarea
									class="custom_{$available_feature.id_feature|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'} textarea-autosize"
									name="custom_{$available_feature.id_feature|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}"
									cols="40"
									rows="1"
									onkeyup="if (isArrowKey(event)) return ;$('#feature_{$available_feature.id_feature|escape:'htmlall':'UTF-8'}_value').val(0);" >{$available_feature.val[$language.id_lang].value|escape:'html':'UTF-8'|default:""}</textarea>

						{if $languages|count > 1}
							</div>
							<div class="col-lg-3">
								<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
									{$language.iso_code|escape:'htmlall':'UTF-8'}
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu">
									<li><a href="javascript:void(0);" onclick="all_languages($(this));">{l s='ALL' mod='productfeaturescategories'}</a></li>
									{foreach from=$languages item=language}
									<li>
										<a href="javascript:hideOtherLanguage({$language.id_lang|escape:'htmlall':'UTF-8'});">{$language.iso_code|escape:'htmlall':'UTF-8'}</a>
									</li>
									{/foreach}
								</ul>
							</div>
						</div>
						{/if}
						{/foreach}
					</td>

				</tr>
				{/if}
				{foreachelse}
				<tr>
					<td colspan="3" style="text-align:center;"><i class="icon-warning-sign"></i> {l s='No features have been defined' mod='productfeaturescategories'}</td>
				</tr>
				{/foreach}

			{/foreach}		
		
		</tbody>
	</table>

	<a href="{$link->getAdminLink('AdminFeatures')|escape:'html':'UTF-8'}&amp;addfeature" class="btn btn-link confirm_leave button">
		<i class="icon-plus-sign"></i> {l s='Add a new feature' mod='productfeaturescategories'} <i class="icon-external-link-sign"></i>
	</a>
	<div class="panel-footer">
		<a href="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}{if isset($smarty.request.page) && $smarty.request.page > 1}&amp;submitFilterproduct={$smarty.request.page|intval}{/if}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel' mod='productfeaturescategories'}</a>
		<button type="submit" name="submitAddproduct" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> {l s='Save' mod='productfeaturescategories'}</button>
		<button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> {l s='Save and stay' mod='productfeaturescategories'}</button>
	</div>
</div>
<script type="text/javascript">
	if (tabs_manager.allow_hide_other_languages)
		hideOtherLanguage({$default_form_language|escape:'htmlall':'UTF-8'});
{literal}
	$(".textarea-autosize").autosize();

	function all_languages(pos)
	{
{/literal}
{if isset($languages) && is_array($languages)}
	{foreach from=$languages key=k item=language}
			pos.parents('td').find('.lang-{$language.id_lang|escape:'htmlall':'UTF-8'}').addClass('nolang-{$language.id_lang|escape:'htmlall':'UTF-8'}').removeClass('lang-{$language.id_lang|escape:'htmlall':'UTF-8'}');
	{/foreach}
{/if}
		pos.parents('td').find('.translatable-field').hide();
		pos.parents('td').find('.lang-0').show();
{literal}
	}

	function restore_lng(pos,i)
	{
{/literal}
{if isset($languages) && is_array($languages)}
	{foreach from=$languages key=k item=language}
			pos.parents('td').find('.nolang-{$language.id_lang|escape:'htmlall':'UTF-8'}').addClass('lang-{$language.id_lang|escape:'htmlall':'UTF-8'}').removeClass('nolang-{$language.id_lang|escape:'htmlall':'UTF-8'}');
	{/foreach}
{/if}
{literal}
		pos.parents('td').find('.lang-0').hide();
		hideOtherLanguage(i);
	}
</script>
{/literal}

{/if}
