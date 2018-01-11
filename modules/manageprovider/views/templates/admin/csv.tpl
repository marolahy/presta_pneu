<fieldset><legend>CSV</legend>
<table class="table" style="width:30%;" border="0" cellpadding="" cellspacing="0">       
                	<thead>
                	<tr>
                    	<th>Maping</th><th>Colonne n°</th></tr>
                	</thead>
                	<tbody><tr>
                    	<td align="left">Reference<td align="right"><input type="text" name="csv[reference]" value="{$currentTab->getCsvValue($id_supplier,'reference')|escape}"/></td></td></tr>
                        <tr><td align="left">Marque <td align="right"><input type="text" name="csv[manufacturer]" value="{$currentTab->getCsvValue($id_supplier,'manufacturer')|escape}"/></td></td></tr>
                        <tr><td align="left">Prix<td align="right"><input type="text" name="csv[price]" value="{$currentTab->getCsvValue($id_supplier,'price')|escape}" /></td></td></tr>
                        <tr><td align="left">Lien image<td align="right"><input type="text" name="csv[image]" value="{$currentTab->getCsvValue($id_supplier,'image')|escape}"/></td></td></tr>
                        <tr><td align="left">Short description<td align="right"><input type="text" name="csv[description]" value="{$currentTab->getCsvValue($id_supplier,'description')|escape}"/></td></td></tr>
                        <tr><td align="left">Poids<td align="right"><input type="text" name="csv[weight]" value="{$currentTab->getCsvValue($id_supplier,'weight')|escape}"/></td></td></tr>
                        <tr><td align="left">Nom du produit<td align="right"><input type="text" name="csv[name]" value="{$currentTab->getCsvValue($id_supplier,'name')|escape}"/></td></td></tr>
                        <tr><td align="left">Ean13<td align="right"><input type="text" name="csv[ean13]" value="{$currentTab->getCsvValue($id_supplier,'ean13')|escape}"/></td></td></tr>
                	</tbody>
                    
</table></br>
					<div align="left"><i>Inserer le numero de la colonne de chaque maping pour le fichier CSV</i></div></fieldset>