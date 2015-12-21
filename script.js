jQuery(function() {
	jQuery('.wiki_slice_form').submit(function() {
		var sectok = jQuery(this).find("input[name=sectok]").val();
		var from = jQuery(this).find("input[name=from]").val();
		var to = jQuery(this).find("input[name=to]").val();
		jQuery.ajax(DOKU_BASE.concat('lib/exe/ajax.php'), {
			data: {
				call: 'components.example',
				sectok: sectok,
				id: JSINFO["id"],
				range: {from: from, to: to}
				// note that javascript arrays must use the form
				// some_var: JSON.stringify(some_array)
			}
		}).done(function(data) {
			// the data is the returned slice
			alert(data);
		}).error(function(jqXHR, textStatus, errorThrown) {
			alert(errorThrown);
		});
		return false;
	});
});
