@include('admin.production.header')
<?php use \App\Http\Controllers\functionController;?>
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @elseif (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            <h3>Edit Scanner User</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <form class="form-horizontal form-label-left" method="post" action="{{ url('/update-branch-scanner/'.$user->branch_user_id) }}"
                          enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group">
                            <span style="color: #E74430;">
                                @if ($errors->getBag('default')->first('full_name'))
                                    {{ $errors->getBag('default')->first('full_name') }}
                                @endif
                            </span>
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Full name:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="text" class="form-control" name="full_name" placeholder="Full Name" value="{{ $user->full_name }}">
                            </div>
                        </div>
                        <div class="form-group">
                             <span style="color: #E74430;">
                                @if ($errors->getBag('default')->first('username'))
                                     {{ $errors->getBag('default')->first('username') }}
                                 @endif
                             </span>
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Username:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="text" class="form-control" placeholder="Username" name="username" value="{{$user->branchUser->username}}" />
                            </div>
                        </div>
                        <div class="form-group">
                            <span style="color: #E74430;">
                                @if ($errors->getBag('default')->first('phone_number'))
                                    {{ $errors->getBag('default')->first('phone_number') }}
                                @endif
                            </span>
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Phone Number:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="text" class="form-control" name="phone_number" maxlength="14" minlength="14" placeholder="Phone Number with country code" value="{{ $user->branchUser->phone }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <span style="color: #E74430;">
                                @if ($errors->getBag('default')->first('pin_code'))
                                    {{ $errors->getBag('default')->first('pin_code') }}
                                @endif
                            </span>
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">PIN Code:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="text" class="form-control" placeholder="PIN (4 digit)" name="pin_code" maxlength="4" minlength="4"
                                       value="<?php if($user->branchUser->pin_code != null) echo $user->branchUser->pin_code;?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <span style="color: #E74430;">
                                @if ($errors->getBag('default')->first('designation'))
                                    {{ $errors->getBag('default')->first('designation') }}
                                @endif
                            </span>
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Designation:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="text" class="form-control" placeholder="Designation" name="designation" value="{{$user->designation}}" >
                            </div>
                        </div>
                        <div class="form-group">
                            <span style="color: #E74430;">
                            @if ($errors->getBag('default')->first('branch_user_role'))
                                    {{ $errors->getBag('default')->first('branch_user_role') }}
                                @endif
                            </span>
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Role:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <select class="form-control" name="branch_user_role" id="">
                                    <!-- <option value="{{\App\Http\Controllers\Enum\BranchUserRole::branchScanner}}"
                                    {{$user->branchUser->role == \App\Http\Controllers\Enum\BranchUserRole::branchScanner ? 'selected': ''}}>Cashier/Manager</option> -->
                                    <option value="{{\App\Http\Controllers\Enum\BranchUserRole::branchOwner}}"
                                        {{$user->branchUser->role == \App\Http\Controllers\Enum\BranchUserRole::branchOwner ? 'selected': ''}}>Owner</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                                <button type="submit" class="btn btn-activate pull-right">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.production.footer')
<script>
    $('#file-fr').fileinput({
        language: 'fr',
        uploadUrl: '#',
        allowedFileExtensions: ['jpg', 'png', 'gif']
    });
    $('#file-es').fileinput({
        language: 'es',
        uploadUrl: '#',
        allowedFileExtensions: ['jpg', 'png', 'gif']
    });
    $("#file-0").fileinput({
        'allowedFileExtensions': ['jpg', 'png', 'gif']
    });
    $("#file-1").fileinput({
        uploadUrl: '#', // you must set a valid URL here else you will get an error
        allowedFileExtensions: ['jpg', 'png', 'gif'],
        overwriteInitial: false,
        maxFileSize: 1000,
        maxFilesNum: 10,
        //allowedFileTypes: ['image', 'video', 'flash'],
        slugCallback: function (filename) {
            return filename.replace('(', '_').replace(']', '_');
        }
    });


    $(document).ready(function () {
        $("#test-upload").fileinput({
            'showPreview': false,
            'allowedFileExtensions': ['jpg', 'png', 'gif'],
            'elErrorContainer': '#errorBlock'
        });
        $("#kv-explorer").fileinput({
            'theme': 'explorer',
            'uploadUrl': '#',
            overwriteInitial: false,
            initialPreviewAsData: true,
            initialPreview: [
                "http://lorempixel.com/1920/1080/nature/1",
                "http://lorempixel.com/1920/1080/nature/2",
                "http://lorempixel.com/1920/1080/nature/3"
            ],
            initialPreviewConfig: [
                {caption: "nature-1.jpg", size: 329892, width: "120px", url: "{$url}", key: 1},
                {caption: "nature-2.jpg", size: 872378, width: "120px", url: "{$url}", key: 2},
                {caption: "nature-3.jpg", size: 632762, width: "120px", url: "{$url}", key: 3}
            ]
        });
    });
</script>