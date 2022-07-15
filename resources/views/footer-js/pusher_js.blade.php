<?php
use App\Http\Controllers\functionController;
use App\Http\Controllers\pusherController;
?>
<script>
    var app_key = '{{env("PUSHER_APP_KEY")}}';
    var app_cluster = '{{env("PUSHER_APP_CLUSTER")}}';
    var pusher = new Pusher(app_key, {
        cluster: app_cluster,
        forceTLS: true
    });
    function get_and_append_notification(customer_id) {
        var url = "{{url('/customer_notification_view_for_pusher')}}";
        $.ajax({
            type: "POST",
            url: url,
            data: {'_token': '<?php echo csrf_token(); ?>', 'customer_id': customer_id},
            success: function (data) {
                // show new notifications to the specific user
                $("#customer_notifications").hide().html(data).fadeIn('slow');
            }
        });
    }

    var channel = pusher.subscribe('like');
    channel.bind('like-review', function(response) {
        var data = response['data'];
        var liker_id = '<?php if(session('customer_id')){echo session('customer_id');}elseif(session('partner_id')){echo session('partner_id');} ?>';
        if(data['liker_id'] === liker_id){//change only this specific browser
            // add animation to like button
            $("#principalSelect-" + data['review_id']).html('<i class="love-f-icon"></i>');
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
        //get customer id from session
        var customer_id = "<?php echo session('customer_id'); ?>";
        if(data['customer_id'] === customer_id){
            get_and_append_notification(data['customer_id']);
        }
    });

    var channel1 = pusher.subscribe('append');
    channel1.bind('append_notification', function(response) {
        var data = response['data'];
        var liker_id = '<?php if(session('customer_id')){echo session('customer_id');}elseif(session('partner_id')){echo session('partner_id');} ?>';
        if(data['liker_id'] === liker_id){
            // add animation to like button
            $("#principalSelect-" + data['review_id']).html('<i class="love-e-icon"></i>');
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
        //get customer id from session
        var customer_id = "<?php echo session('customer_id'); ?>";
        if(data['customer_id'] === customer_id){
            get_and_append_notification(data['customer_id']);
        }
    });

    var channel2 = pusher.subscribe('offer');
    channel2.bind('offer-availed', function(response) {
        //get customer id from session
        var customer_id = "<?php echo session('customer_id'); ?>";
        if(response.data == customer_id){
            // show new notifications to the specific user
            get_and_append_notification(customer_id);
        }
    });

    var channel3 = pusher.subscribe('refer');
    channel3.bind('refer-used', function(response) {
        //get customer id from session
        var customer_id = "<?php echo session('customer_id'); ?>";
        if(response['data'] === customer_id){
            // show new notifications to the specific user
            get_and_append_notification(customer_id);
        }
    });

    var channel4 = pusher.subscribe('reward');
    channel4.bind('reward_availed', function(response) {
        //get customer id from session
        var customer_id = "<?php echo session('customer_id'); ?>";
        if(response['data'] === customer_id){
            // show new notifications to the specific user
            get_and_append_notification(customer_id);
        }
    });

    var channel5 = pusher.subscribe('review-reply');
    channel5.bind('partner-review-reply', function(response) {
        //get customer id from session
        var customer_id = "<?php echo session('customer_id'); ?>";
        if(response['data'] === customer_id){
            // show new notifications to the specific user
            get_and_append_notification(customer_id);
        }
    });
</script>