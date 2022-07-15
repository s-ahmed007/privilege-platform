// // This just toggles the follow/following of the button
// $('a.follow').click(function () {
//     $(this).toggleClass('followed');
//
//     if($(this).hasClass('followed')) {
//         $(this).text('Followed');
//         $('ul li:last-child').html('325<span>Followers</span>');
//     }
//     else {
//         $(this).text('Follow Nick');
//         $('ul li:last-child').html('324<span>Followers</span>');
//     }
// });

//follow customer with Javascript and AJAX
// $(document).on('click', '#follow-customer', function () {
//     var url = "https://royaltybd.com/follow-customer";
//     alert(url);
//     $.ajax({
//         type: "POST",
//         url: url,
//         data: {'_token': '<?php echo csrf_token(); ?>', 'id': this.value},
//         success: function (data) {
//             console.log(data);
//             //alert(data);
//         }
//     });
//
//     $(this).prop('disabled', true);
//
//     var $this = $(this);
//     $this.toggleClass('follow');
//     if ($this.hasClass('follow')) {
//         $this.text('FOLLOWING');
//     } else {
//         $this.text('FOLLOW');
//     }
// });






//
//
// $(function(){
//     $('a[title]').tooltip();
// });
