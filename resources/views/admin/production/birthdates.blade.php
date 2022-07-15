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
            @elseif(session('birthday-wish'))
                <div class="alert alert-success">
                    {{ session('birthday-wish') }}
                </div>

            @endif
            <h3>All Birthdates</h3>
            @if(count($customer_info) != 0 && $wish_send_status == 0)
                <button type="button" id="birthday_wish" class="btn btn-primary" onclick="birthday_wish();">
                    Send Wish
                </button>
            @else
                <button type="button" class="btn btn-primary" disabled>
                    Send Wish
                </button>
            @endif
        </div>
        <div class="title_right">
            <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">

            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    @if(isset($customer_info) && count($customer_info) > 0)
                        <table class="table table-striped projects">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer Name</th>
                                <th>Birthdate</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i = 1; ?>
                            @foreach($customer_info as $customer_info)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $customer_info->customer_first_name.' '.$customer_info->customer_last_name }}</td>
                                    <td>{{$customer_info->customer_dob}}</td>
                                </tr>
                                <?php $i++; ?>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div style="font-size: 1.4em; color: red;">
                            {{ '0 birthdays' }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.production.footer')