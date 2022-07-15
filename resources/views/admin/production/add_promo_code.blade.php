@include('admin.production.header')
<script src="https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=37yoj87gdrindjk3ksaos96cpb8uwpwlf8nyk2rmrqa37n3v"></script>

<script>tinymce.init({selector: '#textarea', plugins: "lists, advlist, image, link, media"});</script>
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Add promo code</h3>
        </div>
        @if(session('promo added'))
            <div class="title_right alert alert-success" style="text-align: center;">{{ session('promo added') }}</div>
        @endif
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <form class="form-horizontal form-label-left" method="post" action="{{ url('add_promo_code') }}"
                          enctype="multipart/form-data">

                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Partner Name</label>
                            <span style="color: red;">
                                         @if ($errors->getBag('default')->first('name'))
                                    {{ $errors->getBag('default')->first('name') }}
                                @endif
                                        </span>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="text" class="form-control" placeholder="Name" name="name">
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
                                <input type="text" class="form-control" placeholder="Category" name="category">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Discount Percentage %</label>
                            <span style="color: red;">
                                         @if ($errors->getBag('default')->first('discount'))
                                    {{ $errors->getBag('default')->first('discount') }}
                                @endif
                                        </span>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="text" class="form-control" placeholder="Discount Percentage"
                                       name="discount">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Website</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="text" class="form-control" placeholder="Website" name="website">
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
                                <input type="text" class="form-control" placeholder="Promo Code" name="promo_code">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Terms & Condition</label>
                            <span style="color: red;">
                                         @if ($errors->getBag('default')->first('terms'))
                                    {{ $errors->getBag('default')->first('terms') }}
                                @endif
                                        </span>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                {{--<input type="text" class="form-control" placeholder="Terms & Condition" name="terms">--}}
                                <textarea id="textarea" name="terms">{{old('terms')}}"</textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Select Profile Image</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input id="file-0c" class="file " name="profile" type="file">
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
                </div>
            </div>

        </div>
    </div>
</div>
</div>
</div>
@include('admin.production.footer')