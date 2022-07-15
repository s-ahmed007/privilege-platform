var base_url = window.location.origin;
var csrf_token = $('meta[name="csrf-token"]').attr("content");

$(document).on('click', '.like-review', function () {
   //initiate url
   var like_url = base_url + "/" + "partner/branch/like_review";
   //get value to pass
   var value = this.value;
   $.ajax({
       type: "POST",
       url: like_url,
       data: {'_token': csrf_token, 'review_id': value},
       success: function (data) {
           // console.log('like review successful');
           // nothing
       }
   });
});
$(document).on('click', '.unlike-review', function () {
    var source_id = $(this).attr('data-source');
   //initiate url
   var like_url = base_url + "/" + "partner/branch/unlike_review";
   //get value to pass
   var value = this.value;
   $.ajax({
       type: "POST",
       url: like_url,
       data: {'_token': csrf_token, 'review_id': value, 'source_id': source_id},
       success: function (data) {
           console.log('unlike review successful');
           // nothing
       }
   });
});

//reply character limit
function replyChars(i) {
    var no_of_chars = $("#review"+i).val();
    $("#charNum"+i).text(no_of_chars.length+'/500');
}
//review liker list
function getReviewLikerList(review_id) {
    var url = base_url + "/" + "review_liker_list";
    $.ajax({
        type: "POST",
        url: url,
        headers: {"X-CSRF-TOKEN": csrf_token},
        data: {'_token': csrf_token, 'review_id' : review_id},
        success: function (data) {
            if(data.length !== 0){
                $("#likerModal").modal('toggle');
                var output = '';
                var i;
                for (i = 0; i < data.length; i++) {
                    output += "<li class='liker'>";
                    output += "<img class='liker_img' src='"+data[i]['liker_image']+"' width='100px' height='100px' alt='Profile Image'>";
                    output += "<p class='liker_name'>"+data[i]['liker_name']+"</p>";
                    output += "</li>";
                }
                $(".likerList").hide().html(output).fadeIn('slow');
            }
        }
    });
}
//reply edit
function editReviewReply(reply_id) {
    'use strict';
    $('.review_reply_'+reply_id).hide();
    $('.reply_box_'+reply_id).show();
}
//reply edit cancel
function cancelReviewReply(reply_id) {
    'use strict';
    $('.review_reply_'+reply_id).show();
    $('.reply_box_'+reply_id).hide();
}