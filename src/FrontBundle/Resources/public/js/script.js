$(document).ready(function() {
    $('#sidebarCollapse').click(function(e) {
        e.stopPropagation();
        $('#sidebar').toggleClass('active');
        $('#overlay').toggleClass('active');
    });
    $('body').click(function(e) {
        if ($('#sidebar').hasClass('active')) {
            $("#sidebar").toggleClass('active')
            $('#overlay').toggleClass('active');
        }
    })
    $('.carousel').carousel();
});
