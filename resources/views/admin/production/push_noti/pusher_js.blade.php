<script>
    var app_key = '{{ env("PUSHER_APP_KEY") }}';
    var app_cluster = '{{ env("PUSHER_APP_CLUSTER") }}';
    var pusher = new Pusher(app_key, {
        cluster: app_cluster,
        forceTLS: true
    });
</script>

@if(url()->current() == url('admin/all_notifications'))
    <script>
        var adminNotificationChannel = pusher.subscribe('admin_notification');
        adminNotificationChannel.bind('append_admin_notification', function(response) {
            var data = response['data'];
            var output = '';
            if(data){
                output += "<li class=\"row1\">";
                output += "<a href=\"#\">";
                output += "<div class=\"column\">";
                output += "<div class=\"card\" style=\"background-color: #87ceeb\">";
                output += "<h4>"+data.notification.text+"</h4>";
                output += "<span>"+data.formatted_date+"</span>";
                output += "</div>";
                output += "</div>";
                output += "</a>";
                output += "</li>";

                var h = document.getElementById("notification_list");
                h.insertAdjacentHTML("afterbegin", output);
            }
        });
    </script>
@endif

@if(url()->current() != url('admin/all_notifications'))
    <script>
        var adminNotificationChannel = pusher.subscribe('admin_notification');
        adminNotificationChannel.bind('append_admin_notification', function(response) {
            var data = response['data'];
            if(data) {
                $(".new_admin_notification").css('color', 'red');
                $("#admin_notification_number").addClass('notify_num').text(data.notification_count);
            }
        });
    </script>
@endif