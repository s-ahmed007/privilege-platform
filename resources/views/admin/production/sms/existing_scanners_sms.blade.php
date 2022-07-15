@include('admin.production.header')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css"/>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>
{{--SMS HUB-EXISTING CUSTOMERS--}}
<div class="right_col" role="main">
   <div class="page-title">
      <div class="title_left">
         @if (session('sms successful'))
         <div class="alert alert-success">
            {{ session('sms successful') }}
         </div>
         @elseif (session('sms failed'))
         <div class="alert alert-danger">
            {{ session('sms failed') }}
         </div>
         @endif
         <h3>Existing Scanners</h3>
      </div>
      <div class="title_right">
         <div class="col-md-8 col-sm-5 col-xs-12 form-group pull-right top_search">
            {{-- 
            <form action="{{ url('customerForSMS') }}" method="post">
               {{csrf_field()}}
               <div class="form-group">
                  <label for="customerSearchKey">Search Customer</label><br>
                  <input type="text" class="form-control" name="customerSearchKey" id="customerSearchKey"
                     placeholder="Customer with name, E-mail or phone" style="width: 100%;">
               </div>
            </form>
            --}}
         </div>
      </div>
   </div>
   <div class="clearfix"></div>
   <div class="container">
      <div class="row">
         <div class="col-xs-12">
            <div class="table-responsive">
               @if($profileInfo)
               <table id="scannerList" class="table table-bordered table-hover table-striped projects">
                  <thead>
                     <tr>
                        <th style="width: 10%">S/N</th>
                        <th style="width: 15%">Scanner Info</th>
                        <th>Partner</th>
                        <th>Mobile</th>
                        <th>Action</th>
                     </tr>
                  </thead>
                  <tbody>
                     @php $i=1; @endphp
                     @foreach ($profileInfo as $scanner)
                     <tr>
                        <td>{{ $i }}</td>
                        <td>{{ $scanner->full_name }}</td>
                        <td>{{$scanner->partner_name.', '.$scanner->partner_area}}</td>
                        <td>{{ $scanner->phone }}</td>
                        <td>
                           <button type="button" class="btn btn-primary" data-toggle="modal"
                              onclick="getPhone({{ $scanner->phone }})"
                              data-target="#sendSMSModal">Send SMS
                           </button>
                        </td>
                     </tr>
                     @php $i++; @endphp
                     @endforeach
                  </tbody>
                  <tfoot>
                     <tr>
                     </tr>
                  </tfoot>
               </table>
               @else
               <div style="font-size: 1.4em; color: red;">
                  {{ 'No scanner found.' }}
               </div>
               @endif
            </div>
         </div>
      </div>
   </div>
</div>
<div id="sendSMSModal" class="modal fade" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
         <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
            <h4 class="modal-title">Send SMS</h4>
         </div>
         <span class="modal-body">
            <form class="form-horizontal form-label-left" method="post" action="{{ url('sendAdminSMS') }}"
               enctype="multipart/form-data">
               <div class="form-group">
                  <label class="control-label col-md-2 col-sm-2 col-xs-12">Language:</label>
                  <div class="col-md-8 col-sm-8 col-xs-12">
                     <select class="browser-default custom-select" name="language"
                        style="display: block;
                        width: 100%;
                        margin: 5px 0px 10px 0;
                        padding: 5px 0px 5px 0;
                        border: 1px solid #ccc;">
                        <option selected value="english">English</option>
                        <option value="bangla">Bengali</option>
                     </select>
                  </div>
               </div>
               <div class="form-group">
                  <label class="control-label col-md-2 col-sm-2 col-xs-12">Mobile No:</label>
                  <div class="col-md-8 col-sm-8 col-xs-12">
                     <input type="text" class="form-control" placeholder="i.e. +8801XXXXXXXXX" name="phone"
                        id="phone" maxlength="14" minlength="14" required>
                  </div>
               </div>
               <div class="form-group">
                  <label class="control-label col-md-2 col-sm-2 col-xs-12">Message</label>
                  <div class="col-md-8 col-sm-8 col-xs-12">
                     <textarea rows="4" name="text_message" id="text_message" onkeyup="countChars();"
                        class="form-control" placeholder="Write a message" required></textarea>
                  </div>
               </div>
               <p class="center"> Characters Count :
                  <span id="charNum">0</span>
               </p>
               <p class="center">160 Characters = 1 SMS</p>
               <input type="hidden" name="_token" value="{{ csrf_token() }}">
               <input type="hidden" name="user_type" value="scanner">
               <div class="form-group">
                  <p class="center">
                     <button type="submit" class="btn btn-activate pull-right">Submit</button>
                  </p>
               </div>
            </form>
         </span>
      </div>
   </div>
</div>
@include('admin.production.footer')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script>
   function getPhone(phone_no) {
       $("#phone").val('+' + phone_no);
   }
   
   function countChars() {
       var no_of_chars = $("#text_message").val();
       $("#charNum").text(no_of_chars.length);
   }
   
   $(document).ready(function () {
      $('#scannerList').DataTable({
          //"paging": false
          "order": []
      });
   });
</script>