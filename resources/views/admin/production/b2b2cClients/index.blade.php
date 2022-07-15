@include('admin.production.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>
<?php use \App\Http\Controllers\functionController; ?>
<div class="right_col" role="main">
   <div class="page-title">
      <div class="title_left">
         @if (session('status'))
         <div class="alert alert-success">
            {{ session('status') }}
         </div>
         @elseif (session('delete blog'))
         <div class="alert alert-danger">
            {{ session('delete blog') }}
         </div>
         @elseif(session('try_again'))
         <div class="alert alert-warning">
            {{ session('try_again') }}
         </div>
         @elseif(session('blog_unpinned'))
         <div class="alert alert-success">
            {{ session('blog_unpinned') }}
         </div>
         @elseif(session('blog_pinned'))
         <div class="alert alert-success">
            {{ session('blog_pinned') }}
         </div>
         @endif
         <h3>B2B2C Clients Create/ Edit/ Delete/ Display</h3>
         <a type="button" class="btn btn-create" href="{{ url('/admin/b2b2c-clients/create') }}" style="margin-left: unset;">+ Create New Client</a>
         {{--<a type="button" class="btn btn-info" href="{{ url('/admin/b2b2c-clients/create') }}">+/- Blog Categories</a>--}}
      </div>
   </div>
   <div class="clearfix"></div>
   <div class="container">
      <div class="row">
         <div class="col-xs-12">
            <div class="table-responsive">
               @if($allClients)
               <table id="clientsList" class="table table-bordered table-hover table-striped projects">
                  <thead>
                     <tr>
                        <th style="width: 15%;">Image</th>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>Action</th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach ($allClients as $client)
                     <tr class="opening_row" data-client-id='{{ $client->id }}'>
                        <td><img src="{{ $client->image }}" width="100%" style="border-radius: 50%;height: 50px;width: 50px;"></td>
                        <td>{{ $client->name }}</td>
                        <td>{{ $client->phone }}</td>
                        <td>{{ $client->email }}</td>
                        <td>
                           <button class="btn btn-edit editBtn" data-client-id='{{ $client->id }}'>
                           <i class="fa fa-edit"></i>
                           </button>
                           <button class="btn btn-delete deleteBtn" data-client-id='{{ $client->id }}'>
                           <i class="fa fa-trash-alt"></i>
                           </button>
                        </td>
                     </tr>
                     @endforeach
                  </tbody>
               </table>
               @else
               <div style="font-size: 1.4em; color: red;">
                  {{ 'No Client found.' }}
               </div>
               @endif
            </div>
            <!--end of .table-responsive-->
         </div>
      </div>
   </div>
</div>
@include('admin.production.footer')
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
{{-- ============================================================================================================
========================Opening edit & delete====================
============================================================================================================= --}}
<script>
   $('.deleteBtn').on('click', function (event) {
       if (confirm("Are you sure?")) {
           //fetch the client id
           var clientId = $(this).attr('data-client-id');
           var url = "{{ url('/delete-b2b2c-client') }}";
           url += '/' + clientId;
           window.location.href = url;
       }
       return false;
   });
   
   $('.editBtn').on('click', function (event) {
       //fetch the client id
       var clientId = $(this).attr('data-client-id');
       var url = "{{ url('/edit-b2b2c-client') }}";
       url += '/' + clientId;
       window.location.href = url;
   });
</script>
<script type="text/javascript">
   $(document).ready(function () {
       $('#clientsList').DataTable({
           //"paging": false
       });
   });
</script>