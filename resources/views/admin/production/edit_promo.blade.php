@include('admin.production.header')

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Edit Promo</h3>
            @if (session('promo updated'))
                <div class="alert alert-success">
                    {{ session('promo updated') }}
                </div>
            @elseif(session('try_again'))
                <div class="alert alert-warning">
                    {{ session('try_again') }}
                </div>
            @endif
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="panel-body">
        @if (isset($promo))
            <form class="form-horizontal form-label-left" method="post"
                  action="{{ url('edit_promo_code/'.$promo['id']) }}">

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Partner Name</label>
                    <span style="color: red;">
                                         @if ($errors->getBag('default')->first('name'))
                            {{ $errors->getBag('default')->first('name') }}
                        @endif
                                        </span>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        <input type="text" class="form-control" placeholder="Name" name="name"
                               value="{{$promo['partner_name']}}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Category</label>
                    <span style="color: red;">
                                         @if ($errors->getBag('default')->first('category'))
                            {{ $errors->getBag('default')->first('category') }}
                        @endif
                                        </span>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        <input type="text" class="form-control" placeholder="Category" name="category"
                               value="{{$promo['category']}}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Discount Percentage</label>
                    <span style="color: red;">
                                         @if ($errors->getBag('default')->first('discount'))
                            {{ $errors->getBag('default')->first('discount') }}
                        @endif
                                        </span>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        <input type="text" class="form-control" placeholder="Discount Percentage" name="discount"
                               value="{{$promo['discount_percentage']}}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Website</label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        <input type="text" class="form-control" placeholder="Website" name="website"
                               value="{{$promo['partner_website']}}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Promo Code</label>
                    <span style="color: red;">
                                         @if ($errors->getBag('default')->first('promo_code'))
                            {{ $errors->getBag('default')->first('promo_code') }}
                        @endif
                                        </span>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        <input type="text" class="form-control" placeholder="Promo Code" name="promo_code"
                               value="{{$promo['promo_code']}}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Terms & Condition</label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        <input type="text" class="form-control" placeholder="Terms & Condition" name="terms"
                               value="{{$promo['term&condition']}}">
                    </div>
                </div>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="ln_solid"></div>
                <div class="form-group">
                    <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                        <button type="submit" class="btn btn-activate pull-right">Submit</button>
                    </div>
                </div>
            </form>
        @endif
    </div>
</div>

@include('admin.production.footer')