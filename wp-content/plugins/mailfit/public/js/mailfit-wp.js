function mailfitAutoFrameHeight() {
    var iFrameID = document.getElementById('mailfit-wp-page-frame');
    if(iFrameID && iFrameID.contentWindow.document.body) {
        var iframe = jQuery('#mailfit-wp-page-frame'); // or some other selector to get the iframe
        
        // jQuery('#mailfit-wp-page-frame').css('height', '');
        // var new_height = iframe.contents().find('.bottom_hook').offset().bottom; //(jQuery('#mailfit-wp-page-frame').contents().height()) + "px";
        var new_height = (jQuery('#mailfit-wp-page-frame').contents().height()) + "px";
        jQuery('#mailfit-wp-page-frame').css('height', new_height);
        
        console.log(iframe.contents().find('.bottom_hook').offset().bottom);
    }
}

function getCurrentUrl() {
    return atob(window.location.hash.replace('#', ''));
}

jQuery(function() {
    var current_url = getCurrentUrl();
    if(current_url) {
        jQuery('iframe#mailfit-wp-page-frame').attr('src', current_url);
    }
    
    jQuery('iframe#mailfit-wp-page-frame').load(function() {
        mailfitAutoFrameHeight();
        jQuery("html, body").animate({ scrollTop: 0 });
    });
    
    setInterval(mailfitAutoFrameHeight, 700);
});