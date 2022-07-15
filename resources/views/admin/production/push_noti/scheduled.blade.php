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
        @if(session('success'))
            <div class="alert alert-success">
                {{session('success')}}
            </div>
        @endif
        <div class="title_left">
            <h3>Scheduled Push Notifications</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="table-responsive">
                    @if($notifications)
                        <table id="transactionList" class="table table-bordered table-hover table-striped projects">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Time</th>
                                <th>Title</th>
                                <th>Body</th>
                                <th>To</th>
                                <th>Language</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i=1; ?>
                            @foreach ($notifications as $key => $value)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ date("M d, Y h:i A", strtotime($value->updated_at)) }}<br>
                                        <b>Scheduled at: {{date("M d, Y h:i A", strtotime($value->scheduled_at))}}</b>
                                    </td>
                                    <td>{{ $value->title }}</td>
                                    <td>{{ $value->body }}</td>
                                    <td>{{ $value->to }}</td>
                                    <td>{{ $value->language }}</td>
                                    <td>
                                        <a href="{{url('admin/edit_scheduled_notification/'.$value->id)}}" class="btn btn-primary">
                                            <i class="fa fa-edit"></i>
                                        </a>
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