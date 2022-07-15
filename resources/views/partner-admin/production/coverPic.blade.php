@if(!session()->has('partner_admin'))
    <script type="text/javascript">
        window.location = "{{ url('/') }}";
    </script>
@endif
@include('partner-admin.production.header')
<!-- page content -->
<div class="right_col" role="main">
  <div class="page-title">
    <div class="title_left">
      <h3>Cover Photo</h3>
    </div>
    @if (Session::has('updated'))
      <div class="title_right alert alert-success" style="text-align: center;">{{ Session::get('updated') }}</div>
    @elseif(session('try_again'))
        <div class="title_right alert alert-warning" style="text-align: center;"> {{ session('try_again') }} </div>
    @endif
  </div>
  <div class="clearfix"></div>
  @if($coverPic['partner_cover_photo']!=null)
  <img src="{{asset($coverPic['partner_cover_photo'])}}" alt="Profile Cover Photo"><br>
  @endif
  <div class="panel-body">
  	<form action="{{url('updateCoverPic')}}" method="post" enctype="multipart/form-data">
        {{csrf_field()}}
        <input type="file" name="coverPic" required><br>
        <button class="btn btn-activate pull-right">Update Cover Photo</button>
  	</form>
  </div>
</div>
@include('partner-admin.production.footer')