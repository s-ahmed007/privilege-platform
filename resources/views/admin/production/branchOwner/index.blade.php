@include('admin.production.header')
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
         @if (session('status'))
         <div class="alert alert-success">
            {{ session('status') }}
         </div>
         @endif
         <h3>Branch Owner Create/ Edit/ Delete/ Display</h3>
         <a type="button" class="btn btn-create" href="{{ url('/branch-owner/create') }}" style="margin-left: unset;">+ Create New Branch Owner</a>
      </div>
   </div>
   <div class="clearfix"></div>
   <div class="container">
      <div class="row">
         <div class="col-xs-12">
            <div class="table-responsive">
               @if($owners)
               <table id="ownerList" class="table table-bordered table-hover table-striped projects">
                  <thead>
                     <tr>
                        <th>Name</th>
                        <th>Partner Info</th>
                        <th>Phone</th>
                        {{--
                        <th>Status</th>
                        --}}
                        <th>Action</th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach ($owners as $owner)
                     <tr class="blog_row" data-blog-id='{{ $owner->id }}'>
                        <td>{{ $owner->name}}</td>
                        <td>{{ count($owner->branches) > 0 ? 
                           $owner->branches[0]->info->partner_name .', '.$owner->branches[0]->partner_area
                           : 'Not Assigned'}}
                        </td>
                        <td>{{ $owner->phone}}</td>
                        {{--                                    
                        <td>{{ $owner->active}}</td>
                        --}}
                        <td>
                           <select id="owner_edit_{{$owner->id}}" onchange="owner_edit('{{$owner->id}}')"
                              class="selectChangeOff">
                              <option disabled selected>--Options--</option>
                              <option value="1">Branches</option>
                              <option value="2">Edit</option>
                              <option value="3">Delete</option>
                           </select>
                        </td>
                     </tr>
                     @endforeach
                  </tbody>
                  <tfoot>
                     <tr>
                     </tr>
                  </tfoot>
               </table>
               @else
               <div style="font-size: 1.4em; color: red;">
                  {{ 'No owner found.' }}
               </div>
               @endif
            </div>
         </div>
      </div>
   </div>
</div>
@include('admin.production.footer')
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
{{-- ============================================================================================================
========================owner edit & delete====================
============================================================================================================= --}}
<script>
   function owner_edit(owner_id) {
       var option_type = document.getElementById("owner_edit_" + owner_id).value;
       if (option_type === '1') {
           // alert('Service Currently unavailable');
           // return false;
           var url = "{{url('/manage-branch')}}" + '/' + owner_id;
           window.location = url;
       } else if (option_type === '2') {
           var url = "{{url('/branch-owner')}}" + '/' + owner_id + '/edit';
           window.location = url;
       } else if (option_type === '3') {
           if(confirm('Are you sure to delete?')){
               var url = "{{url('/branch-owner')}}" + '/' + owner_id;
               window.location = url;
   
               $('<form action="' + url + '" method="POST">' +
                   '<input type="hidden" name="_token" value="{{ csrf_token() }}"/>' +
                   '<input type="hidden" name="_method" value="DELETE"/>' +
                   '</form>').appendTo($(document.body)).submit();
           }
       }
   }
   
   $('.activeBtn').on('click', function (event) {
       //fetch the blog id
       var blogId = $(this).attr('data-blog-id');
       var url = "{{ url('/active-blog') }}" + '/' + blogId;
       window.location.href = url;
   });
   
   $('.deactiveBtn').on('click', function (event) {
       //fetch the blog id
       var blogId = $(this).attr('data-blog-id');
       var url = "{{ url('/deactive-blog') }}" + '/' + blogId;
       window.location.href = url;
   });
   
</script>
<script type="text/javascript">
   $(document).ready(function () {
       $('#ownerList').DataTable({
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