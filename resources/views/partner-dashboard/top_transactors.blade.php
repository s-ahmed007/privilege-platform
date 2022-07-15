@include('partner-dashboard.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>
<style>
    .dataTables_length label select{
        padding: 5px 12px;
        background-color: #fff;
        border-radius: 2px;
        border: 1px solid #e4e7ea
    }
</style>
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-5 col-md-4 col-sm-4 col-xs-12">
            <h3 class="d-inline-block">{{__('partner/dashboard.top_customers')}}</h3>
        </div>
    </div>
    <div class="title_right">
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
    </div>
    <!-- /.row -->
    <!-- .row -->
    <div class="row">
        <div class="col-md-12 col-xs-12">
            @if(count($top_transactors) > 0)
                <div class="panel">
                    <div class="sk-chat-widgets">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <ul class="chatonline">
                                    <li style="float: right;">{{__('partner/dashboard.scan')}}</li>
                                    @foreach($top_transactors as $user)
                                        <li>
                                            <a style="cursor: default;">
                                                <span class="customer-scan-count">{{$user->transaction_count}}</span>
                                                <img src="{{$user->customer_profile_image}}" alt="user-img" class="img-circle">
                                                <span class="customer-name">{{$user->customer_full_name}}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <p>{{__('partner/common.no_user_found')}}</p>
            @endif
        </div>
    </div>
    <!-- /.row -->
</div>
<!-- /.container-fluid -->


@include('partner-dashboard.footer')
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">
    $(document).ready(function () {
        $('#transactionList').DataTable({
            //"paging": false
            "order": []
        });
    });
</script>