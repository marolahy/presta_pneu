<fieldset><legend>Maping {l s='Categorie'}</legend>
<table width="100%" border="0" cellpadding=""0 cellspacing="0" bordercolor="#CCCCCC">   
<tr> 
<td> 
		<div id="categorie_maping">
            <select style="width:300px;" id="cat" class="chosen">
            {$currentTab->getCategories()}
            </select>               
            &nbsp;<input type="text" name="maping[]" class="csv_maping" id="text_mapping_model" />&nbsp;
            <a href="javascript:ajoutCategoryMapping();;" title="Ajouter un categorie" class="ajoutCategorie" rel="categorie"><img src="" alt="Ajouter" /></a></div>
    <p><div class="margin-form" id="selectioncategorie">
            <div id="mapping_model" class="margin-form" style="display:none;">
                <div id="mapping_remove_%s">
                    <input type="hidden" name="mapping_category[]" id="category_mapping_%s" />
                    <input type="hidden" name="mapping_text[]" id="text_mapping_%s" />
                    <span class="result" id="category_mapping_text_%s"></span>
                    <span class="result" id="mapping_text_%s"></span>
                    <a href="javascript:suppCategoryMapping(%s);" title="Supprimer un categorie" class="supprimerCategorie" rel="categorie"><img src="" alt="Supprimer" /></a></div></p>
                </div>
            </div>
    </p>
</td>
</tr>
</table>
</fieldset>

<script type="text/javascript">
    var categoryMappingIndex =  0;
    var ajoutCategoryMapping = function(){
    $("#selectioncategorie").append($("#mapping_model").html().replace(/%s/g,currentCategoryIndex));
    $("#category_mapping_text_"+currentCategoryIndex).html($("#cat option:selected").text());
    $("#mapping_text_"+currentCategoryIndex).html($("#text_mapping_model").val());
    $("#category_mapping_"+currentCategoryIndex).val($("#cat").val());
    $("#text_mapping_"+currentCategoryIndex).val($("#text_mapping_model").val());;
    currentCategoryIndex++;
    }
    var suppCategoryMapping = function( index ){
        $("#mapping_remove_"+index).remove();
    }
   $('input[name=search]').keyup(function(){
      var $this = $(this);

      $this.next('select').children('[value^='+$this.val()+']:first').attr('selected', true);
    });
	
 </script>
 <script type="text/javascript">
 
 	jQuery(document).ready(function(){
		jQuery(".chosen").chosen();
});

 </script>
