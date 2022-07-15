@include('admin.production.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>
<div class="right_col" role="main">
   <div class="page-title">
      <div class="title_left">
         @if(session('news deleted'))
         <div class="alert alert-danger">
            {{ session('news deleted') }}
         </div>
         @elseif(session('news updated'))
         <div class="alert alert-success">
            {{ session('news updated') }}
         </div>
         @elseif(session('news added'))
         <div class="alert alert-success">
            {{ session('news added') }}
         </div>
         @elseif(session('try_again'))
         <div class="alert alert-warning">
            {{ session('try_again') }}
         </div>
         @endif
         <h3>News Create/Edit/Delete/Display</h3>
         <a type="button" class="btn btn-create" href="{{ url('/pressAdmin') }}" style="margin-left: unset;">+ Create New News</a>
      </div>
      <div class="title_right">
         <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
         </div>
      </div>
   </div>
   <div class="clearfix"></div>
   <div class="container">
      <div class="row">
         <div class="col-xs-12">
            <div class="table-responsive">
               @if($allNews)
               <table id="NewsList" class="table table-bordered table-hover table-striped projects">
                  <thead>
                     <tr>
                        <th style="width: 10%">Id</th>
                        <th style="width: 30%">Image</th>
                        <th style="width: 10%">Name</th>
                        <th style="width: 20%">Sub title</th>
                        <th>Action</th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach ($allNews as $news)
                     <tr>
                        <td>{{$news['id']}}</td>
                        <td><img src="{{asset($news['press_image'])}}" width="100%" height="200px"></td>
                        <td>{{ $news['press_name'] }}</td>
                        <td>{{ $news['sub_title'] }}</td>
                        <td><a class="btn btn-primary"
                           href="{{url('/edit-news/'.$news['id'])}}"> <i class="fa fa-edit"></i></a>
                        <a class="btn btn-delete" href="{{url('/delete-news/'.$news['id'])}}"
                           onclick="return confirm('Are you sure?')"><i class="fa fa-trash-alt"></i></a></td>
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
                  {{ 'No news found.' }}
               </div>
               @endif
            </div>
         </div>
      </div>
   </div>
</div>
@include('admin.production.footer')
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
   $(document).ready(function () {
       $('#NewsList').DataTable({
           //"paging": false
           "order": []
       });
   });
</script>