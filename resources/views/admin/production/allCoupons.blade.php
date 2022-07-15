@include('admin.production.header')

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @elseif (session('delete partner'))
                <div class="alert alert-danger">
                    {{ session('delete partner') }}
                </div>
            @elseif(session('try_again'))
                <div class="alert alert-warning">
                    {{ session('try_again') }}
                </div>
            @elseif(session('coupon_update'))
                <div class="alert alert-success">
                    {{ session('coupon_update') }}
                </div>
            @else

            @endif
            <h3>All Coupons</h3>
        </div>
        <div class="title_right">
            <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                <form action="{{ url('partnerByName') }}" method="post">
                    <div class="input-group">
                        <input type="text" class="form-control" name="searchPartner" placeholder="Search for partner">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <span class="input-group-btn">
                      <button type="submit" name="submit" class="btn btn-default">Go!</button>
                    </span>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    @if($coupons)
                        <table class="table table-striped projects">
                            <thead>
                            <tr>
                                <th>Partner Id</th>
                                <th>Partner Name</th>
                                <th>Coupon Type</th>
                                <th>Reward Text</th>
                                <th>No. of Coupons</th>
                                <th>Expiry</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($coupons as $coupon)
                                <tr>
                                    <td>{{ $coupon['partner_account_id'] }}</td>
                                    <td>{{ $coupon['partner_name'] }}</td>
                                    <td>{{ $coupon['coupon_type'] }}</td>
                                    <td>{{ $coupon['reward_text'] }}</td>
                                    <td>{{ $coupon['stock'] }}</td>
                                    <td>{{ $coupon['expiry_date'] }}</td>
                                    <td><a href="{{ url('edit_coupon/'.$coupon['id']) }}">
                                            <button class="btn btn-primary">Edit</button>
                                        </a></td>
                                    <td><a href="{{ url('delete_coupon/'.$coupon['id']) }}">
                                            <button class="btn btn-delete" disabled="disabled">Delete</button>
                                        </a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div style="font-size: 1.4em; color: red;">
                            {{ 'Partner not found.' }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

@include('admin.production.footer')