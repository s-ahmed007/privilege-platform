@include('transactionRequest.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>
@if (session('success'))
   <div class="title_right alert alert-success" style="text-align: center;">{{ session('success') }}</div>
@endif
<div class="row">
   <div class="col-md-12">
   <h2>Reward History</h2>
      @if(count($scanner_prize_history) > 0)
      <div class="table-responsive">
         <table id="transactionList" class="table table-bordered table-hover table-striped projects">
            <thead>
               <tr>
                  <th>Reward</th>
                  <th>Point</th>
                  <th>Time</th>
                  <th>Status</th>
               </tr>
            </thead>
            <tbody>
               @foreach ($scanner_prize_history as $row)
               <tr>
                  <td>{{ $row->text }}
                  </td>
                  <td>
                     {{ $row->point }}
                  </td>
                  <?php
                     $posted_on=date("h:i A | F d, Y", strtotime($row['posted_on']));
                     ?>
                  <td>{{ $posted_on }}</td>
                  <td>
                     @if($row->status == 1)
                        <span class="badge badge-success">Successful</span>
                     @else
                        <span class="badge badge-warning">Pending</span>
                     @endif
                  </td>
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