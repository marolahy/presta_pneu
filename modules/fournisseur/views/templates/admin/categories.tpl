
<table cellpadding="0" cellspacing="0">
	<tr>
		<td>
            <div style="display:none;" id="category_remove">
                <div id="categ_remove_%s" class="margin-form">
                    </br></br><input type="text" name="Categorie[]" class="titre" />
                    <a href="javascript:removeContent(%s);" title="Ajouter un categorie" class="ajoutCategorie" rel="categorie"><img src="../../../../../../essais/modules/manageprovider/views/templates/admin/moins.png" alt="supprimer" /></a>
                </div>
            </div>
        	<div class="categorie" id="ajoutSupprimerCategorie">
                <div>
                    <label>{l s='Categorie'}</label>
                    &nbsp;<input type="text" name="Categorie[]" id="categ_model" class="titre" value=""/>
                    <a href="javascript:addContent();" title="Ajouter un categorie" class="ajoutCategorie" rel="categorie"><img src="../../../../../../essais/modules/manageprovider/views/templates/admin/plus.png" alt="Ajouter"</a>
                </div>
                {foreach $categories item='category' key='catkey'}
                <div id="categ_remove_{$catkey}">
                <input type="text" name="Categorie[]" class="titre" id="categValue_%s" value="{$category.column}"/>
                    <a href="javascript:removeContent({$catkey});" title="Ajouter un categorie" class="ajoutCategorie" rel="categorie"><img src="../../../../../../essais/modules/manageprovider/views/templates/admin/moins.png" alt="supprimer" /></a>
                </div>
                {/foreach}
                <div class="margin-form"><i>Ajouter un niveau à la categorie</i></div>
            </div>
         </td>
	</tr>
</table>
<script language="javascript">
var currentCategoryIndex = {$currentTab->getCategorieValue($id_supplier)|@count};
var addContent = function(){
    if( isNaN($("#categ_model").val()))
    {
        alert('Veuillez entrer un chiffre');
        return false;
    }
    $("#ajoutSupprimerCategorie").append($("#category_remove").html().replace(/%s/g,currentCategoryIndex));
    $("#categValue_"+currentCategoryIndex).val($("#categ_model").val());
    $("#categValue_"+currentCategoryIndex).attr("readonly","readonly");
    currentCategoryIndex++;
    
}
var removeContent=function(index){
    $("#categ_remove_"+index).remove();
    
}
</script>

