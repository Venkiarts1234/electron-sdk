var uploadClass = function() {
	var start_time 				= "",
		estimated_end_time 		= "",
		estimated_time_left 	= "",
		elapsed_upload_time 	= "",
		file_size_total 		= "",
		file_size_uploaded 		= "",
		file_upload_percentage 	= "",
		upload_status 			= "",
		upload 					= null;

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

	this.upload = function(access_key_id, secret_access_key, session_token, bucket, region, prefix, filename) {

		if($("input[name=file]")[0].files[0] === undefined) {
			noFileSelected();
			return false;
		}

		if(prefix === undefined) prefix = "";
		if(filename === undefined || filename == "") filename = $("input[name=file]")[0].files[0].name;

		var uploader = new S2S.Uploader({
			access_key_id: 		access_key_id,
			secret_access_key: 	secret_access_key,
			session_token: 		session_token,
			bucket: 			bucket,
			region: 			region
		});

		upload_status = "Uploading";
		updateUploadProgress(true);

		tick = (new Date()).getTime();

		//filename sanitisation
		var filenameArray = filename.split('.');
		filename = "";
		$.each(filenameArray, function(key, chunk) {
			if(key === (filenameArray.length - 1)) filename += '.';
			else if(key > 0) filename += '_';
			filename += chunk.replace(/\W/g, '_');
		});

		//remove preceding slash from prefix if present
		prefix = prefix.replace(/^\//, '');

		console.log("Destination: " + bucket + '/' + prefix + filename);
		upload = uploader.upload(
			//use the uploader object to start the upload of the file
			$("input[name=file]")[0].files[0],
			bucket + '/' + prefix + filename
		);

		upload.progress(function(p) {
			//update upload progress variables used for display
			var tock = (new Date()).getTime(),
				pct = p.loaded / p.total,
				time_spent = tock - tick,
				finish_time = tick + time_spent / pct;
			estimated_end_time = time(finish_time);//update calculated time that it will finish
			file_size_total = bytes(p.total);//grab the total file size
			file_size_uploaded = bytes(p.loaded);//update the amount of the file that has been uploaded
			file_upload_percentage = Math.round((p.loaded/p.total)*100);
			updateUploadProgress(true);
		}).done(function(resp) {
			upload_status = "Done";
			updateUploadProgress(false);
			console.log("Done", resp);
		}).fail(function(resp) {
			if(resp.error.name != "RequestAbortedError") {
				upload_status = "Fail";
				onError(resp.error);
				console.log("Fail", resp);
			}
			else upload_status = "Aborted";
			updateUploadProgress(false);
		}).always(function(resp) {
			updateUploadProgress(true);
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
	};

	function updateUploadProgress(showCancelButton){
		$('.start_time').attr('value', start_time);
		$('.estimated_end_time').attr('value', estimated_end_time);
		$('.elapsed_upload_time').attr('value', elapsed_upload_time);
		$('.file_size_total').attr('value', file_size_total);
		$('.file_size_uploaded').attr('value', file_size_uploaded);
		$('.file_upload_percentage').attr('value', file_upload_percentage);
		$('.upload_status').attr('value', upload_status);

		if(showCancelButton) $('.cancel_upload').attr('style', '');
		else $('.cancel_upload').attr('style', 'visible: hidden');
	}

	function noFileSelected(){
		alert("Please select a file to uplaod");
	}

	function onError(error){
		alert('error - ' + error);
	}

	$(".cancel_upload").click(function(e) {
		e.preventDefault();
		upload.mu.abort();
	});
};
