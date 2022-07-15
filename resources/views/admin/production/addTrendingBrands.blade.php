@include('admin.production.header')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<style>
    #sortable-list-trending		{ padding:0; }
    #sortable-list-trending li	{ padding:4px 8px; color:#000; cursor:move; list-style:none; width:500px; background:#ddd; margin:10px 0; border:1px solid #999; }

    #sortable-list		{ padding:0; }
    #sortable-list li	{ padding:4px 8px; color:#000; cursor:move; list-style:none; width:500px; background:#ddd; margin:10px 0; border:1px solid #999; }

    .fa-times{font-size: 2rem; float: right;color: red;}
</style>
<div class="right_col" role="main">
    @if (Session::has('operation failed'))
        <div class="title_right alert alert-danger"
             style="text-align: center; margin-top: 60px;">{{ Session::get('operation failed') }}</div>
    @elseif (Session::has('trending partners added'))
        <div class="title_right alert alert-success"
             style="text-align: center; margin-top: 60px;">{{ Session::get('trending partners added') }}</div>
    @elseif (Session::has('brand partners added'))
        <div class="title_right alert alert-success"
             style="text-align: center; margin-top: 60px;">{{ Session::get('brand partners added') }}</div>
    @elseif (Session::has('deleted'))
        <div class="title_right alert alert-danger"
             style="text-align: center; margin-top: 60px;">{{ Session::get('deleted') }}</div>
    @endif
    <div class="page-title">
        <div class="title_left">
            <h3>Add New Partners/ Occasional Partners</h3>
            <div>
                <span class="top_msg"></span>
            </div>
        </div>
    </div>
    <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_content">
                        <div class="row" style="text-align: center">
                            <div class="col-md-6">
                                <input type="hidden" value="true" name="autoSubmit" id="autoSubmit"/>
                                <ul id="sortable-list">

                                    @foreach($selectedTopBrands as $partner)
                                        <li title="{{$partner->id}}">{{$partner->info->partner_name}}
                                            <a href="{{url('admin/removeTopBrand/'.$partner->id)}}"
                                               onclick="return confirm('Are you sure?')"><i class="fas fa-times"></i></a>
                                        </li>
                                        <?php $order[] = $partner->id; ?>
                                    @endforeach
                                </ul>
                                <input type="hidden" name="sort_order" id="sort_order" value="<?php echo implode(',',$order); ?>" />
                            </div>
                            <div class="col-md-6">
                                <form action="{{url('admin/addTopBrand')}}" method="post">
                                    {{csrf_field()}}
                                    <label style="float:left;">Select Partner </label>
                                    <select name="partner1" id="partner2" class="form-control">
                                        @foreach($allPartners as $partner)
                                            <option value="{{$partner->partner_account_id}}">{{$partner->info->partner_name}}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-activate pull-right">Add</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <div class="page-title">
        <div class="title_left">
            <h3>Add Trending Offers</h3>
            <div>
                <span class="trend_msg"></span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <div class="row" style="text-align: center">
                        <div class="col-md-6">
                            <input type="hidden" value="true" name="autoSubmit" id="autoSubmitTrending"/>
                            <ul id="sortable-list-trending">

                                @foreach($selectedTrendingOff as $partner)
                                    <li title="{{$partner->id}}">{{$partner->info->partner_name}}
                                    <a href="{{url('admin/removeTrendPartner/'.$partner->id)}}"
                                    onclick="return confirm('Are you sure?')"><i class="fas fa-times"></i></a>
                                    </li>
                                    <?php $order[] = $partner->id; ?>
                                @endforeach
                            </ul>
                            <input type="hidden" name="sort_order" id="sort_order_trending" value="<?php echo implode(',',$order); ?>" />
                        </div>
                        <div class="col-md-6">
                            <form action="{{url('admin/addTrendingOffer')}}" method="post">
                                {{csrf_field()}}
                                <label style="float:left;">Select Partner </label>
                                <select name="partner2" class="form-control">
                                    @foreach($allPartners as $partner)
                                        <option value="{{$partner->partner_account_id}}">{{$partner->info->partner_name}}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-activate pull-right">Add</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
