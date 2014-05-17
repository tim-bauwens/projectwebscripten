function saveText()
{
	tinymce.triggerSave();
	tinymce.get('dataForm').save();
	var data = tinymce.get('dataForm').getContent();
    $('#textForm').append("<input type='hidden' id='content' name='content' value='" + data + "' />");
}

$(document).ready(function() {

	tinymce.init({
		selector:'textarea',
		height:250
	});

});



