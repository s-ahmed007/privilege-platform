@include('admin.production.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>
<style>
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
                <div><span class="success-d">{{ session('status') }}</span></div>
            @elseif (session('delete branch'))
                <div><span class="delete-d">{{ session('delete branch') }}</span></div>
            @elseif (session('delete partner'))
                <div><span class="delete-d">{{ session('delete partner') }}</span></div>
            @elseif(session('try_again'))
                <div><span class="try-d">{{ session('try_again') }}</span></div>
            @elseif(session('main_branch_deactivate_msg'))
                <div><span class="deactivate-d">{{ session('main_branch_deactivate_msg') }}</span></div>
            @endif
            <a class="btn btn-all" href="{{ url('allPartners/activated') }}">Active</a>
            <a class="btn btn-deactivate" href="{{ url('allPartners/deactivated') }}">Deactive</a>
            <a class="btn btn-trial" href="{{ url('allPartners/about_to_expire') }}">About to Expire</a>
            <a class="btn btn-expired" href="{{ url('allPartners/expired') }}">Expired</a>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="table-responsive">
                    <table id="partnerList" class="table table-bordered table-hover table-striped projects">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Partner Name</th>
                            <th>Email/Mobile</th>
                            <th>Expiry</th>
                            <th>Address</th>
                            <th>Action</th>
                            <th>Change Branch Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($allPartners))
                            <?php $i=1; ?>
                            @foreach ($allPartners as $key => $value)
                                <?php
                                    if($status == 'active'){
                                        $all_branches = $value->activeBranches;
                                    }else{
                                        $all_branches = $value->deactiveBranches;
                                    }
                                ?>
                                @foreach($all_branches as $key2 => $partner)
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>{{ $value->info->partner_name .' ('. $partner->partner_area.')'}}<br>
                                            <span>{{$value->info->category->name ?? 'Not found'}}</span><br>
                                        </td>
                                        <td>{{$partner->partner_email != '0' ? $partner->partner_email : 'No e-mail'}}<br>{{$partner->partner_mobile}}</td>
                                        <?php
                                        $today = date('Y-m-d');
                                        if($value->info->expiry_date >= $today){
                                            $will_expire = \Carbon\Carbon::parse($value->info->expiry_date)->isCurrentMonth(true);
                                            $exp_date = strtotime($value->info->expiry_date);
                                            $datediff = $exp_date - strtotime($today);
                                            $diff = round($datediff / (60 * 60 * 24));
                                            if($will_expire){
                                                $color = 'yellow';
                                                $text = $diff > 1 ? $diff.' days left' : $diff.' day left';
                                            }else{
                                                $color = 'limegreen';
                                                $text = 'Active';
                                            }
                                        }elseif($value->info->expiry_date < $today){
                                            $color = 'red';
                                            $text = 'Expired';
                                        }
                                        ?>
                                        <td style="background-color: {{$color}}" title="{{$text}}">{{ date("F d, Y", strtotime($value->info->expiry_date)) }}</td>
                                        <td>{{ $partner->partner_address }}</td>
                                        <td align="center">
                                            <select id="partner_edit_{{$partner->id}}" class="selectChangeOff"
                                                onchange="partner_edit({{$value->partner_account_id}},{{$partner->id}})">
                                                <option disabled selected>--Options--</option>
                                                <option value="1">Edit Info</option>
                                                <option value="2">Profile Image</option>
                                                <option value="3">Gallery Image</option>
                                                <option value="4">Offers</option>
                                                {{--<option value="5">Add Owner</option>--}}
                                                <option value="6">Subcategory</option>
                                                <option value="7">Rewards</option>
                                                <option value="8">Cover Photo</option>
                                                <option value="9">Menu Image</option>
                                                <option value="10">Deals</option>
                                            </select>
                                            @if(Session::get('admin') == \App\Http\Controllers\Enum\AdminRole::superadmin
                                                   ||Session::get('admin') == \App\Http\Controllers\Enum\AdminRole::admin)
                                            <br><br>
                                            <a href="{{url('branch-qr/'.$partner->partner_account_id.'/'.$partner->id)}}"
                                               class="btn btn-success"><i class="glyphicon glyphicon-qrcode"></i></a>
                                            @endif
                                        </td>
                                        <td>
                                        @if(Session::get('admin') == \App\Http\Controllers\Enum\AdminRole::superadmin)

                                            @if($value->active == 1)
                                                <?php
                                                if ($partner->active == 1) {
                                                    $change_to = 0;
                                                    $change_to_text = "Deactivate";
                                                    echo '<button class="btn btn-deactivate changeStatusBtn" data-partner-branch-id="' . $partner->id . '">' . $change_to_text . '</button>';
                                                } else {
                                                    $change_to = 1;
                                                    $change_to_text = "Activate";
                                                    echo '<button class="btn btn-activate changeStatusBtn" data-partner-branch-id="' . $partner->id . '">' . $change_to_text . '</button>';
                                                }
                                                ?>
                                            @endif
                                            <button class="btn btn-delete deleteBranchBtn" data-partner-branch-id="{{$partner->id}}">
                                                <i class="fa fa-trash-alt"></i></button>
                                        @elseif(Session::get('admin') == \App\Http\Controllers\Enum\AdminRole::admin)
                                            <?php
                                            if ($partner->active == 1) {
                                                $change_to = 0;
                                                $change_to_text = "Deactivate";
                                                echo '<button class="btn btn-deactivate" disabled>' . $change_to_text . '</button>';
                                            } else {
                                                $change_to = 1;
                                                $change_to_text = "Activate";
                                                echo '<button class="btn btn-activate pull-right" disabled>' . $change_to_text . '</button>';
                                            }
                                            ?>
                                        @endif
                                        </td>
                                    </tr>
                                    <?php $i++; ?>
                                @endforeach
                            @endforeach
                        @else
                            <div style="font-size: 1.4em; color: red;">
                                {{ 'Partner not found.' }}
                            </div>
                        @endif
                        </tbody>
                        <tfoot>
                        <tr>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- refer bonus request Modal-->
