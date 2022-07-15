@include('admin.production.header')
<?php
    $web_prices = $membership->where('platform', \App\Http\Controllers\Enum\PlatformType::web);
    $android_prices = $membership->where('platform', \App\Http\Controllers\Enum\PlatformType::android);
    $ios_prices = $membership->where('platform', \App\Http\Controllers\Enum\PlatformType::ios);
?>
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Premium Membership Prices</h3>
        </div>
    </div>
    <div class="col-md-12 col-xs-12">
        <div class="x_panel">
            @if (Session::has('mem_plan_add_success'))
                <div class="alert alert-success title_right" style="text-align: center">{{ session('mem_plan_add_success') }}</div>
            @elseif (Session::has('mem_plan_add_fail'))
                <div class="alert alert-warning title_right" style="text-align: center">{{ session('mem_plan_add_fail') }}</div>
            @endif
            <div class="x_content">
                <h3>Add membership price</h3>
                <form class="form-horizontal form-label-left" method="post" action="{{ url('admin/add_membership_price') }}">
                    {{csrf_field()}}
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label col-md-6 col-sm-6 col-xs-12">Membership Type:</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select class="form-control" name="membership_price_type">
                                        <option value="{{\App\Http\Controllers\Enum\MembershipPriceType::buy}}">Royalty Membership</option>
                                        <option value="{{\App\Http\Controllers\Enum\MembershipPriceType::renew}}">Royalty Membership Renew</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label col-md-6 col-sm-6 col-xs-12">Platform:</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select class="form-control" name="platform">
                                        <option value="{{\App\Http\Controllers\Enum\PlatformType::web}}">Web</option>
                                        <option value="{{\App\Http\Controllers\Enum\PlatformType::android}}">Android</option>
                                        <option value="{{\App\Http\Controllers\Enum\PlatformType::ios}}">IOS</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label col-md-6 col-sm-6 col-xs-12">Price:</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="number" class="form-control" name="card_price" placeholder="Enter Price"
                                           min="0" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label col-md-6 col-sm-6 col-xs-12">Duration:</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="number" class="form-control" name="card_duration" placeholder="Months"
                                           min="1" max="12" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label col-md-6 col-sm-6 col-xs-12">Title:</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" name="membership_title" placeholder="Title" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-12">
                                <button type="submit" class="btn btn-activate pull-right">Add</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-12 col-xs-12">
        <div class="x_panel">
            @if (Session::has('update_success'))
                <div class="alert alert-success title_right" style="text-align: center">{{ session('update_success') }}</div>
            @elseif (Session::has('update_fail'))
                <div class="alert alert-warning title_right" style="text-align: center">{{ session('update_fail') }}</div>
            @endif
            <div class="x_content">
                <h3>Change price</h3>
                <form class="form-horizontal form-label-left" method="post" action="{{ url('admin/update_membership_price') }}">
                    <div class="row">
                        <div style="background-color: beige;padding: 10px;">
                            <h2 style="text-align: center">Web</h2>
                            @foreach($web_prices as $value)
                                <div class="row">
                                    <div class="col-md-4 col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label col-md-6 col-sm-6 col-xs-12">Price:</label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                @if($value->type == \App\Http\Controllers\Enum\MembershipPriceType::buy)
                                                    <input type="number" class="form-control" value="{{$value->price}}" required min="0"
                                                       name="web_prices[]">
                                                @else
                                                    <input type="number" class="form-control" value="{{$value->price}}" required min="0"
                                                           name="renew_web_prices[]">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label col-md-6 col-sm-6 col-xs-12">Duration (Month):</label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                @if($value->type == \App\Http\Controllers\Enum\MembershipPriceType::buy)
                                                    <input type="number" class="form-control" value="{{$value->month}}" min="1" max="12"
                                                       name="web_validity[]" readonly>
                                                @else
                                                    <input type="number" class="form-control" value="{{$value->month}}" min="1" max="12"
                                                           name="renew_web_validity[]" readonly>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <div class="form-group">
                                            @if($value->type == \App\Http\Controllers\Enum\MembershipPriceType::buy)
                                                <label class="control-label col-md-6 col-sm-6 col-xs-12">{{$value->price==0?'Trial:':'Get Premium Membership:'}}</label>
                                            @else
                                                <label class="control-label col-md-6 col-sm-6 col-xs-12">Renew Membership:</label>
                                            @endif
                                            <p class="btn btn-delete deleteBtn" title="Delete"
                                                    data-price-id='{{ $value->id }}'>
                                                <i class="fa fa-trash-alt"></i>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div style="background-color: rgb(219, 68, 55);padding: 10px;">
                            <h2 style="text-align: center">Android</h2>
                            @foreach($android_prices as $value)
                                <div class="row">
                                    <div class="col-md-4 col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label col-md-6 col-sm-6 col-xs-12">Price:</label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                @if($value->type == \App\Http\Controllers\Enum\MembershipPriceType::buy)
                                                    <input type="number" class="form-control" value="{{$value->price}}" required min="0"
                                                           name="android_prices[]">
                                                @else
                                                    <input type="number" class="form-control" value="{{$value->price}}" required min="0"
                                                           name="renew_android_prices[]">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label col-md-6 col-sm-6 col-xs-12">Duration (Month):</label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                @if($value->type == \App\Http\Controllers\Enum\MembershipPriceType::buy)
                                                    <input type="number" class="form-control" value="{{$value->month}}" min="1" max="12"
                                                           name="android_validity[]" readonly>
                                                @else
                                                    <input type="number" class="form-control" value="{{$value->month}}" min="1" max="12"
                                                           name="renew_android_validity[]" readonly>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <div class="form-group">
                                            @if($value->type == \App\Http\Controllers\Enum\MembershipPriceType::buy)
                                                <label class="control-label col-md-6 col-sm-6 col-xs-12">{{$value->price==0?'Trial:':'Get Premium Membership:'}}</label>
                                            @else
                                                <label class="control-label col-md-6 col-sm-6 col-xs-12">Renew Membership:</label>
                                            @endif
                                            <p class="btn btn-delete deleteBtn" title="Delete"
                                                    data-price-id='{{ $value->id }}'>
                                                <i class="fa fa-trash-alt"></i>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div style="background-color: rgb(244, 180, 0);padding: 10px;">
                            <h2 style="text-align: center">IOS</h2>
                            @foreach($ios_prices as $value)
                                <div class="row">
                                    <div class="col-md-4 col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label col-md-6 col-sm-6 col-xs-12">Price::</label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                @if($value->type == \App\Http\Controllers\Enum\MembershipPriceType::buy)
                                                    <input type="number" class="form-control" value="{{$value->price}}" required min="0"
                                                           name="ios_prices[]">
                                                @else
                                                    <input type="number" class="form-control" value="{{$value->price}}" required min="0"
                                                           name="renew_ios_prices[]">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label col-md-6 col-sm-6 col-xs-12">Duration (Month):</label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                @if($value->type == \App\Http\Controllers\Enum\MembershipPriceType::buy)
                                                    <input type="number" class="form-control" value="{{$value->month}}" min="1" max="12"
                                                           name="ios_validity[]" readonly>
                                                @else
                                                    <input type="number" class="form-control" value="{{$value->month}}" min="1" max="12"
                                                           name="renew_ios_validity[]" readonly>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <div class="form-group">
                                            @if($value->type == \App\Http\Controllers\Enum\MembershipPriceType::buy)
                                                <label class="control-label col-md-6 col-sm-6 col-xs-12">{{$value->price==0?'Trial:':'Get Premium Membership:'}}</label>
                                            @else
                                                <label class="control-label col-md-6 col-sm-6 col-xs-12">Renew Membership:</label>
                                            @endif
                                            <p class="btn btn-delete deleteBtn" title="Delete"
                                                    data-price-id='{{ $value->id }}'>
                                                <i class="fa fa-trash-alt"></i>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="ln_solid"></div>
                    <div class="form-group">
                        <div class="col-xs-12">
                                <button type="submit" class="btn btn-activate pull-right">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('admin.production.footer')

<!-- javascript for deleting price item -->
<script>
    $('.deleteBtn').on('click', function (event) {
        if (confirm("Are you sure?")) {
            //fetch the item id
            var itemId = $(this).attr('data-price-id');
            var url = "{{ url('/admin/delete_membership_price') }}";
            url += '/' + itemId;

            $('<form action="' + url + '" method="POST">' +
                '<input type="hidden" name="_token" value="{{ csrf_token() }}"/>' +
                '<input type="hidden" name="_method" value="DELETE"/>' +
                '</form>').appendTo($(document.body)).submit();
        }
        return false;
    });
</script>