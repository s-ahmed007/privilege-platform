<?php
use App\Http\Controllers\functionController;
?>
<script>
    //Remember to replace key and cluster with your credentials.
    var pusher = new Pusher('cd1798cb19ea196ecaf2', {
        cluster: 'ap2',
        encrypted: true
    });
    //Also remember to change channel and event name if your's are different.
    //*******************************channel & event for live like notification*****************************************
    var channel1 = pusher.subscribe('like');
    channel1.bind('like-event', function(data) {
        var liker_id = <?php if(session('customer_id')){echo session('customer_id');}elseif(session('partner_id')){echo session('partner_id');} ?>;
        if(data['likeInfo']['liker_id'] == liker_id){//change only this specific browser
            // add animation to like button
            $("#principalSelect-" + data['likeInfo']['review_id']).html('<i class="love-f-icon"></i>');
            $("#principalSelect-" + data['likeInfo']['review_id']).children('.love-f-icon').addClass('animate-like');

            $("#principalSelect-" + data['likeInfo']['review_id']).addClass('unlike-review').removeClass('like-review');
            $("#principalSelect-" + data['likeInfo']['review_id']).attr("data-source", data['likeInfo']['source_id']);
        }
        //update total like number of this review
        if (document.getElementById('likes_of_review_' + data['likeInfo']['review_id'])) {
            var like_text = ( data['likeInfo']['total_likes_of_a_review'] > 1 ? " likes" : " like");
            document.getElementById('likes_of_review_' + data['likeInfo']['review_id']).innerHTML =
                data['likeInfo']['total_likes_of_a_review'] +like_text;
        }
        // update total like number of a user
        if($(".likes_of_user_"+data['likeInfo']['customer_id'])[0]){
            var className = document.getElementsByClassName('likes_of_user_'+data['likeInfo']['customer_id']);
            for(var i = 0; i < className.length; i++){
                className[i].innerHTML = "";
                className[i].append(' '+data['likeInfo']['total_likes_of_a_user']);
            }
        }
        //get customer id from session
        var customer_id = "<?php echo session('customer_id'); ?>";
        if(data['likeInfo']['customer_id'] === customer_id){
            // show new notifications to the specific user
            $("#customer_notifications").hide().html(data['unseenNotification']).fadeIn('slow');
            <?php
                if(session('customer_id')){
                    $allNotifications = (new functionController)->allNotifications(session('customer_id'));
                    session(['customerAllNotifications' => $allNotifications]);
                }
            ?>
        }else{
            //do nothing
        }
    });
    //****************************channel & event for live like notification ends***************************************

    //****************************channel & event for live discount notification****************************************
    var channel2 = pusher.subscribe('discount');
    channel2.bind('discount-event', function(data) {
        //get customer id from session
        var customer_id = "<?php echo session('customer_id'); ?>";
        if(data['customer_id'] === customer_id){
            // show new notifications to the specific user
            $("#customer_notifications").hide().html(data['unseenNotification']).fadeIn('slow');
        }else{
            //do nothing
        }
    });
    //****************************channel & event for live discount notification ends***********************************

    //******************************channel & event for live reply notification*****************************************
    var channel3 = pusher.subscribe('reply');
    channel3.bind('reply-event', function(data) {
        //get customer id from session
        var customer_id = "<?php echo session('customer_id'); ?>";
        if(data['customer_id'] === customer_id){
            // show new notifications to the specific user
            $("#customer_notifications").hide().html(data['unseenNotification']).fadeIn('slow');
        }else{
            //do nothing
        }
    });
    //****************************channel & event for live reply notification ends**************************************

    //*****************************channel & event for live follow notification*****************************************
    var channel4 = pusher.subscribe('follow');
    channel4.bind('follow-event', function(data) {
        //hide follow button
        $('.follow-user-' + data['customer_id']).hide();
        //document from class returns a json. for this i had to do so
        var className = document.getElementsByClassName('follow-requested-' + data['customer_id']);
        for (var idx = 0; idx < className.length; idx++) {
            className[idx].style.display = 'unset';
            className[idx].style.visibility = 'visible';
        }
        //get customer id from session
        var customer_id = "<?php echo session('customer_id'); ?>";
        if(data['customer_id'] === customer_id){
            // show new notifications to the specific user
            $("#customer_notifications").hide().html(data['unseenNotification']).fadeIn('slow');
        }else{
            //do nothing
        }
    });
    //****************************channel & event for live follow notification ends*************************************

    //****************************channel & event for live accept follow request notification***************************
    var channel5 = pusher.subscribe('acceptFollowRequest');
    channel5.bind('acceptFollowRequest-event', function(data) {
        //get customer id from session
        var customer_id = "<?php echo session('customer_id'); ?>";
        if(data['customer_id'] === customer_id){
            // show new notifications to the specific user
            $("#customer_notifications").hide().html(data['unseenNotification']).fadeIn('slow');
        }else{
            //do nothing
        }
    });
    //****************************channel & event for live accept follow request notification ends**********************

    //****************************channel & event for live birthday notification****************************************
    var channel6 = pusher.subscribe('birthday');
    channel6.bind('birthday-event', function(data) {
        //get customer id from session
        var customer_id = "<?php echo session('customer_id'); ?>";
        if(data['customer_id'] === customer_id){
            console.log('logged in');
            // show new notifications to the specific user
            $("#customer_notifications").hide().html(data['unseenNotification']).fadeIn('slow');
        }else{
            console.log('not logged in');
            //do nothing
        }
    });
    //****************************channel & event for live birthday notification ends***********************************

    //*******************************channel & event for live refer notification****************************************
    var channel7 = pusher.subscribe('refer');
    channel7.bind('refer-event', function(data) {
        //get customer id from session
        var customer_id = "<?php echo session('customer_id'); ?>";
        if(data['customer_id'] === customer_id){
            console.log('logged in');
            // show new notifications to the specific user
            $("#customer_notifications").hide().html(data['unseenNotification']).fadeIn('slow');
        }else{
            console.log('not logged in');
            //do nothing
        }
    });
    //*******************************channel & event for live refer notification ends***********************************

    //*******************************channel & event for live refer notification****************************************
    var channel8 = pusher.subscribe('250tkCoupon');
    channel8.bind('referCoupon-event', function(data) {
        //get customer id from session
        var customer_id = "<?php echo session('customer_id'); ?>";
        if(data['customer_id'] === customer_id){
            console.log('logged in');
            // show new notifications to the specific user
            $("#customer_notifications").hide().html(data['unseenNotification']).fadeIn('slow');
        }else{
            console.log('not logged in');
            //do nothing
        }
    });
    //*******************************channel & event for live refer notification ends***********************************

    //****************************channel & event for live partner follow notification**********************************
    var channel9 = pusher.subscribe('partnerFollow');
    channel9.bind('partnerFollow-event', function(data) {
        // update total followers number
        if (document.getElementById('total_followers_of_partner')) {
            if(data['total_followers'] > 1){
                var followers = data['total_followers'] + ' Followers';
            }else{
                var followers = data['total_followers'] + ' Follower';
            }
            document.getElementById('total_followers_of_partner').innerHTML = followers;
        }
        //update followers list of this partner
        if (document.getElementById('followersModal')) {
            document.getElementById('followersModal').innerHTML = data['followers_list'];
        }
        //get partner id from session
        var partner_id = "<?php echo session('partner_id'); ?>";
        if(data['partner_id'] === partner_id){
            console.log('logged in');
            // show new notifications to the specific user
            $("#partner_notifications").hide().html(data['unseenNotification']).fadeIn('slow');
        }else{
            console.log('not logged in');
            //do nothing
        }
    });
    //**************************channel & event for live partner follow notification ends*******************************

    //****************************channel & event for live create review notification***********************************
    var channel10 = pusher.subscribe('createReview');
    channel10.bind('createReview-event', function(data) {
        //get partner id from session
        var partner_id = "<?php echo session('partner_id'); ?>";
        if(data['partner_id'] === partner_id){
            console.log('logged in');
            // show new notifications to the specific user
            $("#partner_notifications").hide().html(data['unseenNotification']).fadeIn('slow');
        }else{
            console.log('not logged in');
            //do nothing
        }
    });
    //****************************channel & event for live partner follow notification ends*****************************

    //****************************channel & event for live post like notification***************************************
    var channel11 = pusher.subscribe('postLike');
    channel11.bind('postLike-event', function(data) {
        //get partner id from session
        var partner_id = "<?php echo session('partner_id'); ?>";
        if(data['partner_id'] === partner_id){
            console.log('logged in');
            // show new notifications to the specific user
            $("#partner_notifications").hide().html(data['unseenNotification']).fadeIn('slow');
            <?php
            if(session('partner_id')){
                $allNotifications = (new functionController)->partnerAllNotifications(Session::get('partner_id'));
                session(['partnerAllNotifications' => $allNotifications]);
            }
            ?>
        }else{
            console.log('not logged in');
            //do nothing
        }
    });
    //****************************channel & event for live partner follow notification ends*****************************

    //**************channel & event for live user logout of guest user when deleted/deactivated from rbd admin**********
    var channel12 = pusher.subscribe('userLogout');
    channel12.bind('userLogout-event', function(data) {
        //get customer id from session
        var customer_id = "<?php echo session('customer_id'); ?>";
        if(data === customer_id){
            console.log('logged in');
            //logout user forcefully
            window.location.href = '<?php echo url('customer_logout'); ?>';
        }else{
            console.log('not logged in');
            //do nothing
        }
    });
    //****************************channel & event for live partner follow notification ends*****************************

    //******************channel & event for live partner logout when deleted/deactivated from rbd admin*****************
    var channel13 = pusher.subscribe('partnerLogout');
    channel13.bind('partnerLogout-event', function(data) {
        //get partner id from session
        var partner_id = "<?php echo session('partner_id'); ?>";
        if(data === partner_id){
            console.log('logged in');
            //logout user forcefully
            window.location.href = '<?php echo url('partner_logout'); ?>';
        }else{
            console.log('not logged in');
            //do nothing
        }
    });
    //****************************channel & event for live partner follow notification ends*****************************

    //****************channel & event to append notification after unlike review or something like that*****************
    var channel14 = pusher.subscribe('append');
    channel14.bind('appendNotification-event', function(data) {
        var liker_id = <?php if(session('customer_id')){echo session('customer_id');}elseif(session('partner_id')){echo session('partner_id');} ?>;
        if(data['info']['liker_id'] == liker_id) {//change only this specific browser
            // add animation to like button
            $("#principalSelect-" + data['info']['review_id']).html('<i class="love-e-icon"></i>');
            $("#principalSelect-" + data['info']['review_id']).addClass('like-review').removeClass('unlike-review');
        }
        //update total like number of this review
        if (document.getElementById('likes_of_review_' + data['info']['review_id'])) {
            var like_text = ( data['info']['total_likes_of_a_review'] > 1 ? " likes" : " like");
            document.getElementById('likes_of_review_' + data['info']['review_id']).innerHTML =
                data['info']['total_likes_of_a_review'] +like_text;
        }
        // update total like number of a user
        if($(".likes_of_user_"+data['info']['customer_id'])[0]){
            var className = document.getElementsByClassName('likes_of_user_'+data['info']['customer_id']);
            for(var i = 0; i < className.length; i++){
                className[i].innerHTML = "";
                className[i].append(' '+data['info']['total_likes_of_a_user']);
            }
        }

        //get customer id from session
        var customer_id = "<?php echo session('customer_id'); ?>";
        if(data['info']['customer_id'] === customer_id){
            console.log('logged in');
            // show new notifications to the specific user
            $("#customer_notifications").hide().html(data['customerNotification']).fadeIn('slow');
            <?php
            if(session('customer_id')){
                $allNotifications = (new functionController)->allNotifications(session('customer_id'));
                session(['customerAllNotifications' => $allNotifications]);
            }
            ?>
        }else{
            console.log('not logged in');
            //do nothing
        }
    });
    //*************channel & event to append notification after unlike review or something like that ends***************

    //****************channel & event to append notification after unlike post or something like that*****************
    var channel15 = pusher.subscribe('appendPartnerNotification');
    channel15.bind('appendPartnerNotification-event', function(data) {
        //get partner id from session
        var partner_id = "<?php echo session('partner_id'); ?>";
        if(data['partner_id'] === partner_id){
            console.log('logged in');
            // show new notifications to the specific user
            $("#partner_notifications").hide().html(data['partnerNotification']).fadeIn('slow');
            <?php
            if(session('partner_id')){
                $allNotifications = (new functionController)->partnerAllNotifications(Session::get('partner_id'));
                session(['partnerAllNotifications' => $allNotifications]);
            }
            ?>
        }else{
            console.log('not logged in');
            //do nothing
        }
    });
    //*************channel & event to append notification after unlike post or something like that ends***************
</script>