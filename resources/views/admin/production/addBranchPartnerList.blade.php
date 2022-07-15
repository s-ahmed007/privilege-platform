@include('admin.production.header')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css"/>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>
<style>
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    color: #337ab7 !important;background-color: #eee !important}
.dataTables_wrapper .dataTables_paginate .paginate_button{
    color: #337ab7 !important; background-color: #ffffff !important}
.dataTables_wrapper .dataTables_paginate .paginate_button.current{
    color: #ffffff !important; background-color: #337ab7 !important}
.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover{
    color: #ffffff !important; background-color: #337ab7 !important}
</style>
<div class="right_col" role="main">
   <div class="page-title">
      <div class="title_left">
         <h3>Add Branch</h3>
      </div>
   </div>
   <div class="container">
      <div class="row">
         <div class="col-xs-12">
            <div class="table-responsive">
               <table id="branchList" class="table table-bordered table-hover table-striped projects">
                  <thead>
                     <tr>
                        <th>S/N</th>
                        <th>Partner Name</th>
                        <!-- <th>Expiry</th> -->
                        <th>Action</th>
                     </tr>
                  </thead>
                  <tbody>
                     @if(isset($all_partners))
                        <?php $i = 1; ?>
                        @foreach ($all_partners as $key => $value)
                        <tr>
                           <td>{{ $i }}</td>
                           <td>{{ $value->partner_name }}</td>
                           <!-- <td>{{ $value->expiry_date }}</td> -->
                           <td><a class="btn btn-primary"
                              href="{{url('admin/add-branch/'.$value->partner_account_id)}}">Add Branch</a>
                           </td>
                        </tr>
                        <?php $i++; ?>
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