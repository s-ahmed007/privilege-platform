<script>
    var app_key = '{{env("PUSHER_APP_KEY")}}';
    var app_cluster = '{{env("PUSHER_APP_CLUSTER")}}';
    var pusher = new Pusher(app_key, {
        cluster: app_cluster,
        forceTLS: true
    });

    function get_and_append_notification(customer_id) {
        var url = "{{url('/partner/notification_view_for_pusher')}}";
        $.ajax({
            type: "POST",
            url: url,
            data: {'_token': '<?php echo csrf_token(); ?>', 'customer_id': customer_id},
            success: function (data) {
                var merchant_request_count = $(".merchant_request_count");
                if(data.unseen_notification === 0){
                    merchant_request_count.css('display', 'none');
                }else{
                    merchant_request_count.css('display', 'inline').addClass('notify_num').text(data.unseen_notification);
                }
                // show new notifications to the specific user
                $("#partner_notifications").hide().html(data.notification_view).fadeIn('slow');
            }
        });
    }

    function newTransactionRequests() {
        var url = "{{url('/partner/branch/transaction_requests')}}";
        $.ajax({
            type: "POST",
            url: url,
            data: {'_token': '<?php echo csrf_token(); ?>'},
            success: function (requests) {
                if(requests.length > 0){
                    var output = '';
                    requests.forEach(function(request) {
                        output += "<li class=\"list-group-item\">";
                        output += "<div class=\"row\">";
                        output += "<div class=\"col-md-10\">";
                        output += "<span class=\"relative_time\">"+moment(request.posted_on).format('lll')+"</span><br>";
                        output += "<img src=\""+request.image+"\" alt=\"Profile\" class=\"pro_pic\">";
                        output += "<span class=\"request-text\" style='margin-left: 5px'>";
                        if(request.request.redeem_id){
                            output += request.customer_name+" has requested a reward ("+request.request.offer.offer_description+"), Quantity: "+
                                request.request.redeem.quantity;
                        }else{
                            output += request.customer_name+" has requested a transaction ("+request.request.offer.offer_description+").";
                        }
                        output += "</span>";
                        output += "</div>";
                        output += "<div class=\"col-md-2\">";
                        output += "<div class=\"request_\""+request.source_id+">";
                        output += "<button class=\"btn btn-success action_btn notification_" +request.id+"\""+
                            " onclick=\"updateStatus('"+request.id+"', '"+request.source_id+"', '1', '"+request.posted_on+"')\">Accept</button>";
                        output += "<br><br>";
                        output += "<button class=\"btn btn-danger action_btn notification_" +request.id+"\""+
                            " onclick=\"updateStatus('"+request.id+"', '"+request.source_id+"', '2', '"+request.posted_on+"')\">Reject</button>";
                        output += "</div>";
                        output += "</div>";
                        output += "</div>";
                        output += "</li>";
                    });
                    $("#new_tran_requests")
                        .hide()
                        .html(output)
                        .fadeIn("slow");
                    $("#new_request_modal").modal("show");
                }else{
                    $("#new_request_modal").modal('toggle');
                }
            }
        });
    }

    var channel1 = pusher.subscribe('merchant_notification');
    channel1.bind('update_request_status', function(response) {
        var data = response['data'];
        var merchant_request_count = $(".merchant_request_count");
        var branch_id = '<?php echo session('branch_id'); ?>';
        if(data.branch_id == branch_id) {//change only this specific browser.
            //update specific request status
            if(data.request_id != null){
                var request = $(".request_"+data.request_id);
                if(request.length == 1){
                    if(data.status == 1){
                        request.empty().html('<span class="accepted">Accepted</span>')
                    }else{
                        request.empty().html('<span class="rejected">Rejected</span>')
                    }
                }
            }
            //new request
            if(data.request > 0){
                newTransactionRequests();
            }else{
                $("#new_request_modal").modal('hide');
            }
            if(data.notification_count == 0){
                merchant_request_count.css('display', 'none');
            }else{
                merchant_request_count.css('display', 'inline').addClass('notify_num').text(data.notification_count);
                get_and_append_notification(branch_id);
            }
        }
    });

    var channel2 = pusher.subscribe('like_post');
    channel2.bind('partner_noti_of_post_like', function(response) {
        var branch_id = '<?php echo session('branch_id'); ?>';
        if(response.data == branch_id) {//change only this specific browser.
            get_and_append_notification(branch_id);
        }
    });

    var channel3 = pusher.subscribe('like');
    channel3.bind('like-review', function(response) {
        var data = response['data'];
        var liker_id = '<?php if(session('branch_id')){echo session('branch_id');} ?>';
        if(data['liker_id'] == liker_id){//change only this specific browser
            // add animation to like button
            $("#principalSelect-" + data['review_id']).html('<i class="fa fa-heart"></i>');
            $("#principalSelect-" + data['review_id']).children('.love-f-icon').addClass('animate-like');

            $("#principalSelect-" + data['review_id']).addClass('unlike-review').removeClass('like-review');
            $("#principalSelect-" + data['review_id']).attr("data-source", data['source_id']);
        }
        //update total like number of this review
        if (document.getElementById('likes_of_review_' + data['review_id'])) {
            var like_text = ( data['total_likes_of_a_review'] > 1 ? " likes" : " like");
            document.getElementById('likes_of_review_' + data['review_id']).innerHTML =
                data['total_likes_of_a_review'] +like_text;
        }
        // update total like number of a user
        if($(".likes_of_user_"+data['customer_id'])[0]){
            var className = document.getElementsByClassName('likes_of_user_'+data['customer_id']);
            for(var i = 0; i < className.length; i++){
                className[i].innerHTML = "";
                className[i].append(' '+data['total_likes_of_a_user']);
            }
        }
    });

    //unlike review
    var channel4 = pusher.subscribe('append');
    channel4.bind('append_notification', function(response) {
        var data = response['data'];
        var liker_id = '<?php if(session('branch_id')){echo session('branch_id');} ?>';
        if(data['liker_id'] == liker_id){
            // add animation to like button
            $("#principalSelect-" + data['review_id']).html('<i class="fa fa-heart-o"></i>');
            $("#principalSelect-" + data['review_id']).addClass('like-review').removeClass('unlike-review');
        }
        //update total like number of this review
        if (document.getElementById('likes_of_review_' + data['review_id'])) {
            var like_text = ( data['total_likes_of_a_review'] > 1 ? " likes" : " like");
            document.getElementById('likes_of_review_' + data['review_id']).innerHTML =
                data['total_likes_of_a_review'] +like_text;
        }
        // update total like number of a user
        if($(".likes_of_user_"+data['customer_id'])[0]){
            var className = document.getElementsByClassName('likes_of_user_'+data['customer_id']);
            for(var i = 0; i < className.length; i++){
                className[i].innerHTML = "";
                className[i].append(' '+data['total_likes_of_a_user']);
            }
        }
    });

</script>