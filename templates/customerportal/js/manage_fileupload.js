// Custom example logic
var uploader;
jQuery(function() {

	uploader = new plupload.Uploader({
		runtimes : 'gears,html5,html4,flash,silverlight,browserplus',
		browse_button : 'pickfiles',
		container : 'container',
		url : currentURL + "?fileupload=true",
		flash_swf_url : '/plupload/js/plupload.flash.swf',
        multipart_params: { folderid : uploadFolderID, module : fileuploadModule, crmid: fileuploadCrmID },
		silverlight_xap_url : '/plupload/js/plupload.silverlight.xap',
		filters : [
			{title : "Image files", extensions : "jpg,gif,png"},
			{title : "Zip files", extensions : "zip"}
		],
		resize : {width : 320, height : 240, quality : 90}
	});

	uploader.bind('Init', function(up, params) {
		// $('#filelist').html("<div>Current runtime: " + params.runtime + "</div>");
	});

	$('#uploadfiles').click(function(e) {
		uploader.start();
		e.preventDefault();
	});

	uploader.init();

	uploader.bind('FilesAdded', function(up, files) {
		$.each(files, function(i, file) {
            $('#filelist').show();
			$('#filelist').append(
				'<div id="' + file.id + '" style="height:30px;">' +
				"<div style='float:left;width:200px;'><b>" + file.name + '</b></div><div style="float:left;width:80px;">' + plupload.formatSize(file.size) + '</div><a href="#" onclick="removeFile(\'' + file.id + '\');return false;">Remove</a><div style="float:left;width:80px;" class="progress"></div>' +
			'</div>');
		});

		up.refresh(); // Reposition Flash/Silverlight
	});	uploader.bind('FilesRemoved', function(up, files) {
		$.each(files, function(i, file) {
			jQuery("#" + file.id).remove();
		});

		up.refresh(); // Reposition Flash/Silverlight
	});

	uploader.bind('UploadProgress', function(up, file) {
		$('#' + file.id + " .progress").html(file.percent + "%");
	});

	uploader.bind('Error', function(up, err) {
		$('#filelist').append("<div>Error: " + err.code +
			", Message: " + err.message +
			(err.file ? ", File: " + err.file.name : "") +
			"</div>"
		);

		up.refresh(); // Reposition Flash/Silverlight
	});

	uploader.bind('FileUploaded', function(up, file) {
		$('#' + file.id + " .progress").html("100%");
	});

    uploader.bind('StateChanged', function(up) {
		if(up.state == plupload.STOPPED) window.location.reload();
	});
});
function removeFile(id) {
    uploader.removeFile(uploader.getFile(id));
}