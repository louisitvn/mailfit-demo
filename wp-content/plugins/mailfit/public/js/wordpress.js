function setCurrentUrl() {
    parent.window.location.hash = '#' + btoa(window.location.href);
}

$(function() {
    // for mailfit wp
    $('.pml-table-container').bind("DOMSubtreeModified",function(){
        parent.mailfitAutoFrameHeight(); 
    });
    
    setCurrentUrl();
});