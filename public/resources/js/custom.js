jQuery(document).ready(function($){
    // Get current path and find target link
    var path = window.location.pathname.split("/").pop();
    
    // Account for home page with empty path
    // if ( path == '' ) {
    //   path = 'index.php';
    // }
        alert(path)
    var target = $('.nav-item li a[href="'+path+'"]');
    // Add active class to target link
    target.addClass('active');
  });



// $(function() {
//     $('.nav-item li a a[href^="/' + location.pathname.split("/")[1] + '"]').addClass('active');
//   });

// (function () {
//     var current = location.pathname.split('/')[1];
//     if (current === "") return;
//     var menuItems = document.querySelectorAll('.nav-item li a');
//     for (var i = 0, len = menuItems.length; i < len; i++) {
//         if (menuItems[i].getAttribute("href").indexOf(current) !== -1) {
//             menuItems[i].className += "active";
//         }
//     }
// })();
