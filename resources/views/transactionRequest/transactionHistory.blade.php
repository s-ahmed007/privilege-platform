@include('transactionRequest.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>
<style>
   .pro_pic{width: 50px; height: 50px; border-radius: 50%}
</style>
<div class="row">
   <div class="col-md-12">
      @if(count($transactions) > 0)
      {{-- 
      <form class="form-inline" action="{{url('branch/sort_transaction_request')}}" method="post" class="mb">
         {{csrf_field()}}
         <div class="form-group">
            <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
            <select class="form-control" name="year" required="required">
               <option selected disabled>Select year</option>
               @php
               define('DOB_YEAR_START', 2018);
               $current_year = date('Y');
               for ($count = $current_year; $count >= DOB_YEAR_START; $count--)
               {
               print "
               <option value='{$count}'>{$count}</option>
               ";
               }
               @endphp 
            </select>
         </div>
         <div class="form-group">
            <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
            <select class="form-control" name="month">
               <option selected disabled>Select month</option>
               @php
               for ($m=1; $m<=12; $m++) {
               $month = date('F', mktime(0,0,0,$m, 1, date('Y')));
               echo "
               <option value='".$m."'>".$month."</option>
               ";
               }
               @endphp
            </select>
         </div>
         <div class="form-group">
            <label class="control-label col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-6 col-xs-offset-3"></label>
            <button type="submit" class="btn btn-primary form-control">Sort</button>
         </div>
      </form>
      --}}
      <div class="table-responsive">
         <table id="transactionList" class="table table-bordered table-hover table-striped projects">
            <thead>
               <tr>
                  <th>Image</th>
                  <th>Customer Name</th>
                  <th>Offer</th>
                  <th>Transacted By</th>
                  <th>Time</th>
                  <th class="d-none">Sort</th>
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
                     @endif
                  </td>
                  <td>
                     @if($row['full_name'] == null)
                        {{ 'Royalty Admin' }}
                     @else
                        {{ $row['full_name'] }}
                     @endif
                  </td>
                  <?php $posted_on=date("h:i A | F d, Y", strtotime($row['posted_on'])); ?>
                  <td>{{ $posted_on }}</td>
                  <td class="d-none">{{ date("Y F d", strtotime($row['posted_on'])) }}</td>
               </tr>
               @endforeach
            </tbody>
         </table>
      </div>
      @else
      <p>Not request has been made yet</p>
      @endif
   </div>
</div>
@include('transactionRequest.footer')
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
   $(document).ready(function () {
       $('#transactionList').DataTable({
           "order": []
       });
   });
</script>