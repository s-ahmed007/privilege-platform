@include('admin.production.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>
<style>
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        color: #337ab7 !important;background-color: #eee !important}
    .dataTables_wrapper .dataTables_paginate .paginate_button{
        color: #337ab7 !important; background-color: #ffffff !important}
    .dataTables_wrapper .dataTables_paginate .paginate_button.current{
        color: #ffffff !important; background-color: #337ab7 !important}
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover{
        color: #ffffff !important; background-color: #337ab7 !important}
</style>
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Sent Message History ({{$tab_title}})</h3>
            <a class="btn btn-all" href="{{url('admin/sent_message_history/all')}}">All</a>
            <a class="btn btn-expired" href="{{url('admin/sent_message_history/sms')}}">SMS</a>
            <a class="btn btn-premium" href="{{url('admin/sent_message_history/push')}}">Push Notification</a>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="table-responsive">
                    @if($history)
                        <table id="transactionList" class="table table-bordered table-hover table-striped projects">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Time</th>
                                @if($tab_title != 'SMS')
                                    <th>Title</th>
                                @endif
                                <th>Body</th>
                                <th>To</th>
                                <th>Language</th>
                                <th>Type</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i=1; ?>
                            @foreach ($history as $key => $value)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ date("M d, Y h:i A", strtotime($value->updated_at)) }}</td>
                                    @if($tab_title != 'SMS')
                                        <td>{{ $value->title }}</td>
                                    @endif
                                    <td>{{ $value->body }}</td>
                                    <td>{{ $value->to }}</td>
                                    <td>{{ $value->language }}</td>
                                    <td>
                                        @if($value->type == \App\Http\Controllers\Enum\SentMessageType::sms)
                                            SMS
                                        @else
                                            Push Notification
                                        @endif
                                    </td>
                                </tr>
                                <?php $i++; ?>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div style="font-size: 1.4em; color: red;">
                            {{ 'No data found.' }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@include('admin.production.footer')
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#transactionList').DataTable({
            "paging": true,
            "order": [],
        });
    });
</script>