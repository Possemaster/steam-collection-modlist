document.addEventListener('DOMContentLoaded', function () {
	var button = document.getElementById('copy');
	var mods = document.getElementById('mods');
	if (!button || !mods) {
		return;
	}

	button.addEventListener('click', function () {
		mods.select();
		var done = function () { button.textContent = 'Copied!'; };
		if (navigator.clipboard && window.isSecureContext) {
			navigator.clipboard.writeText(mods.value).then(done, function () {
				alert('Could not copy automatically. The text is selected, press Ctrl+C to copy.');
			});
		} else if (document.execCommand('copy')) {
			done();
		} else {
			alert('Could not copy automatically. The text is selected, press Ctrl+C to copy.');
		}
	});
});
