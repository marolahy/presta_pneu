<table cellpadding="0" cellspacing="0">
	<tr>
		<td>
        	<div id="type">
			<form><label>{l s='Type'}</label>
			<div class="margin-form">
				<select name="ftp_type" id="ftp_type">
					<option value="ftp">FTP</option>
					<option value="http">HTTP</option></select></br>
                    <i>Choisir un type d'hôte pour la connexion </i>
			</div></div>
			<div id="cont_ftp">
				<label>{l s='Hostname'}</label>
				<div class="margin-form">
					<input type="text" name="ftp_hostname" value="{$currentTab->getFtpValue($id_supplier,'host')|escape}" /></br>
				</div>
				<label>{l s='Username'}</label>
				<div class="margin-form">
					<input type="text" name="ftp_username" value="{$currentTab->getFtpValue($id_supplier,'username')|escape}" /></br>                  	
				</div>
				<label>{l s='Password'}</label>
				<div class="margin-form">
					<input type="password" name="ftp_password" value="{$currentTab->getFtpValue($id_supplier,'password')|escape}" /></br>                   
				</div>
				<label>{l s='Filename'}</label>
				<div class="margin-form">
					<input type="text" name="ftp_filename" value="{$currentTab->getFtpValue($id_supplier,'filename')|escape}" /></br>
				</div>
			</div>
			<div id="cont_http">
				<label>{l s='url'}</label>
				<div class="margin-form">
					<input type="text" name="ftp_url" value="{$currentTab->getFtpValue($id_supplier, 'filename')|escape}" /></br>                     
				</div>
			</div>
            </form>
            <div class="margin-form"><i>Identification du fournisseur avec un nom d'utilisateur et un mot de passe</i></div>
		</td>
	</tr>
</table>
<script>


	"{$currentTab->getFieldValue($currentObject, 'ftp_type')|escape}";
</script>
<script language="javascript">//<![CDATA[

$(document).ready(function() {
$("#cont_ftp").show();
$("#cont_http").hide();

$("#type").change(function() {

if ( $("#ftp_type").val()=="1"){
$("#cont_ftp").show();
$("#cont_http").hide();

}

else{

$("#cont_ftp").hide();
$("#cont_http").show();

}

});
});

//]]></script>

