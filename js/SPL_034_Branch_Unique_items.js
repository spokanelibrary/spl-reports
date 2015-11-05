//alert('hello!');

jQuery.post(
 // see tip #1 for how we declare global javascript variables
 MyAjax.ajaxurl,
 {
 // here we declare the parameters to send along with the request
 // this means the following action hooks will be fired:
 // wp_ajax_nopriv_myajax-submit and wp_ajax_myajax-submit
 action : 'myajax-submit',
 
 // other parameters can be added along with "action"
 postID : MyAjax.postID
 },
 function( response ) {
 alert( response );
 }
);