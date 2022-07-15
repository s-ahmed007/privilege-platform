@include('admin.production.header')

<div class="right_col" role="main">
        <div class="page-title">
            <div class="title_left">
                <h3>Partners in a Hotspot</h3>
                @if(session('partner deleted'))
                    <div style="color: red; font-weight: bold">
                        {{ session('partner deleted') }}
                    </div>
                @elseif(session('try_again'))
                    <div class="alert alert-warning">
                        {{ session('try_again') }}
                    </div>
                @endif
            </div>
        </div>
        <div class="clearfix"></div>
    <br><br>
    <div class="row">
        <div class="form-group">
            <label class="control-label col-xs-12">SELECT A HOTSPOT : </label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <select name="hotspot" class="form-control">
                    <option disabled selected>Please select</option>
                    @foreach($allHotspots as $hotspot)
                        <option value="{{$hotspot->id}}">{{$hotspot->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <br><br>
    <div class="row">
        <ul id="partners_container">
        </ul>
    </div>
</div>

@include('admin.production.footer')

<script>
    $('select').on('change', function () {
        var hotspot_id = this.value;
        var url = "{{ url('/hotspotPartnerList')}}";
        //alert(hotspot_id); exit(0);
        $.ajax({
            type: "POST",
            url: url,
            data: {'_token': '<?php echo csrf_token(); ?>', 'hotspot_id': hotspot_id},
            success: function (data) {
                //alert(data); exit(0);
                console.log(data);
                $("#partners_container").hide().html(data).fadeIn('slow');
            }
        });
    })
</script>