@include('admin.production.header')

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
        </div>
    </div>
    <h3>Create A New Branch Scanner of {{$branch->info->partner_name.', '.$branch->partner_area}}</h3>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <form class="form-horizontal form-label-left" method="post" action="{{ url('/store-branch-scanner/'.$branch->id) }}"
                        enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group">
                            <span style="color: #E74430;">
                            @if ($errors->getBag('default')->first('full_name'))
                                    {{ $errors->getBag('default')->first('full_name') }}
                                @endif
                            </span>
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Full Name:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="text" class="form-control" placeholder="Enter scanner's full name" value="{{old('full_name')}}" name="full_name"/>
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
                                <input type="text" class="form-control" placeholder="Enter scanner's username" value="{{old('username')}}" name="username"/>
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
                                <input type="text" class="form-control" name="phone_number" maxlength="14" minlength="14" value="+88"
                                    placeholder="Phone Number with country code">
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
                                <input type="text" class="form-control" placeholder="Enter a 4 digit PIN" name="pin_code" maxlength="4" minlength="4"/>
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
                                <input type="text" class="form-control" placeholder="Enter scanner's designation" value="{{old('designation')}}" name="designation">
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
                                    <!-- <option value="{{\App\Http\Controllers\Enum\BranchUserRole::branchScanner}}">Cashier/Manager</option> -->
                                    <option value="{{\App\Http\Controllers\Enum\BranchUserRole::branchOwner}}">Owner</option>
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
