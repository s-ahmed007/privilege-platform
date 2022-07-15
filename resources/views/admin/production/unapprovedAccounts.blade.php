@include('admin.production.header')

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Members</h3>
        </div>

        <div class="title_right">
            <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                <form action="{{ url('customerById') }}" method="post">
                    <div class="input-group">
                        <input type="text" class="form-control" name="searchID" placeholder="Search for customer">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <span class="input-group-btn">
                      <button class="btn btn-default" type="submit">Go!</button>
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
                @if($info)
                    <!-- start project list -->
                        <table class="table table-striped projects">
                            <thead>
                            <tr>
                                <th style="width: 10%">Id</th>
                                <th style="width: 20%">Customer Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($info as $customerInfo)
                                <tr>
                                    <td>{{ $customerInfo['customer_id'] }}</td>
                                    <td>{{ $customerInfo['customer_first_name'].' '.$customerInfo['customer_last_name'] }}</td>
                                    <td>{{ $customerInfo['customer_email'] }}</td>
                                    <td>{{ $customerInfo['customer_contact_number'] }}</td>
                                    <td><i class="fa fa-close" style="font-size: 2em; color: red;"></i></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <!-- end project list -->
                    @else
                        <div style="font-size: 1.4em; color: red;">
                            {{ 'Id not found.' }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.production.footer')