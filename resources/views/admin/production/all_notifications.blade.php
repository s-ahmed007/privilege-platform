@include('admin.production.header')
<style>
    .column {float: left;width: 100%;padding: 0 10px;}

    /* Remove extra left and right margins, due to padding */
    .row1 {margin: 5px -5px;}

    /* Clear floats after the columns */
    .row1:after {content: "";display: table;clear: both;}
    /* Style the counter cards */
    .card {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        padding: 16px;
        text-align: left;
        background-color: #f1f1f1;
        font-size: 1rem;
    }
    ul{padding: 0;}
    ul li{list-style-type: none}
    .pdf_btn{float: right;font-size: 4rem;margin-right: 12px;cursor: pointer;}
</style>
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>All Notifications</h3>
        </div>
        <div class="title_right">
            @if(session('try_again'))
                <div class="alert alert-danger">
                    {{ session('try_again') }}
                </div>
            @endif
                <a class="pdf_btn text-danger" href="{{ url("/pdf/generate/activity-report?from_date=".$from.'&to_date='.$to)}}">
                    <i class="fas fa-file-pdf"></i>
                </a>
        </div>
    </div>

    <div class="clearfix"></div>
    <div class="container">
        <div class="row">
            <form action="{{url('admin/sort_all_notification')}}" method="post">
                {{csrf_field()}}
                <div class="col-md-4">
                    <?php $from_date = new DateTime($from); ?>
                    <label for="">From ({{date_format($from_date, 'm/d/Y')}})</label>
                    <span id="date_from" data-date-from="{{$from}}"></span>
                    <input type="date" name="from_date" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <?php $to_date = new DateTime($to); ?>
                    <label for="">To ({{date_format($to_date, 'm/d/Y')}})</label>
                    <span id="date_to" data-date-to="{{$to}}"></span>
                    <input type="date" name="to_date" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label></label>
                    <button type="submit" class="btn btn-primary form-control">Sort</button>
                </div>
            </form>
            <div class="col-xs-12">
                @if(count($notifications) != 0)
                    <ul id="notification_list">
                        @foreach($notifications as $notification)
                            <li class="row1">
                                @if($notification->type == \App\Http\Controllers\Enum\AdminNotificationType::review_under_moderation)
                                <a href="{{url('admin/pending_review_replies')}}" style="color: #007bff">
                                @endif
                                <div class="column">
                                    <div class="card">
                                        <h4>{{$notification->text}}</h4>
                                        <span>{{date("M d, Y h:i A ", strtotime($notification->created_at))}}</span>
                                    </div>
                                </div>
                                @if($notification->type == \App\Http\Controllers\Enum\AdminNotificationType::review_under_moderation)
                                </a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @else
                <!-- if no notification -->
                    <p>You don't have any notifications.</p>
                @endif
            </div>
        </div>
    </div>
</div>

@include('admin.production.footer')
<script>
    function generateReport() {
        var url = '{{ url("/pdf/generate/activity-report")}}';
        var from = $("#date_from").attr("data-date-from");
        var to = $("#date_to").attr("data-date-to");
        $.ajax({
            type: 'get',
            url: url,
            // send data to function through autocomplete route
            data: {'_token': '<?php echo csrf_token(); ?>', 'from': from, 'to': to},
            success: function (data) {
                //nothing
            }
        });
    }
</script>