@include('admin.production.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>
<div class="right_col" role="main">
   <div class="page-title">
      <div class="title_left">
         @if (session('deleted'))
            <div class="alert alert-danger">
               {{ session('deleted') }}
            </div>
         @endif
         <h3>Influencer Requests</h3>
      </div>
   </div>
   <div class="clearfix"></div>
   <div class="container">
      <div class="row">
         <div class="col-xs-12">
            <div class="table-responsive">
               @if($influencer_requests)
               <table id="influencerList" class="table table-bordered table-hover table-striped projects">
                  <thead>
                     <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Blog</th>
                        <th>Category</th>
                        <th>Facebook</th>
                        <th>Instagram</th>
                        <th>You Tube</th>
                        <th>Website</th>
                        <th>Posted On</th>
                        <th>Action</th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach ($influencer_requests as $request)
                     <tr class="promo_row" data-request-id='{{ $request->id }}'>
                        <td><span style="font-size: 14px;">{{ $request->full_name }}</span></td>
                        <td><span style="font-size: 14px;">{{ $request->email }}</span></td>
                        <td><span style="font-size: 14px;">{{ $request->blog_name }}</span></td>
                        <td><span style="font-size: 14px;">{{ $request->blog_category }}</span></td>
                        <td><span style="font-size: 14px;">{{ $request->facebook_link }}</span></td>
                        <td><span style="font-size: 14px;">{{ $request->instagram_link }}</span></td>
                        <td><span style="font-size: 14px;">{{ $request->youtube_link }}</span></td>
                        <td><span style="font-size: 14px;">{{ $request->website_link }}</span></td>
                        <td><span style="font-size: 14px;">
                           {{isset($request->posted_on) ? date("d M, Y", strtotime($request->posted_on)) : '' }}
                           </span>
                        </td>
                        <td>
                           <button class="btn btn-delete deleteBtn" data-request-id='{{ $request->id }}'>
                              <i class="fa fa-trash-alt"></i>
                           </button>
                        </td>
                     </tr>
                     @endforeach
                  </tbody>
               </table>
               @else
               <div style="font-size: 1.4em; color: red;">
                  {{ 'No request found.' }}
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
========================Opening edit & delete====================
============================================================================================================= --}}
<script type="text/javascript">
    $('.deleteBtn').on('click', function (event) {
        if (confirm("Are you sure?")) {
            //fetch the blog id
            var requestId = $(this).attr('data-request-id');
            var url = "{{ url('/delete_influencer_request/') }}";
            url += '/' + requestId;

            $('<form action="' + url + '" method="POST">' +
                '<input type="hidden" name="_token" value="{{ csrf_token() }}"/>' +
                '<input type="hidden" name="_method" value="DELETE"/>' +
                '</form>').appendTo($(document.body)).submit();
        }
        return false;
    });

   $(document).ready(function () {
       $('#influencerList').DataTable({
           //"paging": false
           "order": []
       });
   });
</script>