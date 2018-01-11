
var ManageProvider = function()
{
	this.currentTab = 'ftp';
}
ManageProvider.prototype.displayTab =  function (tab)
{
	$('.cart_rule_tab').hide();
	$('.tab-page').removeClass('selected');
	$('#manage_provider_' + tab).show();
	$('#manage_provider_' + tab).addClass('selected');
	$('#cart_rule_' + tab).show();
	$('#currentFormTab').val(tab);
}
ManageProvider.prototype.setRemoteField = function(field,value)
{
	$(field).val(value);
	if($value="http"){

	}else{
		
	}

}
$(document).ready(function(){
	$('.cart_rule_tab').hide();
	$('.tab-page').removeClass('selected');
	$('#cart_rule_' + currentFormTab).show();
	$('#manage_provider_' + currentFormTab).addClass('selected');
});
