function checkAllCategories(self){
 	jQuery('input:checkbox').prop('checked', self.checked);       
}
jQuery(document).ready(function(){
	jQuery("#categorie_tree").treetable();
});
function checkAllChild(id,checked){
	jQuery(".treegrid-parent-"+id).find('input').each(function(){
		$(this).prop('checked',checked);
		checkAllChild(getId(this),checked);
	});
}
function getId(el){
	var str = jQuery(el).parent().parent()[0].getAttribute('class')
	var ids = str.match(/treegrid-(\d*)/);
	return ids[1];
}
function checkedElement(el){
	checkAllChild(getId(el),el.checked );
}