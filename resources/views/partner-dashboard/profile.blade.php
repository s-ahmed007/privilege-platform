@include('partner-dashboard.header')
<style>
    input{
        color: #000 !important;}
</style>
    <div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-5 col-md-4 col-sm-4 col-xs-12">
            <h3 class="d-inline-block">{{__('partner/profile.your_profile')}}</h3>
                <h5 class="d-inline-block float-right">{{__('partner/profile.find_your_profile_details')}}</h5>
        </div>
    </div>
        <div class="row">
            <div class="col-md-4 col-xs-12">
                <div class="white-box">
                    <div class="user-bg"> <img width="100%" alt="user" src="{{$user->branchScanner->branch->info->profileImage->partner_profile_image}}">
                        <div class="overlay-box">
                            <div class="user-content">
                                <a href="javascript:void(0)"><img src="{{$user->branchScanner->branch->info->profileImage->partner_profile_image}}"
                                        class="thumb-lg img-circle" alt="img"></a>
                                <h4 class="text-white">{{$user->branchScanner->branch->info->partner_name}}</h4>
                                <h5 class="text-white">{{$user->branchScanner->branch->partner_area}}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="user-btm-box">
                        <div class="col-md-4 col-sm-4 text-center">
                            <p class="text-purple"><i class="ti-facebook"></i></p>
                            <h1>{{$data['total_transaction']}}</h1>
                            <p>{{__('partner/profile.transaction')}}</p>
                        </div>
                        <div class="col-md-4 col-sm-4 text-center">
                            <p class="text-blue"><i class="ti-twitter"></i></p>
                            <h1>{{$data['total_review']}}</h1>
                            <p>{{__('partner/profile.review')}}</p>
                        </div>
                        <div class="col-md-4 col-sm-4 text-center">
                            <p class="text-danger"><i class="ti-dribbble"></i></p>
                            <h1>{{round($data['rating'], 1)}}</h1>
                            <p>{{__('partner/profile.rating')}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8 col-xs-12">
                <div class="white-box">
                    <form class="form-horizontal form-material">
                        <div class="form-group">
                            <label class="col-md-12">{{__('partner/profile.full_name')}}</label>
                            <div class="col-md-12">
                                <input type="text" placeholder="Name" value="{{$user->branchScanner->full_name}}"
                                    class="form-control form-control-line" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-12">{{__('partner/profile.branch_name')}}</label>
                            <div class="col-md-12">
                                <input type="text" placeholder="Branch Name" value="{{$user->branchScanner->branch->info->partner_name.', '.
                                        $user->branchScanner->branch->partner_area}}"
                                    class="form-control form-control-line" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-12">{{__('partner/profile.phone_number')}}</label>
                            <div class="col-md-12">
                                <input type="text" placeholder="Phone Number" value="{{$user->phone}}"
                                    class="form-control form-control-line" maxlength="14" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-12">{{__('partner/profile.pin')}}</label>
                            <div class="col-md-12">
                                <input type="text" value="{{$user->pin_code}}" class="form-control form-control-line"  readonly
                                       maxlength="4">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="example-email" class="col-md-12">{{__('partner/profile.email')}}</label>
                            <div class="col-md-12">
                                <input type="email" placeholder="johnathan@admin.com"
                                    class="form-control form-control-line" name="example-email"
                                    id="example-email" value="{{$user->branchScanner->branch->partner_email}}" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="exaample-address" class="col-md-12">{{__('partner/profile.address')}}</label>
                            <div class="col-md-12">
                                <input type="text" placeholder=""
                                    class="form-control form-control-line" name="example-address"
                                     value="{{$user->branchScanner->branch->partner_address}}" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                            <label class="col-md-12">{{__('partner/profile.contact_to_update_info')}}</label>
                                <!-- <button class="btn btn-success">Update Profile</button> -->
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
    <footer class="footer text-center"> 2020 &copy; Royalty Inc </footer>

@include('partner-dashboard.footer')
