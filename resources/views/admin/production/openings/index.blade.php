@include('admin.production.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>
<div class="right_col" role="main">
   <div class="page-title">
      <div class="title_left">
         @if (session('status'))
         <div class="alert alert-success">
            {{ session('status') }}
         </div>
         @elseif (session('delete opening'))
         <div class="alert alert-danger">
            {{ session('delete opening') }}
         </div>
         @elseif(session('try_again'))
         <div class="alert alert-warning">
            {{ session('try_again') }}
         </div>
         @endif
         <h3>Job Openings Create/Edit/Delete/Display</h3>
         <a type="button" class="btn btn-create" href="{{ url('/openings/create') }}" style="margin-left: unset;">+ Create A New Job</a>
      </div>
   </div>
   <div class="clearfix"></div>
   <div class="container">
      <div class="row">
         <div class="col-xs-12">
            <div class="table-responsive">
               @if($openings)
               <table id="openingsList" class="table table-bordered table-hover table-striped projects">
                  <thead>
                     <tr>
                        <th>Created At</th>
                        <th>Position</th>
                        <th>Duration</th>
                        <th>Salary</th>
                        <th>Deadline</th>
                        <th>Action</th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach ($openings as $opening)
                     <tr class="opening_row" data-opening-id='{{ $opening->id }}'>
                        <td>{{ date('Y-m-d',strtotime($opening->created_at)) }}</td>
                        <td>{{ $opening->position }}</td>
                        <td>{{ $opening->duration }}</td>
                        <td>{{ $opening->salary }}</td>
                        <td>{{ date('Y-m-d',strtotime($opening->deadline)) }}</td>
                        <td>
                           <button class="btn btn-edit editBtn" title="Edit"
                              data-opening-id='{{ $opening->id }}'>
                           <i class="fa fa-edit"></i>
                           </button>
                           <button class="btn btn-delete deleteBtn" title="Delete"
                              data-opening-id='{{ $opening->id }}'>
                           <i class="fa fa-trash-alt"></i>
                           </button>
                           @if($opening->active == 1)
                           <button class="btn btn-deactivate deactiveBtn" title="Deactivate"
                              data-opening-id='{{ $opening->id }}'>
                           <i class="glyphicon glyphicon-pause"></i>
                           </button>
                           @else
                           <button class="btn btn-activate activeBtn" title="Activate"
                              data-opening-id='{{ $opening->id }}'>
                           <i class="glyphicon glyphicon-play"></i>
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
                  {{ 'No Opening found.' }}
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
<script>
    $('.deleteBtn').on('click', function (event) {
        if (confirm("Are you sure?")) {
            //fetch the opening id
            var openingId = $(this).attr('data-opening-id');
            var url = "{{ url('/openings') }}";
            url += '/' + openingId;

            $('<form action="' + url + '" method="POST">' +
                '<input type="hidden" name="_token" value="{{ csrf_token() }}"/>' +
                '<input type="hidden" name="_method" value="DELETE"/>' +
                '</form>').appendTo($(document.body)).submit();
        }
        return false;
    });

    $('.editBtn').on('click', function (event) {
        //fetch the opening id
        var openingId = $(this).attr('data-opening-id');
        var url = "{{ url('/openings') }}";
        url += '/' + openingId + '/edit';
        window.location.href = url;
    });

    $('.activeBtn').on('click', function (event) {
        //fetch the opening id
        var openingId = $(this).attr('data-opening-id');
        var url = "{{ url('/active-opening') }}" + '/' + openingId;


        $('<form action="' + url + '" method="POST">' +
            '<input type="hidden" name="_token" value="{{ csrf_token() }}"/>' +
            '</form>').appendTo($(document.body)).submit();
    });

    $('.deactiveBtn').on('click', function (event) {
        //fetch the opening id
        var openingId = $(this).attr('data-opening-id');
        var url = "{{ url('/deactive-opening') }}" + '/' + openingId;


        $('<form action="' + url + '" method="POST">' +
            '<input type="hidden" name="_token" value="{{ csrf_token() }}"/>' +
            '</form>').appendTo($(document.body)).submit();
    });
</script>
<script type="text/javascript">
   $(document).ready(function () {
       $('#openingsList').DataTable({
           //"paging": false
           "order": []
       });
   });
</script>