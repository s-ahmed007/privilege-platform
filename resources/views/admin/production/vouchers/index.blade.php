@include('admin.production.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if (session('delete'))
                <div class="alert alert-danger">
                    {{ session('delete') }}
                </div>
            @elseif (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>    
            @endif
            <h3>{{$branch_info->info->partner_name.' - '.$branch_info->partner_area}}</h3>
            <h3>Deals /Edit /Display</h3>
             <a type="button" class="btn btn-create" href="{{ url('/admin/vouchers/create?id='.$id) }}" style="margin-left: unset;">+ Create A New Deal</a>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    @if($vouchers)
                        <table id="voucherList" class="table table-striped projects">
                            <thead>
                            <tr>
                                <th>Heading</th>
                                <th>Duration</th>
                                <th>Price</th>
                                <th>Priority</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($vouchers as $voucher)
                                <?php $dates = $voucher->date_duration; ?>
                                <tr class="opening_row" data-voucher-id='{{ $voucher->id }}'>
                                    <td>{{ $voucher->heading }}</td>
                                    <td>{{ 'From: '.date('F d, Y', strtotime($dates[0]['from']))}}
                                        <br>{{'To: '.date('F d, Y', strtotime($dates[0]['to']))}}</td>
                                    <td>
                                        Actual price: {{intval($voucher->actual_price).' Tk'}}<br>
                                        Selling price: {{intval($voucher->selling_price).' Tk'}}<br>
                                        Discount: {{intval($voucher->discount)}}{{$voucher->discount_type == 1 ? ' Tk':'%'}}
                                    </td>
                                    <td>{{$voucher->priority}}</td>
                                    <td>
                                       <button class="btn btn-edit editBtn" title="Edit" data-voucher-id='{{ $voucher->id }}'>
                                       <i class="fa fa-edit"></i>
                                       </button>
                                       @if(Session::get('admin') == \App\Http\Controllers\Enum\AdminRole::superadmin)
                                           <button class="btn btn-delete deleteBtn" title="Delete" data-voucher-id='{{ $voucher->id }}'>
                                           <i class="fa fa-trash-alt"></i>
                                           </button>
                                       @endif
                                       @if($voucher->active == 1)
                                           <button class="btn btn-deactivate deactiveBtn" title="Deactivate"
                                              data-voucher-id='{{ $voucher->id }}'>
                                           <i class="glyphicon glyphicon-pause"></i>
                                           </button>
                                       @else
                                           <button class="btn btn-activate activeBtn" title="Activate" data-voucher-id='{{ $voucher->id }}'>
                                           <i class="glyphicon glyphicon-play"></i>
                                           </button>
                                       @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div style="font-size: 1.4em; color: red;">
                            {{ 'No Deal found..' }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.production.footer')
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script>
    $('.deleteBtn').on('click', function (event) {
        if (confirm("Are you sure to delete?")) {
            //get voucher id
            var voucherId = $(this).attr('data-voucher-id');
            var url = "{{ url('/admin/vouchers') }}";
            url += '/' + voucherId;

            $('<form action="' + url + '" method="POST">' +
                '<input type="hidden" name="_token" value="{{ csrf_token() }}"/>' +
                '<input type="hidden" name="_method" value="DELETE"/>' +
                '</form>').appendTo($(document.body)).submit();
        }
        return false;
    });

    $('.editBtn').on('click', function (event) {
        //get voucher id
        var voucherId = $(this).attr('data-voucher-id');
        var url = "{{ url('/admin/vouchers') }}";
        url += '/' + voucherId + '/edit';
        window.location.href = url;
    });

    $('.activeBtn').on('click', function (event) {
        if(confirm('Are you sure to activate?')){
            //get voucher id
            var voucherId = $(this).attr('data-voucher-id');
            var url = "{{ url('/admin/voucher/change_status') }}" + '/' + voucherId;
            
            $('<form action="' + url + '" method="POST">' +
                '<input type="hidden" name="_token" value="{{ csrf_token() }}"/>' +
                '</form>').appendTo($(document.body)).submit();
        }
    });

    $('.deactiveBtn').on('click', function (event) {
        if(confirm('Are you sure to deactivate?')) {
            //get the voucher id
            var voucherId = $(this).attr('data-voucher-id');
            var url = "{{ url('/admin/voucher/change_status') }}" + '/' + voucherId;

            $('<form action="' + url + '" method="POST">' +
                '<input type="hidden" name="_token" value="{{ csrf_token() }}"/>' +
                '</form>').appendTo($(document.body)).submit();
        }
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('#voucherList').DataTable({
            //"paging": false
            "order": []
        });
    });
</script>