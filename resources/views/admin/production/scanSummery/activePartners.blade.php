@include('admin.production.header')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css"/>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>

<div class="right_col" role="main">
    <div class="page-title">
        <h3>Branch Wise Scan Summary (Active{{$status != '' ? '->'.$status : $status}})</h3>
        <div class="title_left">
            <a href="{{ url('transactionList/active') }}" class="btn btn-success">Active Partners</a>
            <a href="{{ url('transactionList/inactive') }}" class="btn btn-warning">Inactive Partners</a>
            <br>
            <a href="{{ url('transactionList/active/current') }}" class="btn btn-primary btn-sm">Current</a>
            <a href="{{ url('transactionList/active/old') }}" class="btn btn-primary btn-sm">Old</a>

        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                @if($transactionList)
                    <!-- start project list -->
                        <table id="transactionList" class="table table-striped projects">
                            <thead>
                            <tr>
                                {{--<th>S/N</th>--}}
                                <th>Branch Name
                                    <i class="fa fa-exchange custom"></i>
                                </th>
                                <th><p class="center">Total Transactions</p></th>
                                {{--<th>Transaction Amount</th>
                                <th>Discount Availed</th>--}}
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($transactionList as $tranInfo)
                                <tr>
                                    {{--                                        <th>{{ $tranInfo['serial'] }}</th>--}}
                                    <td><span style="font-weight: bold;">{{ $tranInfo['name'] }}
                                            - {{ $tranInfo['area'] }}</span><br>
                                        {{substr($tranInfo['address'], 0,30).'...'}}
                                    </td>
                                    <td style="font-weight: bold; color: darkgreen " align="middle">{{ $tranInfo['no_of_tran'] }}
                                    {{-- {{$tranInfo['active']}} --}}
                                    </td>
                                    {{--<td>{{ $tranInfo['tot_amount'] }}</td>
                                    <td>{{ $tranInfo['tot_discount'] }}</td>--}}
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    @else
                        <div style="font-size: 1.4em; color: red;">
                            {{ 'No Branch found.' }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


@include('admin.production.footer')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">
    $(document).ready(function () {
        $('#transactionList').DataTable({
            //"paging": false
            "order": []
        });
    });
</script>
