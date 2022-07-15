@include('admin.production.header')
{{--Crop image--}}
<script src="{{asset('js/imageCrop/jquery.js')}}"></script>
<script src="{{asset('js/imageCrop/croppie.js')}}"></script>
<link href="{{asset('admin/vendors/croppie/croppie.css')}}" rel="stylesheet">

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Profile Image</h3>
        </div>
        @if (Session::has('updated'))
            <div class="title_right alert alert-success" style="text-align: center;">{{ Session::get('updated') }}</div>
        @elseif(session('try_again'))
            <div class="title_right alert alert-warning" style="text-align: center;"> {{ session('try_again') }} </div>
        @endif
    </div>
    <div class="clearfix"></div>
    @if($proPic['partner_profile_image']!=null)
        <img src="{{asset($proPic['partner_profile_image'])}}" alt="Profile Image"><br>
    @endif
    <?php $partner_id = $proPic['partner_account_id']; ?>
    <div class="panel-body">
        <form action="{{url('updateProPic/'.$partner_id)}}" method="post" enctype="multipart/form-data">
            {{csrf_field()}}
            <input type="file" name="proPic" required><br>
            <button class="btn btn-activate pull-right">Update Cover Photo</button>
        </form>
    </div>
</div>
@include('admin.production.footer')