<!--<table class="table" style="width:30%;" border="0" cellpadding="" cellspacing="0">       
                	<thead>
                	<tr>
                    	<th>Maping</th><th>Delimiteur</th></tr>
                	</thead>
                	<tbody><tr>
                    	<td align="left">Reference<td align="right"><input type="text" name="config_csv[reference]" value="{$currentTab->getCsvdelimitValue($id_supplier,'reference')|escape}"/></td></td></tr>
                        <tr><td align="left">Marque <td align="right"><input type="text" name="config_csv[manufacturer]" value="{$currentTab->getCsvdelimitValue($id_supplier,'manufacturer')|escape}"/></td></td></tr>
                        <tr><td align="left">Prix<td align="right"><input type="text" name="config_csv[price]" value="{$currentTab->getCsvdelimitValue($id_supplier,'price')|escape}" /></td></td></tr>
                        <tr><td align="left">Lien image<td align="right"><input type="text" name="config_csv[image]" value="{$currentTab->getCsvdelimitValue($id_supplier,'image')|escape}"/></td></td></tr>
                        <tr><td align="left">Short description<td align="right"><input type="text" name="config_csv[description]" value="{$currentTab->getCsvdelimitValue($id_supplier,'description')|escape}"/></td></td></tr>
                        <tr><td align="left">Poids<td align="right"><input type="text" name="config_csv[weight]" value="{$currentTab->getCsvdelimitValue($id_supplier,'weight')|escape}"/></td></td></tr>
                        <tr><td align="left">Nom du produit<td align="right"><input type="text" name="config_csv[name]" value="{$currentTab->getCsvdelimitValue($id_supplier,'name')|escape}"/></td></td></tr>
                	</tbody>
                    
</table></br>-->

<table cellpadding="0" cellspacing="0">
	<tr>
		<td>
        		<label>{l s='Separateur de ligne :'}</label>
                	<div class="margin-form">
             			<input type="text"  size="2" name="separateur_linge" value="{$currentTab->getCsvdelimitValue($id_supplier,'separateur_ligne')|escape}"/></br></div>
            	<label>{l s='Separateur de colonne :'}</label>
                	<div class="margin-form">
             			<input type="text" size="2" name="separateur_colonne" value="{$currentTab->getCsvdelimitValue($id_supplier,'separateur_colonne')|escape}"/></br></div>
                <label>{l s='Separateur de texte :'}</label>
                	<div class="margin-form">
             			<input type="text" size="2" name="separateur_text" value="{$currentTab->getCsvdelimitValue($id_supplier,'separateur_text')|escape}"/></div>
        </td>
	</tr>
</table>