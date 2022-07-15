@include('admin.production.header')

<div class="right_col" role="main">
    <div class="page-title">
        <h3>Manual Registration</h3>
        <div class="title_left">
            @if(session('error'))
                <div class="title_right alert alert-warning" style="text-align: center;">
                    {{ session('error') }}
                </div>
            @endif
        </div>
    </div>
    <div class="col-md-12 col-xs-12">
        <div class="x_panel">
            <div class="title_left">
                <div class="clearfix"></div>
                <div class="panel-body">
                    <form class="form-horizontal form-label-left" method="post" action="{{ url('admin/manual_registration') }}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Name:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                  <span style="color: red;">
                                    @if ($errors->getBag('default')->first('postHeader'))
                                          {{ $errors->getBag('default')->first('postHeader') }}
                                      @endif
                                  </span>
                                <input type="text" class="form-control" placeholder="Full name" name="name" required
                                value="{{old('name')}}"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Phone:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                  <span style="color: red;">
                                    @if ($errors->getBag('default')->first('phone'))
                                      {{ $errors->getBag('default')->first('phone') }}
                                    @endif
                                  </span>
                                <input type="text" class="form-control" placeholder="+88017xxxxxxxx" name="phone"
                                   required="required" minlength="14" maxlength="14" value="{{old('phone')}}"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Email:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <span style="color: red;">
                                    @if ($errors->getBag('default')->first('email'))
                                        {{ $errors->getBag('default')->first('email') }}
                                    @endif
                                </span>
                                <input type="email" class="form-control" placeholder="test@example.com" name="email"
                                   value="{{old('email')}}" required/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Pin:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <span style="color: red;">
                                    @if ($errors->getBag('default')->first('pin'))
                                        {{ $errors->getBag('default')->first('pin') }}
                                    @endif
                                  </span>
                                <input type="text" class="form-control" placeholder="1234" name="pin" required
                                   value="{{old('pin')}}" pattern="[0-9]{4}" title="4 digits number only"/>
                            </div>
                        </div>
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

@include('admin.production.footer')
<script>

</script>