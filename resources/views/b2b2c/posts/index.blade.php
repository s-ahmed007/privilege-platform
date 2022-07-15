@include('b2b2c.layout.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>
<?php use \App\Http\Controllers\functionController; ?>
<div class="right_col" role="main">
   <div class="page-title">
      <div class="title_left">
         @if (session('status'))
         <div class="alert alert-success">
            {{ session('status') }}
         </div>
         @elseif (session('delete post'))
         <div class="alert alert-danger">
            {{ session('delete post') }}
         </div>
         @elseif(session('try_again'))
         <div class="alert alert-warning">
            {{ session('try_again') }}
         </div>
         @endif
         <h3>Posts Create/ Edit/ Delete/ Display</h3>
         <a type="button" class="btn btn-create" href="{{ url('client/all-post/create') }}" style="margin-left: unset;">+ Create A New Post</a>
      </div>
   </div>
   <div class="clearfix"></div>
   <div class="row">
      <div class="col-md-12">
         <div class="x_panel">
            <div class="x_content">
               @if($allPosts)
               <table id="postsList" class="table table-striped projects">
                  <thead>
                     <tr>
                        <th width="10%">Image</th>
                        <th>Header</th>
                        <th>Caption</th>
                        <th>Posted<br>On</th>
                        <th>Total<br>Shared</th>
                        <th>Push<br>Notification</th>
                        <th>Action</th>
                        <th>Status</th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach ($allPosts as $post)
                     <tr class="post_row" data-post-id='{{ $post->id }}'>
                        <td><img src="{{asset($post['image_url'])}}" width="100%" alt="post-image"></td>
                        <td>{{ $post->header }}</td>
                        <td>{{ $post->caption }}</td>
                        <td>{{ $post->posted_on }}</td>
                        <td>
                             {{$post->share_post_count}}
                         </td>
                         <td>
                             Received: {{ count($post['log_events']->where('event', 'feed_notification_received')) }}<br>
                             Opened: {{ count($post['log_events']->where('event', 'feed_notification_opened')) }}
                         </td>
                        <td>
                           <button class="btn btn-edit editBtn"
                              data-post-id='{{ $post->id }}'>
                           <i class="fa fa-edit"></i>
                           </button>
                           <button class="btn btn-delete deleteBtn"
                              data-post-id='{{ $post->id }}'>
                           <i class="fa fa-trash-alt"></i>
                           </button>
                        </td>
                         <td>
                             @if($post->moderate_status == 1)
                                 <h4><p align="middle" class="label label-success">Active</p></h4>
                             @else
                                 <h4><p align="middle" class="label label-warning">Pending</p></h4>
                             @endif
                         </td>
                     </tr>
                     @endforeach
                  </tbody>
               </table>
               @else
               <div style="font-size: 1.4em; color: red;">
                  {{ 'No post found.' }}
               </div>
               @endif
            </div>
         </div>
      </div>
   </div>
</div>
@include('b2b2c.layout.footer')
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
{{-- ============================================================================================================
========================post edit & delete====================
============================================================================================================= --}}
<script>
   $('.deleteBtn').on('click', function (event) {
       if (confirm("Are you sure?")) {
           //fetch the post id
           var postId = $(this).attr('data-post-id');
           var url = "{{ url('/client/all-post/') }}";
           url += '/' + postId;
   
           $('<form action="' + url + '" method="POST">' +
               '<input type="hidden" name="_token" value="{{ csrf_token() }}"/>' +
               '<input type="hidden" name="_method" value="DELETE"/>' +
               '</form>').appendTo($(document.body)).submit();
       }
       return false;
   });
   
   $('.editBtn').on('click', function (event) {
       //fetch the post id
       var postId = $(this).attr('data-post-id');
       var url = "{{ url('/client/all-post/') }}";
       url += '/' + postId + '/edit';
       window.location.href = url;
   });

</script>
<script type="text/javascript">
   $(document).ready(function () {
       $('#postsList').DataTable({
           //"paging": false
       });
   });
</script>