<?php

/*
	-----
		The purpose of this script is to demonstrate a fully customisable customer hosted
		script for uploading into BAFTA Electron with a server side login.

		Username and password can be provided in this script or left bank to default
		to the credentials in S2Sv2-PHP-SDK/config.php

		You can optionally enter a custom path location and filename in the 'file_name'
		input text box.
	-----
	Elliot Mitchell - BAFTA Tech
*/

include('../../s2sClient.php');
$s2sClient = new s2sClient();

$login = $s2sClient->login();

if(!$login['successful']) echo '<font style="color: #FF0000">'.$login['error'].'</font><br />';

?>
<form class="upload">
	<table>
		<tr><td><label for="file">File</label></td><td><input type="file" id="file" name="file" /></td></tr>
		<tr><td><label for="file_name">Path</label></td><td><input type="text" id="path" name="path" /></td></tr>
		<tr><td><label for="file_name">File Name</label></td><td><input type="text" id="file_name" name="file_name" /></td></tr>
		<tr><td></td><td><input type="submit" value="Upload" /></td></tr>
	</table>
</form>
<?php include('html/upload.html'); ?>
<script>
	$("form.upload").submit(function(e) {
		e.preventDefault();
			'<?php echo $s2sClient->getBucket(); ?>' + ($("input[name=file_name]").val() || filename)
		var prefix = $("input[name=path]").val(),
			filename = $("input[name=file_name]").val(),
			uploadObject = new uploadClass();
		uploadObject.upload(
			"<?php echo $s2sClient->getAccessKeyId(); ?>",
			"<?php echo $s2sClient->getSecretAccessKey(); ?>",
			"<?php echo $s2sClient->getSessionToken(); ?>",
			"<?php echo $s2sClient->getBucket(); ?>",
			"<?php echo $s2sClient->getRegion(); ?>",
			prefix,
			filename
		);
	});
</script>
