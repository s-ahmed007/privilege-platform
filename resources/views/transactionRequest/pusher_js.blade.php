
<script>
    var app_key = '{{env("PUSHER_APP_KEY")}}';
    var app_cluster = '{{env("PUSHER_APP_CLUSTER")}}';
    var pusher = new Pusher(app_key, {
        cluster: app_cluster,
        forceTLS: true
    });

    var channel4 = pusher.subscribe('merchant_notification');
    channel4.bind('update_request_status', function(response) {
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
            // var output = '';
            if(data.notification_count == 0){
                merchant_request_count.css('display', 'none');
                // output += '<br><br>';
                // output += '<p>No request has been made yet.</p>\n' +
                //     '<p style="font-size: 0.9em">এখনও কোন অনুরোধ করা হয়নি।</p>';
            }else{
                merchant_request_count.css('display', 'inline').text(data.notification_count);
                // for (var i=0; i<requests.length; i++){
                //     output += '<li class="list-group-item">';
                //     output += '<span class="relative_time">'+requests[i].posted_on+'</span><br>\n';
                //     output += '<img src="'+requests[i].image+'" alt="Profile" class="pro_pic">\n';
                //     output += '<span class="request-text">\n' + requests[i].customer_name+
                //         'has requested ('+requests[i].request.offer.offer_description+')\n' +
                //         '</span>';
                //
                //     if(requests[i].request.status == 0){
                //         output += '<button class="btn btn-success action_btn notification_'+requests[i].id+'"';
                //         output += 'onclick="updateStatus(\''+requests[i].id+'\',\'' +requests[i].source_id+'\',\''+1+'\',\''+requests[i].posted_on+'\')"' +
                //             '>Accept</button>';
                //         output += '<button class="btn btn-danger action_btn notification_'+requests[i].id+'"';
                //         output += 'onclick="updateStatus(\''+requests[i].id+'\',\'' +requests[i].source_id+'\',\''+2+'\',\''+requests[i].posted_on+'\')"' +
                //             '>Reject</button>';
                //     }else if(requests[i].request.status == 1){
                //         output += '<span class="accepted">Accepted</span>';
                //     }else{
                //     output += '<span class="rejected">Rejected</span>';
                //     }
                //     output += '</li>';
                // }


            }
            // $(".all_pending_requests").html(output).fadeIn('slow');
        }
    });

</script>