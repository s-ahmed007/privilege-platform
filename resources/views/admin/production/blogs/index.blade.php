@include('admin.production.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>
<style>
   .category_list{padding: 10px; font-size: 15px; display: inline-block;}
   .deleteCategory{color: red; cursor: pointer;}
   ul>li.category_list:before{content: '\ffed';}
</style>
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
         @elseif($errors->getBag('default')->first('category_name'))
         <div class="alert alert-warning">
            {{ $errors->getBag('default')->first('category_name') }}
         </div>
         @endif
         <h3>Blog Create/ Edit/ Delete/ Display</h3>
         <a type="button" class="btn btn-create" href="{{ url('/admin/blog-post/create') }}" style="margin-left: unset;">+ Create New blog post</a>
         <a type="button" class="btn btn-active" data-toggle="modal" data-target="#blogCategoryModal">+/- Blog Category</a>
      </div>
   </div>
   <div class="clearfix"></div>
   <div class="container">
      <div class="row">
         <div class="col-xs-12">
            <div class="table-responsive">
               @if($allblogs)
               <table id="blogsList" class="table table-bordered table-hover table-striped projects">
                  <thead>
                     <tr>
                        <th>Image</th>
                        <th>Heading</th>
                        {{--<th>Details</th>--}}
                        <th>Category</th>
                        <th>Posted On</th>
                        <th>Priority</th>
                        <th>View</th>
                        <th>Action</th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach ($allblogs as $blog)
                     <tr class="blog_row" data-blog-id='{{ $blog->id }}'>
                        <td><img src="{{asset($blog->image_url)}}" width="100%" alt="blog-image"></td>
                        <td>{{ $blog->heading }}</td>
                        <td>{{ $blog->BlogCategory['category'] }}</td>
                        <td>{{ date("F d, Y h:i A", strtotime($blog->posted_on)) }}</td>
                        <td>{{ $blog->priority != 0 ? $blog->priority : 'N/A'}}</td>
                         @if($blog->visit_count > 1)
                             <td>{{ $blog->visit_count}} times</td>
                         @else
                             <td>{{ $blog->visit_count}} time</td>
                         @endif
                        <td>
                           <button class="btn btn-edit editBtn" data-blog-id='{{ $blog->id }}'>
                           <i class="fa fa-edit"></i>
                           </button>
                           <button class="btn btn-delete deleteBtn" data-blog-id='{{ $blog->id }}'>
                           <i class="fa fa-trash-alt"></i>
                           </button>
                           @if($blog->active_status == 1)
                               <button class="btn btn-deactivate" title="Deactive" onclick="updateStatus('{{$blog->id}}', '0')">
                               <i class="glyphicon glyphicon-pause"></i>
                               </button>
                           @else
                               <button class="btn btn-activate pull-right" title="Active"onclick="updateStatus('{{$blog->id}}', '1')">
                               <i class="glyphicon glyphicon-play"></i>
                               </button>
                           @endif
                        </td>
                     </tr>
                     @endforeach
                  </tbody>
               </table>
               @else
               <div style="font-size: 1.4em; color: red;">
                  {{ 'No blog found.' }}
               </div>
               @endif
            </div>
            <!--end of .table-responsive-->
         </div>
      </div>
   </div>
</div>
<div id="blogCategoryModal" class="modal fade" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
            <span class="modal-title">Blog category</span>
         </div>
         <div class="modal-body">
            <form action="{{url('admin/add_blog_category')}}" onsubmit="return checkCategory($('#text').val())" method="post">
               {{csrf_field()}}
               <div class="form-group">
                  <label for="text">Add a new category name:</label>
                  <span class="error_category"></span>
                  <input type="text" class="form-control" name="category_name" id="text" required>
               </div>
               <button type="submit" class="btn btn-primary" style="margin-left: unset;">Add</button>
            </form>
            <hr>
            <p>Previously added categories:</p>
            <span class="error_cat_delete"></span>
            <ul>
               @foreach($all_categories as $category)
               <li class="category_list {{$category->id}}">
                  <input type="text" value="{{$category->category}}" onfocusout="categoryUpdate({{ $category->id }})">
                  <span class="deleteCategory" onclick="deleteCategory({{$category->id}})">
                      <i class="fas fa-minus-circle"></i>
                  </span>
                   <i class="fas fa-check-circle category_updated_{{$category->id}}" style="display: none;"></i>
               </li>
               <br>
               @endforeach
            </ul>
            <p>Click on the red button <i class="fas fa-minus-circle" style="color: red"></i> to delete an existing category.</p>
         </div>
      </div>
   </div>
</div>
@include('admin.production.footer')
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
{{-- ============================================================================================================
========================blog edit & delete====================
============================================================================================================= --}}
<script type="text/javascript">
   $('.deleteBtn').on('click', function (event) {
       if (confirm("Are you sure?")) {
           //fetch the blog id
           var blogId = $(this).attr('data-blog-id');
           var url = "{{ url('/admin/blog-post/') }}";
           url += '/' + blogId;
   
           $('<form action="' + url + '" method="POST">' +
               '<input type="hidden" name="_token" value="{{ csrf_token() }}"/>' +
               '<input type="hidden" name="_method" value="DELETE"/>' +
               '</form>').appendTo($(document.body)).submit();
       }
       return false;
   });
   
   $('.editBtn').on('click', function (event) {
       //fetch the blog id
       var blogId = $(this).attr('data-blog-id');
       var url = "{{ url('/admin/blog-post/') }}";
       url += '/' + blogId + '/edit';
       window.location.href = url;
   });

   function updateStatus(id, status) {
       if(confirm("Are you sure?")){
           window.location.href = "{{ url('/admin/update_blog_status') }}" + '/' + id + '/' + status;
       }
   }

   $(document).ready(function () {
       $('#blogsList').DataTable({
           //"paging": false
           "order": []
       });
   });
   
   function checkCategory(category) {
       var className = document.getElementsByClassName('category_list');
       for (var idx = 0; idx < className.length; idx++) {
           var str1 = category.trim();//remove space from front & end
           var str2 = className[idx].outerText.trim();//remove space from front & end
           if(str1.toLowerCase() === str2.toLowerCase()){
               $('.error_category').text('Category already exists').css({'color': 'red', 'padding-left': '50px'});
               return false;
           }
       }
       return true;
   }

   function categoryUpdate(category_id) {
       var url = "{{ url('admin/update_blog_category') }}";
       var category_name = $("."+category_id).find('input').val();
       $.ajax({
           type: "POST",
           url: url,
           data: {'_token': '<?php echo csrf_token(); ?>', 'category_id': category_id, 'category_name': category_name},
           success: function (data) {
               if(data['result'] === true){
                   $(".category_updated_"+data['category_id']).css({'color': 'forestgreen', 'display': 'inline-block'});
               }else{
                   $(".category_updated_"+data['category_id']).css({'color': 'red', 'display': 'inline-block'});
               }
           }
       })
   }

   function deleteCategory(category_id) {
       if(confirm('Are you sure to delete?')){
           var url = "{{ url('admin/delete_blog_category') }}";
           $.ajax({
               type: "POST",
               url: url,
               data: {'_token': '<?php echo csrf_token(); ?>', 'category_id': category_id},
               success: function (data) {
                   if(data['result'] === true){
                       $(".error_cat_delete").empty();
                       $("."+data['category_id']).remove();
                   }else{
                       $(".error_cat_delete").text('First delete post under this category.').css({'color': 'red', 'padding-left': '50px'});
                   }
               }
           })
       }
   }
</script>