<div id="ownersModal" class="modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><i class="cross-icon"></i></button>
                <h4 class="modal-title">Select Owner</h4>
            </div>
            <div class="modal-body" id="profile_modal" class="profile_modal center" style="text-align: center">
                <p>Select an owner </p>
                <div class="row">
                    <div class="col-lg-3"></div>
                    <div class="col-md-6">
                        <select id="ownerList" class="form-control">
                            <option disabled selected>Owner</option>
                            @foreach($owners as $owner)
                                <option value="{{$owner->id}}">{{$owner->name.'('.$owner->phone.')'}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" id="branch_id">
                        <button type="submit" onclick="addOwner()" class="btn btn-primary">ASSIGN</button>
                    </div>
                    <div class="col-md-3 loading_time">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function addOwner() {
        $(".loading_time").html("<img src='https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/icon/loading.gif'>");
        var owner = document.getElementById('ownerList');
        var branch_id = $('#branch_id').val();
        var url = "{{url('/assign-owner')}}";
        $.ajax({
            type: 'POST',
            url: url,
            data: {'_token': '<?php echo csrf_token(); ?>', 'owner_id': owner.value, 'branch_id': branch_id},
            success: function (data) {
                if(data === 1){
                    $(".loading_time").html("<i style='font-size: 2em; color: limegreen;' class=\"fas fa-check\"></i>");
                }else{
                    $(".loading_time").html("<i style='font-size: 2em; color: red;' class=\"fas fa-times\"></i>");
                }
            }
        });
    }
    function partner_edit(partner_id, branch_id) {
        var option_type = document.getElementById("partner_edit_" + branch_id).value;
        if (option_type == 1) {
            var url = "{{url('/edit_partner')}}" + '/' + branch_id;
            window.location.href = url;
        } else if (option_type == 2) {
            var url = "{{url('/edit_pro_pic')}}" + '/' + partner_id;
            window.location.href = url;
        } else if (option_type == 3) {
            var url = "{{url('/partner-gallery-images')}}" + '/' + partner_id;
            window.location.href = url;
        } else if (option_type == 4) {
            var url = "{{url('/branch-offers')}}" + '/' + branch_id;
            window.location.href = url;
        } else if (option_type == 5) {
            var url = "{{url('/get-branch-owner')}}";
            $.ajax({
                type: 'POST',
                url: url,
                data: {'_token': '<?php echo csrf_token(); ?>', 'branch_id': branch_id},
                success: function (data) {
                    if(data['owner'] != null){
                        $("#ownerList option[value="+data['owner']['id']+"]").prop('selected', true);
                    }else{
                        $("#ownerList option[disabled]").prop('selected', true);
                    }
                    $("#branch_id").val(branch_id);
                    $(".loading_time").html("");
                    $("#ownersModal").modal('toggle');
                }
            });
        }else if (option_type == 6){
            var url = "{{url('/partner-subcategory')}}" + '/' + partner_id;
            window.location.href = url;
        }else if (option_type == 7){
            var url = "{{url('/admin/reward')}}" + '/' + branch_id;
            window.location.href = url;
        }else if (option_type == 8){
            var url = "{{url('/admin/partner_cover_photo')}}" + '/' + partner_id;
            window.location.href = url;
        }else if (option_type == 9){
           var url = "{{url('/admin/partner-menu-images')}}" + '/' + partner_id;
           window.location.href = url;
       }else if (option_type == 10){
           var url = "{{url('/admin/vouchers')}}" + '/?branch_id=' + branch_id;
           window.location.href = url;
       }
    }
</script>
<script>
    function hotspot_exists(partner_id, branch_id) {
        var url = "{{ url('/existsInTrendingBrands') }}";
        $.ajax({
            type: "POST",
            url: url,
            data: {'_token': '<?php echo csrf_token(); ?>', 'partner_id': partner_id, 'branch_id': branch_id},
            success: function (data) {
                if (data['status'] === 'exists_in_trending') {
                    alert('Delete the Partner from Top Brands first');
                } else if (data['status'] === 'exists_in_top') {
                    alert('Delete the Partner from Trending Offers first');
                } else if (data['status'] === 'exists_in_trending_top') {
                    alert('Delete the Partner from Top Brands and Trending Offers first');
                } else if (data['status'] === 'delete_partner') {
                    var result = confirm('Are you sure?');
                    if (result == true) {
                        window.location = "{{url('/deletePartner/')}}" + "/" + data['id'];
                    }
                } else if (data['status'] === 'delete_branch') {
                    var result = confirm('Are you sure?');
                    if (result == true) {
                        window.location = "{{url('/deleteBranch/')}}" + "/" + data['id'];
                    }
                }
            }
        });
    }
</script>
@include('admin.production.footer')
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
    $('.changeStatusBtn').on('click', function (event) {
        if (confirm("Are you sure?")) {
            //fetch the partner branch id
            var partnerBranchId = $(this).attr('data-partner-branch-id');
            var url = "{{ url('/partner-branch-change-status/partner') }}";
            url += '/' + partnerBranchId;

            $('<form action="' + url + '" method="POST">' +
                '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
                '</form>').appendTo($(document.body)).submit();

        }
        return false;
    });
    $('.deleteBranchBtn').on('click', function (event) {
        if (confirm("Are you sure?")) {
            //fetch the partner branch id
            var partnerBranchId = $(this).attr('data-partner-branch-id');
            var url = "{{ url('/delete-branch') }}";
            url += '/' + partnerBranchId;

            $('<form action="' + url + '" method="POST">' +
                '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
                '</form>').appendTo($(document.body)).submit();
        }
        return false;
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#partnerList').DataTable({
            //"paging": false
            "order": []
        });
    });
    //to keep select option unselected from prev page
    $(document).ready(function () {
        $(".selectChangeOff").each(function () {
            $(this).val($(this).find('option[selected]').val());
        });
    })
</script>