@include('admin.production.footer')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    /* when the DOM is ready */
    //trending offer section
    $(document).ready(function() {
        /* grab important elements */
        var trendingSortInput = $('#sort_order_trending');
        var trendingList = $('#sortable-list-trending');
        /* create requesting function to avoid duplicate code */
        function request1() {
            var update_url = "{{ url('/admin/trendingOffer/update_order') }}";
            $.ajax({
                type: "POST",
                url: update_url,
                data: {'_token': '<?php echo csrf_token(); ?>', 'sort_order': trendingSortInput[0].value},
                success: function (data) {

                }
            });
        }
        /* worker function */
        var fnSubmitTrending = function(save) {
            var sortOrder = [];
            trendingList.children('li').each(function(){
                sortOrder.push($(this).data('id'));
            });
            trendingSortInput.val(sortOrder.join(','));
            if(save) {
                request1();
            }
        };
        /* store values */
        trendingList.children('li').each(function() {
            var li = $(this);
            li.data('id',li.attr('title')).attr('title','');
        });
        /* sortables */
        trendingList.sortable({
            opacity: 0.7,
            update: function() {
                fnSubmitTrending(true);
            }
        });
        trendingList.disableSelection();
        /* ajax form submission */
        $('#dd-form').bind('submit',function(e) {
            if(e) e.preventDefault();
            fnSubmitTrending(true);
        });
    });

    //top brand section
    $(document).ready(function() {
        /* grab important elements */
        var sortInput = $('#sort_order');
        var list = $('#sortable-list');
        /* create requesting function to avoid duplicate code */
        function request() {
            var update_url = "{{ url('/admin/topBrands/update_order') }}";
            $.ajax({
                type: "POST",
                url: update_url,
                data: {'_token': '<?php echo csrf_token(); ?>', 'sort_order': sortInput[0].value},
                success: function (data) {

                }
            });
        }
        /* worker function */
        var fnSubmit = function(save) {
            var sortOrder = [];
            list.children('li').each(function(){
                sortOrder.push($(this).data('id'));
            });
            sortInput.val(sortOrder.join(','));
            if(save) {
                request();
            }
        };
        /* store values */
        list.children('li').each(function() {
            var li = $(this);
            li.data('id',li.attr('title')).attr('title','');
        });
        /* sortables */
        list.sortable({
            opacity: 0.7,
            update: function() {
                fnSubmit(true);
            }
        });
        list.disableSelection();
        /* ajax form submission */
        $('#dd-form').bind('submit',function(e) {
            if(e) e.preventDefault();
            fnSubmit(true);
        });
    });









    function addTrendPartner() {
        var partner_id = $( "#partner1" ).val();
        var url = "{{ url('/addTrendingOffer') }}";
        $.ajax({
            type: "POST",
            url: url,
            data: {'_token': '<?php echo csrf_token(); ?>', 'partner_id' : partner_id},
            success: function (data) {
                if(data.result === true){
                    $(".trend_msg").parent().removeClass().addClass('alert alert-success');
                    $(".trend_msg").html('Successfully added');
                    //append partners
                    var i = 0;
                    var partners = data.trendPartners;
                    $(".trendPartnerList").empty();
                    for (i=0; i<partners.length; i++) {
                        $(".trendPartnerList").append("<p>"+partners[i]['info'][0]['partner_name']+
                            "<i class=\"fas fa-times\" onclick=\"removeTrendPartner("+partners[i]['partner_account_id']+")\"></i></p>");
                    }
                }else{
                    $(".trend_msg").parent().removeClass().addClass('alert alert-warning');
                    $(".trend_msg").html('Could not add');
                }
            }
        });
    }

    function removeTrendPartner(partner_id) {
        var url = "{{ url('/removeTrendPartner') }}";
        $.ajax({
            type: "POST",
            url: url,
            data: {'_token': '<?php echo csrf_token(); ?>', 'partner_id' : partner_id},
            success: function (data) {
                console.log(data);
                $(".trend_msg").parent().removeClass();
                $(".trend_msg").empty();
                //append partners
                var i = 0;
                var partners = data.trendPartners;
                $(".trendPartnerList").empty();
                for (i=0; i<partners.length; i++) {
                    $(".trendPartnerList").append("<p>"+partners[i]['info'][0]['partner_name']+
                        "<i class=\"fas fa-times\" onclick=\"removeTrendPartner("+partners[i]['partner_account_id']+")\"></i></p>");
                }
            }
        });
    }

    function addTopPartner() {
        var partner_id = $( "#partner2" ).val();
        var url = "{{ url('/addTopBrand') }}";
        $.ajax({
            type: "POST",
            url: url,
            data: {'_token': '<?php echo csrf_token(); ?>', 'partner_id' : partner_id},
            success: function (data) {
                if(data.result === true){
                    $(".top_msg").parent().removeClass().addClass('alert alert-success');
                    $(".top_msg").html('Successfully added');
                    //append partners
                    var i = 0;
                    var partners = data.topPartners;
                    $(".topPartnerList").empty();
                    for (i=0; i<partners.length; i++) {
                        $(".topPartnerList").append("<p>"+partners[i]['info'][0]['partner_name']+
                            "<i class=\"fas fa-times\" onclick=\"removeTopPartner("+partners[i]['partner_account_id']+")\"></i></p>");
                    }
                }else{
                    $(".top_msg").parent().removeClass().addClass('alert alert-warning');
                    $(".top_msg").html('Could not add');
                }
            }
        });
    }

    function removeTopPartner(partner_id) {
        var url = "{{ url('/remove_top_partner') }}";
        $.ajax({
            type: "POST",
            url: url,
            data: {'_token': '<?php echo csrf_token(); ?>', 'partner_id' : partner_id},
            success: function (data) {
                $(".top_msg").parent().removeClass();
                $(".top_msg").empty();
                //append partners
                var i = 0;
                var partners = data.topPartners;
                $(".topPartnerList").empty();
                for (i=0; i<partners.length; i++) {
                    $(".topPartnerList").append("<p>"+partners[i]['info'][0]['partner_name']+
                        "<i class=\"fas fa-times\" onclick=\"removeTopPartner("+partners[i]['partner_account_id']+")\"></i></p>");
                }
            }
        });
    }
</script>