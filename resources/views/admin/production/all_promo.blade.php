@include('admin.production.header')

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if (session('added'))
                <h3>All Promo Codes</h3>
                <div class="alert alert-success">
                    {{ session('added') }}
                </div>
            @elseif (session('deleted'))
                <h3>All Promo Codes</h3>
                <div class="alert alert-danger">
                    {{ session('deleted') }}
                </div>
            @elseif (session('promo_added'))
                <h3>All Promo Codes</h3>
                <div class="alert alert-success">
                    {{ session('promo_added') }}
                </div>
            @elseif(session('try_again'))
                <div class="alert alert-warning">
                    {{ session('try_again') }}
                </div>
            @else
                <h3>All Promo Codes</h3>
            @endif
        </div>
        <div class="title_left">
            <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                <form action="{{ url('searchPromo') }}" method="post">
                    <div class="input-group">
                        <select name="partnerName" class="form-control">
                            <option>Select partner</option>
                            @foreach($promoPartners as $name)
                                <option value="{{$name}}">{{$name}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <span class="input-group-btn">
                              <button type="submit" name="submit" class="btn btn-default">Go!</button>
                            </span>
                    </div>
                </form>
                {{--<form action="{{ url('searchPromo') }}" method="post">--}}
                {{--<div class="input-group">--}}
                {{--<select name="partnerEmail" class="form-control">--}}
                {{--<option>Select E-mail</option>--}}
                {{--@foreach($promoPartners as $name)--}}
                {{--<option value="{{$name['partner_email']}}">{{$name['partner_email']}}</option>--}}
                {{--@endforeach--}}
                {{--</select>--}}
                {{--<input type="hidden" name="_token" value="{{ csrf_token() }}">--}}
                {{--<span class="input-group-btn">--}}
                {{--<button type="submit" name="submit" class="btn btn-default" >Go!</button>--}}
                {{--</span>--}}
                {{--</div>--}}
                {{--</form>--}}
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    @if($allPromo)
                        <table class="table table-striped projects">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($allPromo as $promo)
                                <tr>
                                    <td>{{ $promo['id'] }}</td>
                                    <td><img src="{{ $promo['image_link'] }}" alt="hotspot profile image" width="360"
                                             height="250"></td>
                                    <td>{{ $promo['partner_name'] }}</td>
                                    <td><a href="{{ url('edit_promo/'.$promo['id']) }}">
                                            <button class="btn btn-primary">Edit</button>
                                        </a></td>
                                    <td><a href="{{ url('delete_promo/'.$promo['id']) }}">
                                            <button class="btn btn-danger"
                                                    onclick="return confirm('Are you sure you want to delete this hotspot')">
                                                Delete
                                            </button>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
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