@include('admin.production.header')

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Add new hotspot</h3>
            @if (Session::has('added'))
                <div class="title_right alert alert-success"
                     style="text-align: center;">{{ Session::get('updated') }}</div>
            @endif
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <form class="form-horizontal form-label-left" method="post" action="{{ url('addHotspot') }}"
                          enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Name</label>
                            <span style="color: red;">
                                         @if ($errors->getBag('default')->first('name'))
                                    {{ $errors->getBag('default')->first('name') }}
                                @endif
                                        </span>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="text" class="form-control" placeholder="Name" name="name"
                                       value="{{old('name')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Description</label>
                            <span style="color: red;">
                                         @if ($errors->getBag('default')->first('description'))
                                    {{ $errors->getBag('default')->first('description') }}
                                @endif
                                        </span>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="text" class="form-control" placeholder="Description" name="description"
                                       value="{{old('description')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Select Profile Image</label>
                            <span style="color: red;">
                                         @if ($errors->getBag('default')->first('profile_image'))
                                    {{ $errors->getBag('default')->first('profile_image') }}
                                @endif
                                        </span>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input id="file-0c" class="file " name="profile_image" type="file">
                            </div>
                        </div>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="ln_solid"></div>
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
    <div class="page-title">
        <div class="title_left">
            <h3>Add partner to hotspot</h3>
            @if (Session::has('partner_added_to_hotspot'))
                <div class="title_right alert alert-success"
                     style="text-align: center;">{{ Session::get('partner_added_to_hotspot') }}</div>
            @endif
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <form class="form-horizontal form-label-left" method="post"
                          action="{{ url('/addPartnerToHotspot') }}" id="addHotspotToPartnerForm">
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Select Hotspot</label>
                            <span style="color: red;">
                                         @if ($errors->getBag('default')->first('hotspot'))
                                    {{ $errors->getBag('default')->first('hotspot') }}
                                @endif
                                        </span>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <select name="hotspot" class="form-control">
                                    <option disabled selected>----------</option>
                                    @foreach($allHotspots as $hotspot)
                                        <option value="{{$hotspot->id}}">{{$hotspot->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Select Partner</label>
                            <span style="color: red;">
                                         @if ($errors->getBag('default')->first('partner'))
                                    {{ $errors->getBag('default')->first('partner') }}
                                @endif
                                        </span>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <select name="partner_branch" class="form-control" id="partnerSelect">
                                    <option disabled selected value="0">----------</option>
                                    @if(isset($allPartners))
                                        @foreach ($allPartners as $key => $value)
                                            @if(count($value->branches) > 0)
                                                @foreach($value->branches as $key2 => $partner)
                                                    <option value="{{$partner['id']}}">{{$value->partner_name .' ('. $partner->partner_area.')'}}</option>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="ln_solid"></div>
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
</div>
</div>
@include('admin.production.footer')

<script type="text/javascript">
    $("#partnerSelect").change(function () {
        var partner_account_id = $("#partnerSelect").val();
        if (partner_account_id == 0) {
            $('#branchSelect').html("");
            $('.selectBranch').attr("hidden");
            return false;
        }

        var url = "{{ url('/get-branches-by-partner') }}";
        url += '/' + partner_account_id;

        $.ajax({
            type: 'GET',
            url: url,
        }).done(function (response) {
            console.clear();
            console.log(response);
            console.log(response.length);
            $('#branchSelect').html("");
            if (response.length > 0) {
                response.forEach(function (item) {
                    $('#branchSelect').append("<option value='" + item.id + "'>" + item.username + "</option>");
                });
                $('.selectBranch').removeAttr("hidden");
            } else {
                $('.selectBranch').attr("hidden");
            }

        });
    });
</script>