@include('admin.production.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>
<div class="right_col" role="main">
   <div class="page-title">
      <div class="title_left">
         <h3>Partner With Branches <?php if (isset($partner_number)) {
            echo '(' . $partner_number . ')';
            }?>
         </h3>
      </div>
      <div class="title_left"></div>
   </div>
   <div class="clearfix"></div>
   <div class="container">
      <div class="row">
         <div class="col-xs-12">
            <div class="table-responsive">
               <table id="branchList" class="table table-bordered table-hover table-striped projects">
                  <thead>
                     <tr>
                        <th>Partner Branch Name</th>
                        <th>Address</th>
                        <th>Action</th>
                     </tr>
                  </thead>
                  <tbody>
                  @if(Session::get('admin') == \App\Http\Controllers\Enum\AdminRole::superadmin)
                     @foreach ($allPartners as $key => $value)
                        @foreach($value->branches as $key2 => $branch)
                        <tr>
                           <td>{{ $value->info->partner_name .' ('. $branch->partner_area.')'}}</td>
                           <td>{{ $branch->partner_address }}</td>
                           <td align="center">
                              <select id="branch_edit_{{$branch->id}}" onchange="branch_edit('{{$branch->id}}')"
                                 class="selectChangeOff">
                                 <option disabled selected>--Options--</option>
                                 <option value="1">Scanners</option>
                                 <!-- <option value="2">Ip address</option> -->
                              </select>
                           </td>
                        </tr>
                        @endforeach
                     @endforeach
                  @else
                     @foreach ($allPartners as $key => $value)
                        @foreach($value->branches as $key2 => $branch)
                           @if($branch->active == 1)
                           <tr>
                              <td>{{ $value->info->partner_name .' ('. $branch->partner_area.')'}}</td>
                              <td>{{ $branch->partner_address }}</td>
                              <td align="center">
                                 <select id="branch_edit_{{$branch->id}}" onchange="branch_edit('{{$branch->id}}')"
                                    class="selectChangeOff">
                                    <option disabled selected>--Options--</option>
                                    <option value="1">Scanners</option>
                                    <!-- <option value="2">Ip address</option> -->
                                 </select>
                              </td>
                           </tr>
                           @endif
                        @endforeach
                     @endforeach
                  @endif
                  </tbody>
               </table>
            </div>
            <!--end of .table-responsive-->
         </div>
      </div>
   </div>
</div>
@include('admin.production.footer')
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script>
   function branch_edit(branch_id) {
       var option_type = document.getElementById("branch_edit_" + branch_id).value;
       //return false;
       if (option_type == 1) {
           var url = "{{url('/manage-branch-scanners')}}" + '/' + branch_id;
           window.location = url;
       } else if (option_type == 2) {
           var url = "{{url('/manage-branch-ip-address')}}" + '/' + branch_id;
           window.location = url;
       }
   }
</script>
<script type="text/javascript">
   $(document).ready(function () {
       $('#branchList').DataTable({
           //"paging": false
           "order": []
       });
   });
   //to keep select option unselected from prev page
   $(document).ready(function () {
      $(".selectChangeOff").each(function () {
         $(this).val($(this).find('option[selected]').val());
      });
   })
</script>