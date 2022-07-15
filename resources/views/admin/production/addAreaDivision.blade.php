@include('admin.production.header')

<div class="right_col" role="main">
    <div class="page-title">
    <div class="title_left">
    <h3>Add Divisions & Areas</h3>
        </div>
    </div>
    <div class="title_right">
        <div class="col-sm-3 col-xs-12 form-group pull-right">
            @if (session('area_deleted'))
                <div class="alert alert-danger">
                    {{ session('area_deleted') }}
                </div>
            @elseif(session('area_added'))
                <div class="alert alert-success">
                    {{ session('area_added') }}
                </div>
            @elseif (session('division_added'))
                <div class="alert alert-success">
                    {{ session('division_added') }}
                </div>
            @endif    
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <h4>Add A New Division : </h4>
            <div class="x_panel">
                <div class="x_content">
                    <form class="form-inline" action="{{url('add-division')}}" method="post">
                        {{csrf_field()}}
                        @if ($errors->getBag('default')->first('division'))
                            <span style="color: red;">
                                {{ $errors->getBag('default')->first('division') }}
                            </span>  
                        @endif
                        <br>
                        <div class="form-group">
                            <label for="division">Division:</label>
                            <input type="text" class="form-control" name="division" id="division">
                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-activate pull-right" style="margin-bottom: 0">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12">
            <h4>Add A New Area : </h4>
            <div class="x_panel">
                <div class="x_content">
                    <form class="form-inline" action="{{url('add-area')}}" method="post">
                        {{csrf_field()}}
                        <div class="form-group">
                            <label for="division">Division:</label>
                            <select class="form-control" name="division_name">
                                <option selected disabled>Select Division</option>
                                @foreach($divisions as $division)
                                    <option value="{{$division->id}}">{{$division->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            @if(session('area_duplicate'))
                                <span style="color: red;">
                                    {{ session('area_duplicate') }}
                                </span>
                                <br>
                            @endif
                            <label for="area">Area:</label>
                            <input type="text" class="form-control" name="area_name">
                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-activate pull-right" style="margin-bottom: 0;">
                        </div>
                        {{--<button type="submit" class="btn btn-default">Submit</button>--}}
                    </form>
                </div>
            </div>
        </div>
        <hr>
        <h3 style="margin-left:10px;">All Divisions & Areas</h3>
        <!-- <h3>All Divisions & Areas</h3> -->
            <div class="col-md-6 col-sm-6 col-xs-12">
                <h4>Select Division : </h4>
                <div class="x_panel">
                    <div class="x_content">
                    <select class="form-control" name="division" id="sel_div" onchange="select_division()">
                        <option selected disabled>-----</option>
                        @foreach($divisions as $division)
                        <option value="{{$division['id']}}">{{$division['name']}}</option>
                        @endforeach
                    </select>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-12">
            <h4>List of Areas : </h4>
                <div class="x_panel">
                    <div class="x_content" id="area_section">
                        Please Select a Division
                    </div>
                </div>
            </div>
    </div>
</div>
<script>
    function select_division()
    {
        var division_id = document.getElementById("sel_div").value;
        var url = "{{ url('/selectedAreaList') }}";
        $.ajax({
            type: "POST",
            url: url,
            data: {'_token': '<?php echo csrf_token(); ?>', 'division_id': division_id },
            success: function (data) {
                //console.log(data);
                $("#area_section").empty();
                $("#area_section").append(data);
            }
        });
    }

    function area_update(area_id)
    {
    var area_name = document.getElementById("area_updated_"+area_id).value;
    var url = "{{ url('/updateAreaName') }}";
    $.ajax({
        type: "POST",
        url: url,
        data: {'_token': '<?php echo csrf_token(); ?>', 'area_name': area_name, 'area_id': area_id},
        success: function (data) {
            console.log(data);
        }
    });
    }
</script>
@include('admin.production.footer')