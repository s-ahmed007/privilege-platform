@include('admin.production.header')
<div class="right_col" role="main">
    <div class="page-title">
        @if(session('error'))
            <div class="alert alert-danger">
                {{session('error')}}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="title_left">
            <h3>Edit Scheduled Notification</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="panel-body col-sm-10">
        <form action="{{ url('admin/update_scheduled_notification/'. $notification->id) }}" class="form-horizontal"
              method="post">

            <div class="form-group">
                <label class="control-label">Send to:</label>
                <label for="customer_type"></label>
                <select class="browser-default custom-select" name="customer_type" id="customer_type"
                        style="display: block;
                                        width: 100%;
                                        margin: 5px 0px 10px 0;
                                        padding: 5px 0px 5px 0;
                                        border: 1px solid #ccc;">
                    <option {{$notification->to == \App\Http\Controllers\Enum\SentMessageType::ALL_MEMBERS ? 'selected' : ''}}
                            value="all">All Members</option>
                    <option {{$notification->to == \App\Http\Controllers\Enum\SentMessageType::ALL_GUESTS? 'selected' : ''}}
                            value="3">Guest Users</option>
                    <option {{$notification->to == \App\Http\Controllers\Enum\SentMessageType::ALL_PREMIUMS ? 'selected' : ''}}
                            value="2">Royalty Members</option>
                    <option {{$notification->to == \App\Http\Controllers\Enum\SentMessageType::ALL_EXPIRED ? 'selected' : ''}}
                            value="4">Expired Members</option>
                    <option {{$notification->to == \App\Http\Controllers\Enum\SentMessageType::ALL_ACTIVE ? 'selected' : ''}}
                            value="5">Active Users</option>
                    <option {{$notification->to == \App\Http\Controllers\Enum\SentMessageType::ALL_INACTIVE ? 'selected' : ''}}
                            value="6">Inactive Users</option>
                    <option {{$notification->to == \App\Http\Controllers\Enum\SentMessageType::ALL_EXPIRED_TRIAL ? 'selected' : ''}}
                            value="7">Expired Trial Members</option>
                    <option {{$notification->to == \App\Http\Controllers\Enum\SentMessageType::ALL_EXPIRED_PREMIUM ? 'selected' : ''}}
                            value="8">Expired Premium Members</option>
                    <option {{$notification->to == \App\Http\Controllers\Enum\SentMessageType::ALL_EXPIRING_MEMBERS ? 'selected' : ''}}
                            value="9">Expiring Members</option>
                    <option {{$notification->to == \App\Http\Controllers\Enum\SentMessageType::ALL_SCANNERS ? 'selected' : ''}}
                            value="scanner">All Scanners</option>
                </select>
            </div>

            <div class="form-group">
                <label for="title">Title: </label>
                <input type="text" name="title" class="form-control" id="title" required onkeyup="countTitle();"
                       value="{{ $notification->title }}" placeholder="Title">

            </div>
            <p class="center"> Characters Count :
                <span id="titleNum">0</span> /58
            </p>
            <div class="form-group">
                <label for="message">Body: </label>
                <input type="text" name="body" class="form-control" id="message" required onkeyup="countMessage();"
                       value="{{ $notification->body }}" placeholder="Body">
            </div>
            <p class="center"> Characters Count :
                <span id="messageNum">0</span> /58
            </p>
            <div class="form-group">
                <label for="scheduled_at">Scheduled at: </label>
                <input type="datetime-local" name="scheduled_at" class="form-control" id="scheduled_at" required
                       value="{{ date('Y-m-d\TH:i:s', strtotime($notification->scheduled_at))}}"  min="{{date('Y-m-d\TH:i')}}">
                <p style="color:red">Set minute to 00</p>
            </div>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="form-group">
                    <button type="submit" class="btn btn-activate pull-right">Submit</button>
            </div>
        </form>
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
    countTitle();
    countMessage();
</script>