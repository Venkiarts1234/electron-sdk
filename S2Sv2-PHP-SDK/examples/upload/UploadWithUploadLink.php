<?php

/*
	-----
		The purpose of this script is to demonstrate a fully customisable customer hosted
		script for uploading into BAFTA Electron with Upload Link details.

		No login is required for this script as is the nature of an upload link.
	-----
	Elliot Mitchell - BAFTA Tech
*/

include('../../s2sClient.php');
$s2sClient = new s2sClient();

// obtain $fileId, $uploaderId, $hash from the GET parameters of an upload link gerenerated on baftaelectron.com
$fileId = '';
$uploaderId = '';
$hash = '';
$uploader = $s2sClient->getFileUploader($fileId, $uploaderId, $hash, true);

if(!$uploader["successful"]) echo '<font style="color: #FF0000">'.$uploader['error'].'</font><br />';

?>
<form class="upload">
	<table>
		<tr><td><label for="file">File</label></td><td><input type="file" id="file" name="file" /></td></tr>
		<tr><td></td><td><input type="submit" value="Upload" /></td></tr>
	</table>
</form>
<?php include('html/upload.html'); ?>
<script>
	$("form.upload").submit(function(e) {
		e.preventDefault();
		var prefix = "<?php echo $uploader['result']->folder; ?>",
			uploadObject = new uploadClass();
		uploadObject.upload(
			"<?php echo $s2sClient->getAccessKeyId(); ?>",
			"<?php echo $s2sClient->getSecretAccessKey(); ?>",
			"<?php echo $s2sClient->getSessionToken(); ?>",
			"<?php echo $s2sClient->getBucket(); ?>",
			"<?php echo $s2sClient->getRegion(); ?>",
			prefix
		);
	});
</script>
