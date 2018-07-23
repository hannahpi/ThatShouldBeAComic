$(document).ready(function() { 
	$('#btnAddMore').click(function() {
		var toAppend = '<tr> <td><input name="uploadedfile[]" type="file" /></td>'
		             + '<td><input name="imageName[]" type="text" /></td>'
					 + '<td><input name="desc[]" type="text" /></td>'
					 + '<td><input name="anonymous[]" type="checkbox" value="1" /></td></tr>';
		$('#filesUpload').append(toAppend);
		});
	});