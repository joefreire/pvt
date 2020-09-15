/*! Download button for Buttons
 * 2018 SpryMedia Ltd - MIT licensed - datatables.net/license
 */

/**
 * @summary     Download Button
 * @author      SpryMedia Ltd (www.datatables.net)
 * @copyright   Copyright 2018 SpryMedia Ltd.
 *
 * License      MIT - http://datatables.net/license/mit
 *
 * A button that can be used to trigger a server-side action, typically a
 * download of a file generated by the server. If server-side processing
 * is enabled in the host table it will automatically add the last parameters
 * used for a table draw allowing the script to output a file with the same
 * order / search applied as the main table.
 * 
 * This is particularly useful when using server-side processing and wishing
 * to allow user export of the data in a table. The default Buttons package
 * will only export the data available on the client-side, while this button
 * can be used to let the server generate the required file and then download
 * it to the client-side.
 *
 * @example
 *   // Download button
 * 	var table = $('#example').DataTable({
 * 		dom: 'Bfrtip',
 * 		buttons: [
 * 			{
 * 				extend: 'download',
 * 				url: '/api/download'
 * 			}
 * 		]
 * 	});
 */
(function(window, document, $, undefined) {
	function flattenJson(data, name, flattened) {
		if (!flattened) {
			flattened = {};
		}

		if (!name) {
			name = '';
		}

		if ($.isPlainObject(data) || $.isArray(data)) {
			$.each(data, function(idx, val) {
				if (name === '') {
					flattenJson(val, idx, flattened);
				} else {
					flattenJson(val, name + '[' + idx + ']', flattened);
				}
			});
		} else {
			flattened[name] = data;
		}

		return flattened;
	}

	$.fn.dataTable.ext.buttons.download = {
		text: 'Download',
		action: function(e, dt, node, config) {
			// Gather information to be submitted
			var data = {};

			if (dt.page.info().serverSide) {
				$.extend(data, dt.ajax.params());
			}

			if (typeof config.data === 'function') {
				config.data(data);
			} else if (typeof config.data === 'object') {
				$.extend(data, config.data);
			}

			// Convert data to a flat structure for submission
			var flattened = flattenJson(data);

			// Create an iframe
			var iframe = $('<iframe/>')
				.css({
					border: 'none',
					height: 0,
					width: 0
				})
				.appendTo(document.body);

			var contentDoc = iframe[0].contentWindow.document;
			contentDoc.open();
			contentDoc.close();

			var form = $('<form/>', contentDoc)
				.attr('method', config.type)
				.attr('action', config.url)
				.appendTo(contentDoc.body);

			$.each(flattened, function(name, val) {
				$('<input/>', contentDoc)
					.attr('type', 'text')
					.attr('name', name)
					.attr('autocomplete', 'no')
					.val(val)
					.appendTo(form);
			});

			form.submit();
		},
		url: '',
		type: 'POST',
		data: {}
	};
})(window, document, jQuery);
