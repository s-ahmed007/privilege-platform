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
            @elseif (session('try_again'))
                <div class="alert alert-warning">
                    {{ session('try_again') }}
                </div>    
            @endif
            <h3>{{$tab_title}} refund requests</h3>
            <a class="btn btn-all" href="{{url('admin/deal_refund_requests/all')}}">All</a>
            <a class="btn btn-premium" href="{{url('admin/deal_refund_requests/accepted')}}">Accepted</a>
            <a class="btn btn-guest" href="{{url('admin/deal_refund_requests/rejected')}}">Rejected</a>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12">
        <div class="x_panel">
          <div class="x_content">
              @if($requests)
                <table id="requestList" class="table table-striped projects">
                  <thead>
                    <tr>
                      <th>Customer</th>
                      <th>Deal</th>
                      <th>Comment</th>
                      <th>Date</th>
                      @if($tab_title == 'All')
                        <th>Action</th>
                      @endif
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($requests as $request)
                      <tr class="opening_row">
                        <td>{{ $request->customer->customer_full_name }}<br>
                            {{ $request->customer->customer_contact_number }}<br>
                            @if($request->customer->customer_type == 2)
                                <p class="card-type-premium">Premium Member</p>
                            @else
                                <p class="card-type-guest">Guest Member</p>
                            @endif
                        </td>
                        <td>{{ $request->purchaseDetails->voucher->heading}}<br>
                            {{ $request->purchaseDetails->voucher->branch->info->partner_name.', '.$request->purchaseDetails->voucher->branch->partner_area }}<br>
                            {{ 'Refund amount: ' .intval($request->purchaseDetails->voucher->selling_price) }}
                        </td>
                          <td>{{$request->comment}}</td>
                        <td>
                            {{ date('F d, Y h:i A', strtotime($request->created_at)) }}
                        </td>
                        <td>
                           @if(Session::get('admin') == \App\Http\Controllers\Enum\AdminRole::superadmin)
                               @if($tab_title == 'All')
                               <button class="btn btn-success acceptBtn" title="Accept" data-request-id='{{ $request->id }}'>
                               <i class="fa fa-check"></i>
                               </button>
                               <button class="btn btn-delete rejectBtn" title="Reject" data-request-id='{{ $request->id }}'>
                               <i class="fa fa-times"></i>
                               </button>
                               @if($request->refund_status == 0)
                               <button class="btn btn-delete deleteBtn" title="Delete" data-request-id='{{ $request->id }}'>
                               <i class="fa fa-trash-alt"></i>
                               </button>
                               @endif
                               @endif
                           @endif
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              @else
                <div style="font-size: 1.4em; color: red;">
                  {{ 'No Request found..' }}
                </div>
              @endif
          </div>
        </div>
      </div>
    </div>
</div>

<!-- deal refund accept modal -->
<div class="modal fade" id="dealRefundAcceptModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
     <div class="modal-content">
        <div class="modal-header">  <h4 class="modal-title">Accept Refund Request!</h4>
           <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="cross-icon"></i>
           </button>
         
        </div>
        <div class="modal-body">
            <div class="row">
                <h3>Choose how to refund:<span style="color:red;font-size: 1.5em">*</span></h3>
               <form class="" action="{{url('admin/accept_deal_refund_request')}}" method="POST">
                  {{csrf_field()}}
                  <div class="col-md-6 col-md-offset-3">
                     <input type="radio" id="credit" name="refund_type" value="1" checked>
                     <label for="credit">Credit</label><br>
                     <input type="radio" id="cash" name="refund_type" value="2">
                     <label for="cash">Cash</label><br>
                  </div>
                  <div class="col-md-12" style="text-align: center;">
                     <input type="hidden" name="request_id" id="request_id">
                     <button type="submit" class="btn btn-success">Submit</button>
                  </div>
               </form>
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
            //get request id
            var requestId = $(this).attr('data-request-id');
            var url = "{{ url('/admin/delete_deal_refund_request') }}" + "/" + requestId;

            $('<form action="' + url + '" method="POST">' +
                '<input type="hidden" name="_token" value="{{ csrf_token() }}"/>' +
                '<input type="hidden" name="_method" value="DELETE"/>' +
                '</form>').appendTo($(document.body)).submit();
        }
        return false;
    });

    $('.acceptBtn').on('click', function (event) {
        $("#request_id").val($(this).data("request-id"));
        $("#dealRefundAcceptModal").modal("show");
    });

    $('.rejectBtn').on('click', function (event) {
        if(confirm('Are you sure to reject?')) {
            //get the request id
            var requestId = $(this).attr('data-request-id');
            var url = "{{ url('/admin/reject_deal_refund_request') }}" + '/' + requestId;

            $('<form action="' + url + '" method="POST">' +
                '<input type="hidden" name="_token" value="{{ csrf_token() }}"/>' +
                '</form>').appendTo($(document.body)).submit();
        }
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('#requestList').DataTable({
            //"paging": false
            "order": []
        });
    });
</script>