<div class="panel">
	<h1>Categories</h1>
	{if $categories === false }
		<b>Please Configure your Api key</b>
	{else}
		<div class="panel panel-default">
			<form action="{$url_to_submit}" method="post">
				<div class="panel-heading">Configuration Categories</div>
				<table class="tree" >
					{foreach from=$categories  item=categorie}
						<tr class="treegrid-{$categorie->id} {if $categorie->parent > 0} treegrid-parent-{$categorie->parent}" {/if}">
							<td>
								<input type="checkbox" onclick="checkedElement(this)" name="activeCategories[]" value="{$categorie->id}" {if $categorie->checked} checked {/if} />
							</td>
							<td>{$categorie->nom}</td>
							<td>Nombre Articles {$categorie->nb_articles}</td>
						</tr>
					{/foreach}
				</table>
				<button type="submit" value="1" id="module_form_submit_btn" name="submitToncommerceCategories" class="btn btn-default pull-right">
					<i class="process-icon-save"></i> 
					Enregistrer
				</button>
			<form>
		</div>
	{/if}
	<br />
	<br />
</div>
<div>
	<div class="panel">
		<div class="panel panel-default">
			<form action="{$url_to_submit}" method="post">
			Importer et creer les categories cochées dans prestashop
			<button type="submit" value="1" id="module_form_submit_btn" name="submitSaveAndEraseCategories" class="btn btn-default pull-right">
								<i class="process-icon-save"></i> Enregistrer
							</button>
			</form>
		</div>
	
	</div>
	<div>
		<p>
			Vous pouvez lancer le cron en utilisant cette url<br />
			<a href="{$cron_url}"><i class="icon-external-link-sign"></i>{$cron_url}</a>
		</p>
	<br />
	</div>
</div>


{literal}
	<script>
	 $('.tree').treegrid();
	</script>
{/literal}
