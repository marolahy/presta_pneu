<style>
form#supplier_form { background-color:#ebedf4; border:1px solid #ccced7; min-height:404px; padding: 5px 10px 10px; margin-left:140px;}

#supplier_form {

}

#supplier_form h4 { font-size:18px; font-weight:normal; margin-top:0;}

</style>
{include file="toolbar.tpl" toolbar_btn=$toolbar_btn toolbar_scroll=$toolbar_scroll title=$title}
<div class="leadin">{block name="leadin"}{/block}</div>
<div>
 	<div class="productTabs">
		<ul class="tab">
			<li class="tab-row">
				<a class="tab-page" id="manage_provider_ftp" href="javascript:manager.displayTab('ftp');">{l s='Remote configuration'}</a>
			</li>
			<li class="tab-row">
				<a class="tab-page" id="manage_provider_csv" href="javascript:manager.displayTab('csv');">{l s='Csv configuration'}</a>
			</li>
            <li class="tab-row">
				<a class="tab-page" id="manage_provider_csv_config" href="javascript:manager.displayTab('csv_config');">{l s='Csv'}</a>
			</li>
			<li class="tab-row">
				<a class="tab-page" id="manage_provider_tax" href="javascript:manager.displayTab('tax');">{l s='Taxe configuration'}</a>
			</li>
			<li class="tab-row">
				<a class="tab-page" id="manage_provider_categories" href="javascript:manager.displayTab('categories');">{l s='Categories configuration'}</a>
			</li>
			<li class="tab-row">
				<a class="tab-page" id="manage_provider_actions" href="javascript:manager.displayTab('actions');">{l s='Actions'}</a>
			</li>
            <li class="tab-row">
				<a class="tab-page" id="manage_provider_maping" href="javascript:manager.displayTab('maping');">{l s='Maping'}</a>
			</li>
             <li class="tab-row">
				<a class="tab-page" id="manage_provider_import_logs" href="javascript:manager.displayTab('log');">{l s='logs'}</a>
			</li>
		</ul>
	</div>
</div>
<form action="{$currentIndex|escape}&token={$currentToken|escape}&addcart_rule" id="supplier_form" method="post">
	<input type="hidden" name="id_supplier" value="{$id_supplier}" />
	{if $currentObject->id}<input type="hidden" name="id_supplier_form" value="{$currentObject->id|intval}" />{/if}
	<input type="hidden" id="currentFormTab" name="currentFormTab" value="ftp" />
		<div id="cart_rule_ftp" class="cart_rule_tab">
		<h4>{l s='FTP Configuration'}</h4>
		<div class="separation"></div>
		{include file='./ftp.tpl'}
	</div>
	<div id="cart_rule_csv" class="cart_rule_tab">
		<h4>{l s='Csv Configuration'}</h4>
		<div class="separation"></div>
		{include file='./csv.tpl'}
	</div>
	<div id="cart_rule_tax" class="cart_rule_tab">
		<h4>{l s='Tax Configuration'}</h4>
		<div class="separation"></div>
		{include file='./tax.tpl'}
	</div>
    <div id="cart_rule_csv_config" class="cart_rule_tab">
		<h4>{l s='CSV'}</h4>
		<div class="separation"></div>
		{include file='./csv_config.tpl'}
	</div>
	<div id="cart_rule_categories" class="cart_rule_tab">
		<h4>{l s='Categories Configuration'}</h4>
		<div class="separation"></div>
		{include file='./categories.tpl'}
	</div>
	<div id="cart_rule_actions" class="cart_rule_tab">
		<h4>{l s='Action'}</h4>
		<div class="separation"></div>
		{include file='./actions.tpl'}
	</div>
    <div id="cart_rule_maping" class="cart_rule_tab">
		<h4>{l s='Maping'}</h4>
		<div class="separation"></div>
		{include file='./maping.tpl'}
	</div>
    <div id="cart_rule_log" class="cart_rule_tab">
		<h4>{l s='Logs'}</h4>
		<div class="separation"></div>
		{include file='./logs.tpl'}
	</div>
	<div class="separation"></div>
	<div style="text-align:center">
		<input type="submit" value="{l s='Save'}" class="button" name="submitAddcart_rule" id="{$table|escape}_form_submit_btn" />
		<!--<input type="submit" value="{l s='Save and stay'}" class="button" name="submitAddcart_ruleAndStay" id="" />-->
	</div>
</form>





<script type="text/javascript" src="./../modules/manageprovider/views/js/manageprovider.js"></script>

<script type="text/javascript">
	var manager = new ManageProvider();
	var currentToken = '{$currentToken|escape:'quotes'}';
	var currentFormTab = '{if isset($smarty.post.currentFormTab)}{$smarty.post.currentFormTab|escape:'quotes'}{else}ftp{/if}';
</script>