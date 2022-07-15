@include('admin.production.header')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<style>
    #sortable-list-all{ padding:0; }
    #sortable-list-all li{ padding:4px 8px; color:#000; cursor:move; list-style:none; width:500px; background:#ddd; margin:10px 0; border:1px solid #999; }

    .fa-times{font-size: 2rem; float: right;color: red;}
</style>
<div class="right_col" role="main">
    @if (Session::has('failed'))
        <div class="title_right alert alert-danger"
             style="text-align: center; margin-top: 60px;">{{ Session::get('failed') }}</div>
    @elseif (Session::has('success'))
        <div class="title_right alert alert-success"
             style="text-align: center; margin-top: 60px;">{{ Session::get('success') }}</div>
    @elseif (Session::has('brand partners added'))
        <div class="title_right alert alert-success"
             style="text-align: center; margin-top: 60px;">{{ Session::get('brand partners added') }}</div>
    @elseif (Session::has('deleted'))
        <div class="title_right alert alert-danger"
             style="text-align: center; margin-top: 60px;">{{ Session::get('deleted') }}</div>
    @endif


{{--    for all offers--}}
    <div class="page-title">
        <div class="title_left">
            <h3>Featured Partners (All)</h3>
            <div>
                <span class="top_msg"></span>
            </div>
        </div>
    </div>
    <div class="x_panel">
        <div class="x_content">
            <div class="row" style="text-align: center">
                <div class="col-md-6">
                    <input type="hidden" value="true" name="autoSubmit" id="autoSubmit"/>
                    <ul id="sortable-list-all">
                        <?php  $order_all = []; ?>
                        @if(count($featuredList) > 0)
                            <?php $featuredAll = $featuredList->where('category_id', null);?>
                            @foreach($featuredAll as $partner)
                                <li title="{{$partner->id}}">{{$partner->partner->partner_name}}
                                    <a href="{{url('admin/removeFeaturedPartner/'.$partner->id)}}"
                                       onclick="return confirm('Are you sure?')" style="float: right;">
                                        <i class="fas fa-times"></i></a>
                                </li>
                                <?php array_push($order_all, $partner->id) ?>
                            @endforeach
                        @endif
                    </ul>

                    <input type="hidden" name="sort_order_all" id="sort_order_all" value="<?php echo implode(',',$order_all); ?>" />
                </div>
                <div class="col-md-6">
                    <form action="{{url('admin/addFeaturedPartner/all')}}" method="post">
                        {{csrf_field()}}
                        <label style="float:left;">Select Partner </label>
                        <select name="partner" id="partner2" class="form-control">
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

{{--    for category wise offers    --}}
    @foreach($categories as $category)
        <div class="page-title">
            <div class="title_left">
                <h3>Featured Partners ({{$category->name}})</h3>
                <div>
                    <span class="top_msg"></span>
                </div>
            </div>
        </div>

        <div class="x_panel">
            <div class="x_content">
                <div class="row" style="text-align: center">
                    <div class="col-md-6">
                        <input type="hidden" value="true" name="autoSubmit" id="autoSubmit"/>
                        <ul id="sortable-list-{{$category->id}}" style="padding:0;">
                            <?php $order[$category->id] = []; ?>
                            @if(count($featuredList) > 0)
                                <?php $featuredAll = $featuredList->where('category_id', $category->id);?>
                                @foreach($featuredAll as $partner)
                                    <li title="{{$partner->id}}" style="padding:4px 8px; color:#000; cursor:move; list-style:none; width:500px; background:#ddd; margin:10px 0; border:1px solid #999;">
                                        {{$partner->partner->partner_name}}
                                        <a href="{{url('admin/removeFeaturedPartner/'.$partner->id)}}"
                                           onclick="return confirm('Are you sure?')" style="float: right;">
                                            <i class="fas fa-times"></i></a>
                                    </li>
                                    <?php array_push($order[$category->id], $partner->id); ?>
                                @endforeach
                            @endif
                        </ul>
                    <input type="hidden" name="sort_order_{{$category->id}}" id="sort_order_{{$category->id}}"
                           value="<?php echo implode(',',$order[$category->id]); ?>" />
                    </div>
                    <div class="col-md-6">
                        <?php $catPartners = $allPartners->where('info.partner_category', $category->id);?>
                        <form action="{{url('admin/addFeaturedPartner/'.$category->id)}}" method="post">
                            {{csrf_field()}}
                            <label style="float:left;">Select Partner </label>
                            <select name="partner" id="partner2" class="form-control">
                                @foreach($catPartners as $partner)
                                    <option value="{{$partner->partner_account_id}}">{{$partner->info->partner_name}}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-activate pull-right">Add</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

</div>

@include('admin.production.footer')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    /* when the DOM is ready */
    //all partners section
    $(document).ready(function() {
        /* grab important elements */
        var allSortInput = $('#sort_order_all');
        var allList = $('#sortable-list-all');
        /* create requesting function to avoid duplicate code */
        function request() {
            var update_url = "{{ url('/admin/category/update_order') }}";
            $.ajax({
                type: "POST",
                url: update_url,
                data: {'_token': '<?php echo csrf_token(); ?>', 'sort_order': allSortInput[0].value},
                success: function (data) {
                    //
                }
            });
        }
        /* worker function */
        var fnSubmitTrending = function(save) {
            var sortOrder = [];
            allList.children('li').each(function(){
                sortOrder.push($(this).data('id'));
            });
            allSortInput.val(sortOrder.join(','));
            if(save) {
                request();
            }
        };
        /* store values */
        allList.children('li').each(function() {
            var li = $(this);
            li.data('id',li.attr('title')).attr('title','');
        });
        /* sortables */
        allList.sortable({
            opacity: 0.7,
            update: function() {
                fnSubmitTrending(true);
            }
        });
        allList.disableSelection();
        /* ajax form submission */
        $('#dd-form').bind('submit',function(e) {
            if(e) e.preventDefault();
            fnSubmitTrending(true);
        });
    });
</script>

<script>
    /* when the DOM is ready */
    //all partners section
    $(document).ready(function() {
        @foreach($categories as $category)

            /* grab important elements */
            var SortInput{{$category->id}} = $('#sort_order_{{$category->id}}');
            var List{{$category->id}} = $('#sortable-list-{{$category->id}}');
            /* create requesting function to avoid duplicate code */
            function request{{$category->id}}() {
                var update_url = "{{ url('/admin/category/update_order') }}";
                $.ajax({
                    type: "POST",
                    url: update_url,
                    data: {'_token': '<?php echo csrf_token(); ?>', 'sort_order': SortInput{{$category->id}}[0].value},
                    success: function (data) {
                    }
                });
            }
            /* worker function */
            var fnSubmit{{$category->id}} = function(save) {
                var sortOrder = [];
                List{{$category->id}}.children('li').each(function(){
                    sortOrder.push($(this).data('id'));
                });
                SortInput{{$category->id}}.val(sortOrder.join(','));
                if(save) {
                    request{{$category->id}}();
                }
            };
            /* store values */
            List{{$category->id}}.children('li').each(function() {
                var li = $(this);
                li.data('id',li.attr('title')).attr('title','');
            });
            /* sortables */
            List{{$category->id}}.sortable({
                opacity: 0.7,
                update: function() {
                    fnSubmit{{$category->id}}(true);
                }
            });
            List{{$category->id}}.disableSelection();
            /* ajax form submission */
            $('#dd-form').bind('submit',function(e) {
                if(e) e.preventDefault();
                fnSubmit{{$category->id}}(true);
            });

        @endforeach
    });
</script>