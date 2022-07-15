@include('admin.production.header')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if (session('push_notification_successful'))
                <div class="alert alert-success">
                    {{ session('push_notification_successful') }}
                </div>
            @elseif (session('push_notification_fail'))
                <div class="alert alert-danger">
                    {{ session('push_notification_fail') }}
                </div>
            @endif
            <div class="alert alert-success schedule_set" style="display: none;">
                Scheduled successfully
            </div>
            <h3>Send Push Notifications</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <span id="progress_value"></span>
                    <div class="progress-bar-container" style="display: none;">
                        <div class="progress">
                            <div class="progress-bar progress-bar-success progress-bar-striped"
                                 role="progressbar" id="progressBar"
                                 aria-valuenow="70" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                    <form method="post" id="upload_form" enctype="multipart/form-data">
                    <div class="row">
                        <div class="form-group">
                          <label for="customer_type" class="control-label">Send to:</label>
                            <select class="form-control" name="customer_type" id="customer_type"
                                style="display: block;
                                    width: 100%;
                                    margin: 5px 0px 10px 0;
                                    padding: 5px 0px 5px 0;
                                    border: 1px solid #ccc;">
                                @if($user == 'customer')
                                    <option selected value="all">All Members</option>
                                    <option value="3">Guest Members</option>
                                    <option value="2">Royalty Members</option>
                                    <option value="4">Expired Members</option>
                                    <option value="5">Active Members</option>
                                    <option value="6">Inactive Members</option>
                                    <option value="7">Expired Trial Members</option>
                                    <option value="8">Expired Premium Members</option>
                                    <option value="9">Expiring Members</option>
                                @else
                                    <option selected value="scanner">All Scanners</option>
                                @endif
                            </select>
                       </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                          <label class="control-label">Title:</label>
                          <span style="color: #E74430;">
                                @if ($errors->getBag('default')->first('title'))
                                    {{ $errors->getBag('default')->first('title') }}
                                @endif
                           </span>
                            <textarea rows="1" name="title" onkeyup="countTitle();" id="title"
                                  class="form-control" placeholder="Write a message"
                                  required>{{old('title')}}</textarea>
                       </div>
                    </div>
                    <p class="center"> Characters Count :
                        <span id="titleNum">0</span> /58
                    </p>
                    <div class="row">
                        <div class="form-group">
                          <label class="control-label">Message:</label>
                          <span style="color: #E74430;">
                            @if ($errors->getBag('default')->first('message'))
                                {{ $errors->getBag('default')->first('message') }}
                            @endif
                          </span>
                            <textarea rows="2" name="message" onkeyup="countMessage();" id="message"
                                  class="form-control" placeholder="Write a message"
                                  required>{{old('message')}}</textarea>
                       </div>
                    </div>
                    <p class="center"> Characters Count :
                        <span id="messageNum">0</span> /58
                    </p>
                    <div class="row">
                        <div class="form-group">
                            <label class="control-label">Image (optional):</label>
                            <input type="file" name="image" id="push_image">
                            <p>Use Landscape Image(w:h=2:1), 1024*512px</p>
                            <p> If you insert image, then keep the title and body text 40-43 characters max.
</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="control-label">Schedule (optional):</label>
                            <input type="datetime-local" class="form-control" id="pushSchedule" name="pushSchedule"
                                   placeholder="2019-01-20 20:00:00 (Optional)" min="{{date('Y-m-d\TH:i')}}"/>
                            <p style="color:red">Set minute to 00</p>
                        </div>
                    </div>
                    </form>
                </div>
                <div class="ln_solid"></div>
                <div class="form-group">
                    <p class="center">
                        <button class="btn btn-activate pull-right" onclick="sendPushNotification()">Submit</button>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@include('admin.production.footer')
<script>
    function countTitle() {
        var no_of_chars = $("#title").val();
        $("#titleNum").text(no_of_chars.length);
    }
    function countMessage() {
        var no_of_chars = $("#message").val();
        $("#messageNum").text(no_of_chars.length);
    }

    function sendPushNotification() {
        var customer_type = $("#customer_type").val();
        var title = $("#title").val();
        var message = $("#message").val();
        var schedule = $("#pushSchedule").val();

        if (title == '' || message == '') {
            alert('Please set all values');
            return false;
        }
        if(schedule !== ''){
            if(new Date(schedule) < new Date()){
                alert('You can not set past time');
                return false;
            }
        }
        if (confirm('Are you sure?')){

            var url = "{{ url('/send-push-notification') }}";
            $.ajax({
                type: "POST",
                url: url,
                async: true,
                data:new FormData($("#upload_form")[0]),

                dataType:'json',
                processData: false,
                contentType: false,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},

                success: function (response) {
                    if(schedule === ''){
                        $(".progress-bar-container").css('display', 'block');
                        var data = response.f_tokens;
                        for (var i = 0; i < data.length; i++) {
                            var url = "{{ url('/sending-push-notification') }}";
                            $.ajax({
                                type: "POST",
                                url: url,
                                async: true,
                                data: {
                                    '_token': '<?php echo csrf_token(); ?>',
                                    'token': data[i],
                                    'title': title,
                                    'message': message,
                                    'image_url': response.image
                                },
                                success: function (result) {
                                    if (result) {
                                        var progress_value = 0;
                                        if (data.length == 1){
                                            progress_value = 100;
                                        } else{
                                            progress_value = Math.ceil((i / data.length) * 100);
                                        }
                                        $("#progressBar").css('width', progress_value + '%');
                                        $("#progress_value").text(progress_value + '%');
                                    }
                                }
                            });
                        }
                        //save this push message
                        saveSentMessage(customer_type, title, message, null, response.image);
                    }else{
                        //save this push message
                        saveSentMessage(customer_type, title, message, schedule, response.image);
                        $(".schedule_set").css('display', 'block');
                    }
                }
            });
        }
    }

    function saveSentMessage(customer_type, title, message, schedule, image_url) {
        $.ajax({
            type: "POST",
            url: "{{url('admin/saveSentMessage')}}",
            async: true,
            data: {
                '_token': '<?php echo csrf_token(); ?>',
                'customer_type': customer_type,
                'title': title,
                'message': message,
                'schedule': schedule,
                'image_url': image_url
            },
            success: function (res) {
                //nothing
            }
        });
    }

</script>