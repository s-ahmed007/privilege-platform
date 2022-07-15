@include('admin.production.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>{{$tab_title}}</h3>
            <a class="btn btn-all" href="{{url('admin/deals/purchased/all')}}">All</a>
            <a class="btn btn-trial" href="{{url('admin/deals/purchased/redeemed')}}">Redeemed</a>
            <a class="btn btn-expired" href="{{url('admin/deals/purchased/expired')}}">Expired</a>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    @if($result)
                        <table id="voucherList" class="table table-striped projects">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Customer Info</th>
                                <th>Deal Details</th>
                                <th>Merchant Details</th>
                                <!-- <th>Action</th> -->
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i=1; ?>
                            @foreach ($result as $value)
                            <tr class="opening_row">
                                <td>{{$i}}</td>
                                <td>{{ $value->customer_full_name }}<br>
                                    {{ $value->customer_contact_number }}
                                </td>
                                <td>{{ $value->heading }}<br>
                                    Expiry date: {{date('F d, Y', strtotime($value->expiry_date))}}<br>
                                    @if($value->credit != 0)
                                        <span class="guest-label">Credit Used</span>
                                    @endif
                                </td>
                                <td>{{$value->partner_name.', '.$value->partner_area}}</td>
                                <!-- <td>
                                    <button>Edit</button>
                                </td> -->
                            </tr>
                            <?php $i++; ?>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div style="font-size: 1.4em; color: red;">
                            {{ 'No Deal Found.' }}
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
        $('#voucherList').DataTable({
            //"paging": false
            "order": []
        });
    });
</script>