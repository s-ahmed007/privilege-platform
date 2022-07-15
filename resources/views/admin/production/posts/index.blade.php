@include('admin.production.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>
<?php use \App\Http\Controllers\functionController; ?>
<style>
    .bg_color {
        background-color: #ffedc9;
        background-image: linear-gradient(to top right, #ffd3c9, #c9f7ff, #cdc9ff);
    }
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
         @elseif (session('delete post'))
         <div class="alert alert-danger">
            {{ session('delete post') }}
         </div>
         @elseif(session('try_again'))
         <div class="alert alert-warning">
            {{ session('try_again') }}
         </div>
         @elseif(session('post_unpinned'))
         <div class="alert alert-success">
            {{ session('post_unpinned') }}
         </div>
         @elseif(session('post_pinned'))
         <div class="alert alert-success">
            {{ session('post_pinned') }}
         </div>
         @elseif(session('post_noti'))
         <div class="alert alert-success">
            {{ session('post_noti') }}
         </div>
         @endif
         <h3>Posts Create/Edit/Delete/Display</h3>
         <a type="button" class="btn btn-create" href="{{ url('/admin/post/create') }}"
            style="margin-left: unset;">+ Create A New Post</a>
         <a href="{{url('admin/post')}}" class="btn btn-all">All</a>
         <a href="{{url('admin/post/?post_of=royalty')}}" class="btn btn-premium">Royalty</a>
         <a href="{{url('admin/post/?post_of=partner')}}" class="btn btn-guest">Partner</a>
      </div>
   </div>
   <div class="container">
      <div class="row">
         <div class="col-xs-12">
            <div class="table-responsive">
               @if($allPosts)
               <table id="postsList" class="table table-bordered table-hover table-striped projects">
                  <thead>
                     <tr>
                     <th>Posted On</th>
                        <th>Image</th>
                        <th>Header</th>
                        <th>Caption</th>
                        <th>Post Type</th>
                        <th>Activity</th>
                        <th>Action</th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach ($allPosts as $post)
                         <?php
                         if ($post->pinned_post == 1 || ($post->poster_type == \App\Http\Controllers\Enum\PostType::partner
                             && $post->push_status == 0)) {
                             $bg_color = 'bg_color';
                         } else {
                             $bg_color = '';
                         }
                         ?>
                     <tr class="post_row {{$bg_color}}" data-post-id='{{ $post->id }}'>
                     <?php $posted_on = date("F d, Y h:i A", strtotime($post->posted_on)); ?>
                        <td>{{ $posted_on }}
                           @if($post->scheduled_at > date("Y-m-d H:i:s") && $post->moderate_status == 0)
                           <b style="color: #c13e3e;">{{'Scheduled at '. date("h:i A F d, Y", strtotime($post->scheduled_at))}}</b>
                           @endif
                        </td>
                        <td>
                            @if($post['media_type'] == \App\Http\Controllers\Enum\MediaType::IMAGE)
                                <img src="{{asset($post['image_url'])}}" width="100%" alt="post-image">
                            @else
                                <iframe width="100%" height="154" src="{{$post['image_url']}}" frameborder="0"
                                        allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen></iframe>
                            @endif
                        </td>
                        <td>{{ $post->header }}</td>
                        <td>{{ $post->caption }}</td>
                        
                        <td>
                           @if($post->poster_type == \App\Http\Controllers\Enum\PostType::partner)
                           <?php $PartnerInfo = (new functionController)->partnerInfoById($post->poster_id); ?>
                           <p style="font-weight: bold; color: darkgreen">Partner</p>
                           <p>{{ $PartnerInfo->partner_name ?? 'Not Found'}}</p>
                           @elseif($post->poster_type == \App\Http\Controllers\Enum\PostType::b2b2c)
                           <?php $ClientInfo = (new functionController)->clientInfoById($post->poster_id); ?>
                           <p style="font-weight: bold; color: #2f6ee0">Client</p>
                           <p>{{ $ClientInfo['name'] ?? 'Not Found'}}</p>
                           @else
                           <p style="font-weight: bold; color: darkred">Admin</p>
                           @endif
                        </td>
                        <td>Shared: {{$post->share_post_count}}<br>
                            Loved: {{$post->like_count}}</td>
                        <td>
                           <button class="btn btn-edit editBtn" data-post-id='{{ $post->id }}'>
                           <i class="fa fa-edit"></i>
                           </button>
                           <button class="btn btn-delete deleteBtn" data-post-id='{{ $post->id }}'>
                           <i class="fa fa-trash-alt"></i>
                           </button>
                           @if($post->moderate_status == 1)
                               <button class="btn btn-deactivate deactiveBtn" data-post-id='{{ $post->id }}' title="Deactive">
                               <i class="glyphicon glyphicon-pause"></i>
                               </button>
                           @else
                               <button class="btn btn-activate activeBtn" data-post-id='{{ $post->id }}' title="Active">
                               <i class="glyphicon glyphicon-play"></i>
                               </button>
                           @endif
                           @if($post->pinned_post == 1)
                               <!-- if pinned -->
                               <button class="btn btn-pin unpinBtn" data-post-id='{{ $post->id }}' title="Unpin">unpin
                               </button>
                           @else
                               <!-- if needs to pin -->
                               <button class="btn btn-pin pinBtn" data-post-id='{{ $post->id }}' title="Pin">
                               <i class="bx bxs-bookmark-star"></i>
                               </button>
                           @endif
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
                  {{ 'No post found.' }}
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
========================post edit & delete====================
============================================================================================================= --}}
<script>
   $('.deleteBtn').on('click', function (event) {
       if (confirm("Are you sure?")) {
           //fetch the post id
           var postId = $(this).attr('data-post-id');
           var url = "{{ url('/admin/post/') }}";
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
       var url = "{{ url('/admin/post/') }}";
       url += '/' + postId + '/edit';
       window.location.href = url;
   });
   
   $('.activeBtn').on('click', function (event) {
       //fetch the post id
       var postId = $(this).attr('data-post-id');
       var url = "{{ url('/active-post') }}" + '/' + postId;
       window.location.href = url;
   });
   
   $('.deactiveBtn').on('click', function (event) {
       //fetch the post id
       var postId = $(this).attr('data-post-id');
       var url = "{{ url('/deactive-post') }}" + '/' + postId;
       window.location.href = url;
   });
   
   $('.unpinBtn').on('click', function (event) {
       if (confirm("Are you sure?")) {
           //fetch the opening id
           var postId = $(this).attr('data-post-id');
           var url = "{{ url('/unpin-post') }}";
           url += '/' + postId;
   
           $('<form action="' + url + '" method="POST">' +
               '<input type="hidden" name="_token" value="{{ csrf_token() }}"/>' +
               '<input type="hidden" name="_method" value="POST"/>' +
               '</form>').appendTo($(document.body)).submit();
       }
       return false;
   });
   $('.pinBtn').on('click', function (event) {
       if (confirm("Are you sure?")) {
           //fetch the opening id
           var postId = $(this).attr('data-post-id');
           var url = "{{ url('/pin-post') }}";
           url += '/' + postId;
   
           $('<form action="' + url + '" method="POST">' +
               '<input type="hidden" name="_token" value="{{ csrf_token() }}"/>' +
               '<input type="hidden" name="_method" value="POST"/>' +
               '</form>').appendTo($(document.body)).submit();
       }
       return false;
   });
</script>
<script type="text/javascript">
   $(document).ready(function () {
       $('#postsList').DataTable({
           //"paging": false
           "order": []
       });

   });
</script>