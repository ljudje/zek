var Cookies =
{
	run: function ()
	{
		cc.checkapproval();
		cc.reloadmodal ();
		$(document).on('change', "select[id^='cc-preference-selector-']", function () {
			jQuery.each(cc.cookies, function(key, value) {
				thisval = jQuery('#cc-preference-selector-'+key).val();
				if(key == "necessary")
				{
					thisval = "yes";
				}

				if(thisval == "no")
				{
					cc.cookies[key].approved = false;
					cc.approved[key] = "no";
					cc.setcookie('cc_'+key, cc.approved[key], 365);
				} else if(thisval == "yes") {
					cc.cookies[key].approved = true;
					cc.approved[key] = "yes";
					cc.setcookie('cc_'+key, cc.approved[key], 365);
				} else {
					cc.cookies[key].approved = false;
					cc.deletecookie(key);
					delete cc.approved[key];
				}
				cc.cookies[key].asked = false;

			});
			cc.checkapproval();
			cc.reloadifnecessary ();
		});

		if ($('table#cookietable').is('*')) {
			var response = $.parseJSON (cc.settings.cookieList);
			if (typeof response =='object') {
				$.each (response, function (i) {
					var item = response[i];
					$('table#cookietable').append ('<tr><td>' + item.name + '</td><td>' + item.duration + '</td><td>' + item.desc + '</td></tr>');
				});
			}
		}
	}
}
StartUp (Cookies);
