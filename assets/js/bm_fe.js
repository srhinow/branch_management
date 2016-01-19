$(document).ready(function()
{    
    // User-Agent-String auslesen
    var UserAgent = navigator.userAgent.toLowerCase();

    // User-Agent auf gewisse Schlüsselwörter prüfen
    if(UserAgent.search(/(iphone|ipod|opera mini|fennec|palm|blackberry|android|symbian|series60)/)>-1)
    {
		// mobiles Endgerät		
        $('body').addClass('mobile');
    }
    else
    {
		$('body').addClass('no-mobile');

    }

});
