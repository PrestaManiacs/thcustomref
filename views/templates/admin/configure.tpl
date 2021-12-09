{*
* 2006-2021 THECON SRL
*
* NOTICE OF LICENSE
*
* DISCLAIMER
*
* YOU ARE NOT ALLOWED TO REDISTRIBUTE OR RESELL THIS FILE OR ANY OTHER FILE
* USED BY THIS MODULE.
*
* @author    THECON SRL <contact@thecon.ro>
* @copyright 2006-2021 THECON SRL
* @license   Commercial
*}

<div class="panel">
	<h3><i class="icon icon-info"></i> {l s='Custom order reference' mod='thcustomref'}</h3>
	<img src="{$module_dir|escape:'html':'UTF-8'}/logo.png" id="payment-logo" class="pull-right" width="50" />
	<p>
		{l s='If you have any question about our module, or if you need help with your PrestaShop store, please contact us!' mod='thanaf'}
	</p>
	<p>Our Website: <a href="https://prestamaniacs.com/" target="_blank">prestamaniacs.com</a><br />
		We are also present on: <a href="https://addons.prestashop.com/en/244_thecon" target="_blank">addons.prestashop.com</a><br />
		Facebook: <a href="https://www.facebook.com/prestaManiacs" target="_blank">prestaManiacs</a>
	</p>
</div>

<div class="row thdisplay">
	<div class="col-md-8">
		{$renderForm nofilter}
	</div>

	<div class="col-md-4">
		<div class="panel thdisplayright">
			<h3><i class="icon icon-eye-open"></i> {l s='Preview' mod='thcustomref'}</h3>
			<div class="thdesignpreview">
				<h3>{l s='You can see here which will be the reference of the next order' mod='thcustomref'}</h3>
				<div id="thcustomref_preview">{$THCUSTOMREF_DEFAULT|escape:'html':'UTF-8'}</div>
			</div>
		</div>
	</div>
</div>



