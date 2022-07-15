@include('admin.production.header')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css"/>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>
<div class="right_col" role="main">
   <div class="page-title">
      <div class="title_left">
         <h3>Add Deal</h3>
      </div>
   </div>
   <div class="clearfix"></div>
   <div class="container">
      <div class="row">
         <div class="col-xs-12">
            <div class="table-responsive">
               <table id="branchList" class="table table-bordered table-hover table-striped projects">
                  <thead>
                     <tr>
                        <th>ID</th>
                        <th>Partner Name</th>
                        <th>Address</th>
                        <th>Expiry</th>
                        <th>Action</th>
                     </tr>
                  </thead>
                  <tbody>
                     @if(isset($branches))
                     @foreach ($branches as $key => $value)
                     <tr>
                        <td>{{ $value->partner_account_id }}</td>
                        <td>{{ $value->info->partner_name }}</td>
                        <td width="50%">{{ $value->partner_address }}</td>
                        <td>{{ $value->info->expiry_date }}</td>
                        <td><a class="btn btn-primary"
                           href="{{url("admin/vouchers/create?id=".$value->id)}}">Add Deal</a>
                        </td>
                     </tr>
                     @endforeach
                     @else
                     <div style="font-size: 1.4em; color: red;">
                        {{ 'Partner not found.' }}
                     </div>
                     @endif
                  </tbody>
                  <tfoot>
                     <tr>
                     </tr>
                  </tfoot>
               </table>
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
       $('#branchList').DataTable({
           //"paging": false
           "order": []
       });
   });
</script>