
<fieldset><legend>Update</legend>
<table style="width:75%;" class="table" cellpadding=""0 cellspacing="0" bordercolor="#CCCCCC" align="center">
            	<thead bgcolor="#CCCCCC">
                	<tr>
                    	<th>Lundi</th><th>Mardi</th><th>Mercredi</th><th>Jeudi</th><th>Vendredi</th><th>Samedi</th><th>Dimanche</th></tr>
                </thead>
                <tbody><tr>
                		<td><p><input type="checkbox" name="update[lundi]" {if $currentTab->getActionValue($id_supplier,'lundi')==true }checked{/if} value="lundi"/></p></td>
                        <td><input type="checkbox" name="update[mardi]"{if $currentTab->getActionValue($id_supplier,'mardi')==true }checked{/if} value="mardi" value="mardi" /></td>
                        <td><input type="checkbox" name="update[mercredi]"{if $currentTab->getActionValue($id_supplier,'mercredi')==true }checked{/if} value="mercredi"  /></td>
                        <td><input type="checkbox" name="update[jeudi]" {if $currentTab->getActionValue($id_supplier,'jeudi')==true }checked{/if}  "value="jeudi" /></td>
                        <td><input type="checkbox" name="update[vendredi]" {if $currentTab->getActionValue($id_supplier,'vendredi')==true }checked{/if} value="vendredi" /></td>
                        <td><input type="checkbox" name="update[samedi]" {if $currentTab->getActionValue($id_supplier,'samedi')==true }checked{/if}  value=samedi"" /></td>
                        <td><input type="checkbox" name="update[dimanche]" {if $currentTab->getActionValue($id_supplier,'dimanche')==true }checked{/if}  value="dimanche" /></td></tr></tbody>
</table>
</fieldset>
					<div class="margin-form"><i>Cochez tous les jours auxquels vous souhaitez mettre à jour le fournisseur </i></div>
