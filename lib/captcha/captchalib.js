//$Id: captchalib.js 41603 2012-05-27 15:57:46Z jonnybradley $

jQuery(document).ready(function() {
	jQuery('#captchaRegenerate').click(function() {
		generateCaptcha();
		$("#antibotcode").focus();
		return false;
	});
});

function generateCaptcha() {
	jQuery('#captchaImg').attr('src', 'img/spinner.gif').show();
	jQuery('body').css('cursor', 'progress');
	jQuery.ajax({
		url: 'antibot.php',
		dataType: 'json',
		success: function(data) {
			jQuery('#captchaImg').attr('src', data.captchaImgPath);
			jQuery('#captchaId').attr('value', data.captchaId);
			jQuery('body').css('cursor', 'auto');
		}
	});
}
