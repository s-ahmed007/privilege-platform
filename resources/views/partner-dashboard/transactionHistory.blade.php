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
            <h3 class="d-inline-block">{{__('partner/common.all_transactions')}}</h3>
                <h5 class="d-inline-block float-right">{{__('partner/transactions.find_all_transactions_here')}}</h5>
        </div>
{{--        <a href="{{url('partner/branch/deal_purchased')}}" class="btn btn-primary">Deals Redeemed</a>--}}
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
            <form class="form-inline" action="{{url('partner/branch/sort_transaction_history')}}" method="post" class="mb">
                {{csrf_field()}}
                <div class="form-group">
                    <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
                    <select class="form-control" name="year" required="required">
                        <option value="" selected disabled>Select year</option>
                        @for($i = 2018; $i <= date('Y'); $i++)
                            <option value="{{$i}}" {{$i == $year ? 'selected' : ''}}>{{$i}}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
                    <select class="form-control" name="month">
                        <option value="">Select month</option>
                        <option value="01" {{$month == '01' ? 'selected' : ''}}>January</option>
                        <option value="02" {{$month == '02' ? 'selected' : ''}}>February</option>
                        <option value="03" {{$month == '03' ? 'selected' : ''}}>March</option>
                        <option value="04" {{$month == '04' ? 'selected' : ''}}>April</option>
                        <option value="05" {{$month == '05' ? 'selected' : ''}}>May</option>
                        <option value="06" {{$month == '06' ? 'selected' : ''}}>June</option>
                        <option value="07" {{$month == '07' ? 'selected' : ''}}>July</option>
                        <option value="08" {{$month == '08' ? 'selected' : ''}}>August</option>
                        <option value="09" {{$month == '09' ? 'selected' : ''}}>September</option>
                        <option value="10" {{$month == '10' ? 'selected' : ''}}>October</option>
                        <option value="11" {{$month == '11' ? 'selected' : ''}}>November</option>
                        <option value="12" {{$month == '12' ? 'selected' : ''}}>December</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-6 col-xs-offset-3"></label>
                    <button type="submit" class="btn btn-primary">Sort</button>
                </div>
            </form>
            <br>
            @if(count($transactions) > 0)
                <div class="table-responsive">
                    <table id="transactionList" class="table table-bordered table-hover table-striped projects">
                        <thead>
                        <tr>
                            <th>Image</th>
                            <th>Customer Name</th>
                            <th>Offer</th>
                            <th>Transacted By</th>
                            <th>Time</th>
                            {{-- <th class="d-none">Sort</th>--}}
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($transactions as $row)
                            <tr>
                                <td><img src="{{$row['customer_profile_image']}}" width="100%"
                                         class="pro_pic" alt="post-image"></td>
                                <td>{{ $row['customer_full_name'] }}</td>
                                <td>{{ $row['offer_description'] }}
                                    @if($row['offer_status'] != '')
                                        ({{$row['offer_status']}})
                                        <br>
                                        @if(isset($row['quantity']))
                                            Quantity: {{$row['quantity']}}
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if($row['full_name'] == null)
                                        {{ 'Royalty Admin' }}
                                    @else
                                        {{ $row['full_name'] }}
                                    @endif
                                </td>
                                <?php $posted_on=date("d F, Y", strtotime($row['posted_on'])); ?>
                                <td>{{ $posted_on }}</td>
{{--                                <td class="d-none">{{ date("Y F d", strtotime($row['posted_on'])) }}</td>--}}
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p>No transaction available for this month</p>
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