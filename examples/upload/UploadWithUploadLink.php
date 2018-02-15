<?php
include('../../S2Sv2-PHP-SDK/s2sClient.php');
$s2sClient = new s2sClient();

// obtain $fileId, $uploaderId, $hash from an upload link gerenerated on baftaelectron.com
$fileId = '';
$uploaderId = '';
$hash = '';
$uploader = $s2sClient->getFileUploader($fileId, $uploaderId, $hash);

if(!$uploader["successful"]) echo '<font style="color: #FF0000">'.$uploader['error'].'</font><br />';

?>

<form class="upload">
	<div class="field">
		<label for="file">File</label>
		<input type="file" id="file" name="file" />
	</div>
	<div class="field">
		<input type="submit" value="Upload" />
	</div>
</form>
<div id="progress_div">
	<p>
		Start Time: <input type="text" class="start_time" /><br />
		Estimated End Time: <input type="text" class="estimated_end_time" /><br />
		Elapsed Upload Time: <input type="text" class="elapsed_upload_time" /><br />
		File Size Total: <input type="text" class="file_size_total" /><br />
		File Size Uploaded: <input type="text" class="file_size_uploaded" /><br />
		File Upload Percentage<input type="text" class="file_upload_percentage" /><br />
		Upload Status<input type="text" class="upload_status" /><br />
	</p>
</div>

<script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
<script src="js/s2s-upload-1.0.6.min.js"></script>
<script>
	var uploader = new S2S.Uploader({
        access_key_id: 		"<?php if($uploader["successful"]) echo $uploader['result']->credentials->accessKeyId; ?>",
        secret_access_key: 	"<?php if($uploader["successful"]) echo $uploader['result']->credentials->secretAccessKey; ?>",
        session_token: 		"<?php if($uploader["successful"]) echo $uploader['result']->credentials->sessionToken; ?>",
        bucket: 			"<?php if($uploader["successful"]) echo $uploader['result']->bucket; ?>",
        region: 			"<?php echo $s2sClient->getRegion(); ?>",
    });

	var start_time 				= "",
		estimated_end_time 		= "",
		estimated_time_left 	= "",
		elapsed_upload_time 	= ""
		file_size_total 		= "",
		file_size_uploaded 		= "",
		file_upload_percentage 	= "",
		upload_status 			= "";

	var time = function(d) {
		d = new Date(d);
		var h = ("00" + d.getHours()).slice(-2),
			m = ("00" + d.getMinutes()).slice(-2),
			s = ("00" + d.getSeconds()).slice(-2);
		return [h, m, s].join(":");
	};

	var bytes = function(bytes) {
		var sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
		if (bytes == 0) return '0B';
		var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
		return Math.round(bytes / Math.pow(1024, i)) + sizes[i];
	};

	$("form.upload").submit(function(e) {
		e.preventDefault();

		if($("input[name=file]")[0].files[0] === undefined) {
			noFileSelected();
			return false;
		}

		upload_status = "Uploading";
		updateUploadProgress();

		tick = (new Date()).getTime();

		//filename sanitisation
		var filename = "",
			filenameArray = $("input[name=file]")[0].files[0].name.split('.');
		$.each(filenameArray, function(key, chunk) {
			if(key === (filenameArray.length - 1)) filename += '.';
			else if(key > 0) filename += '_';
			filename += chunk.replace(/\W/g, '_');
		});

		uploader.upload(
			//use the uploader object to start the upload of the file
			$("input[name=file]")[0].files[0],
			'<?php if($uploader["successful"]) echo $uploader['result']->bucket . $uploader['result']->folder . 'folder/'; ?>' + filename
		).progress(function(p) {
			//update upload progress variables used for display
			var tock = (new Date()).getTime(),
				pct = p.loaded / p.total,
				time_spent = tock - tick,
				finish_time = tick + time_spent / pct;
			estimated_end_time = time(finish_time);//update calculated time that it will finish
			file_size_total = bytes(p.total);//grab the total file size
			file_size_uploaded = bytes(p.loaded);//update the amount of the file that has been uploaded
			file_upload_percentage = Math.round((p.loaded/p.total)*100);
			updateUploadProgress();
		}).done(function(resp) {
			upload_status = "Done";
			updateUploadProgress();
			console.log("Done", resp);
		}).fail(function(resp) {
			upload_status = "Fail";
			updateUploadProgress();
			onError(resp.error);
			console.log("Fail", resp);
		}).always(function(resp) {
			updateUploadProgress();
			clearInterval(timer);
			console.log("Always", resp);
		}).start();

		start_time = time(tick);//set the start time
		elapsed_upload_time = time(0);

		timer = setInterval(function() {
			elapsed_upload_time = time((new Date()).getTime() - tick);
		}, 1000);

		updateUploadProgress();
		return false;
	});

	function updateUploadProgress(){
		$('.start_time').attr('value', start_time);
		$('.estimated_end_time').attr('value', estimated_end_time);
		$('.elapsed_upload_time').attr('value', elapsed_upload_time);
		$('.file_size_total').attr('value', file_size_total);
		$('.file_size_uploaded').attr('value', file_size_uploaded);
		$('.file_upload_percentage').attr('value', file_upload_percentage);
		$('.upload_status').attr('value', upload_status);
	}

	function noFileSelected(){
		alert("Please select a file to uplaod");
	}

	function onError(error){
		alert('error - ' + error);
	}
</script>
