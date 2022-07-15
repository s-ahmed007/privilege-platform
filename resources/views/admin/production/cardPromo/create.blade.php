@include('admin.production.header')
<script src="https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=37yoj87gdrindjk3ksaos96cpb8uwpwlf8nyk2rmrqa37n3v"></script>

<script>tinymce.init({selector: '#summernote', plugins: "lists, advlist"});</script>

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @elseif(session('multiple-promo'))
                <div class="alert alert-warning">
                    {{ session('multiple-promo') }}
                </div>
            @elseif(session('no-promo'))
                <div class="alert alert-warning">
                    {{ session('no-promo') }}
                </div>
            @elseif(session('try_again'))
                <div class="alert alert-danger">
                    {{ session('try_again') }}
                </div>
            @endif
            <h3>Create A New Promo Code</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <form class="form-horizontal form-label-left" method="post" action="{{ url('/card-promo') }}"
                          enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group">
                            <span style="color: red;">
                                @if ($errors->getBag('default')->first('mem_type'))
                                    {{ $errors->getBag('default')->first('mem_type') }}
                                @endif
                            </span>
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Membership Type:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <select name="mem_type" class="form-control" required>
                                    <option value="{{\App\Http\Controllers\Enum\PromoType::ALL}}">All</option>
                                    <option value="{{\App\Http\Controllers\Enum\PromoType::CARD_PURCHASE}}">New Purchase</option>
                                    <option value="{{\App\Http\Controllers\Enum\PromoType::RENEW}}">Renew</option>
                                    <option value="{{\App\Http\Controllers\Enum\PromoType::UPGRADE}}">Upgrade</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Month:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="number" class="form-control" max="12" placeholder="Month (optional)"
                                       name="month"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Promo Code:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <span style="color: red;">
                                    @if ($errors->getBag('default')->first('code'))
                                        {{ $errors->getBag('default')->first('code') }}
                                    @endif
                                </span>
                                <input type="text" class="form-control" placeholder="Promo Code" name="code"
                                       required="required"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Flat Discount Amount:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="text" class="form-control" placeholder="Flat Discount Amount: (i.e. 200)"
                                       name="flat_rate"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Percentage:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="text" class="form-control" placeholder="Percentage (i.e. 30)"
                                       name="percentage"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <span style="color: red;">
                            @if ($errors->getBag('default')->first('expiry_date'))
                                    {{ $errors->getBag('default')->first('expiry_date') }}
                                @endif
                         </span>
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Expiry Date:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="date" name="expiry_date" class="form-control" required="required">
                            </div>
                        </div>
                        <div class="form-group">
                            <span style="color: red;">
                            @if ($errors->getBag('default')->first('usage'))
                                    {{ $errors->getBag('default')->first('usage') }}
                                @endif
                         </span>
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Usage Limit:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="text" class="form-control" placeholder="unlimited or numeric value"
                                       name="usage"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <span style="color: red;">
                            @if ($errors->getBag('default')->first('text'))
                                    The details field is required.
                                @endif
                         </span>
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Promo Detail:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <textarea id="summernote" name="text"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <span style="color: red;">
                                @if ($errors->getBag('default')->first('seller'))
                                    {{ $errors->getBag('default')->first('seller') }}
                                @endif
                            </span>
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Seller:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <select name="seller" class="form-control">
                                    <option selected value="">Select Seller (Optional)</option>
                                    @foreach($sellers as $seller)
                                        <option value="{{$seller->id}}">{{$seller->first_name.' '.$seller->last_name
                                        .' => '.$seller->account->phone}}

                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Influencer ID:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="text" class="form-control" maxlength="16" minlength="16" placeholder="16 digit ID (optional)"
                                       name="influencer_id"/>
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
    $('#file-fr').fileinput({
        language: 'fr',
        uploadUrl: '#',
        allowedFileExtensions: ['jpg', 'png', 'gif']
    });
    $('#file-es').fileinput({
        language: 'es',
        uploadUrl: '#',
        allowedFileExtensions: ['jpg', 'png', 'gif']
    });
    $("#file-0").fileinput({
        'allowedFileExtensions': ['jpg', 'png', 'gif']
    });
    $("#file-1").fileinput({
        uploadUrl: '#', // you must set a valid URL here else you will get an error
        allowedFileExtensions: ['jpg', 'png', 'gif'],
        overwriteInitial: false,
        maxFileSize: 1000,
        maxFilesNum: 10,
        //allowedFileTypes: ['image', 'video', 'flash'],
        slugCallback: function (filename) {
            return filename.replace('(', '_').replace(']', '_');
        }
    });


    $(document).ready(function () {
        $("#test-upload").fileinput({
            'showPreview': false,
            'allowedFileExtensions': ['jpg', 'png', 'gif'],
            'elErrorContainer': '#errorBlock'
        });
        $("#kv-explorer").fileinput({
            'theme': 'explorer',
            'uploadUrl': '#',
            overwriteInitial: false,
            initialPreviewAsData: true,
            initialPreview: [
                "http://lorempixel.com/1920/1080/nature/1",
                "http://lorempixel.com/1920/1080/nature/2",
                "http://lorempixel.com/1920/1080/nature/3"
            ],
            initialPreviewConfig: [
                {caption: "nature-1.jpg", size: 329892, width: "120px", url: "{$url}", key: 1},
                {caption: "nature-2.jpg", size: 872378, width: "120px", url: "{$url}", key: 2},
                {caption: "nature-3.jpg", size: 632762, width: "120px", url: "{$url}", key: 3}
            ]
        });
    });
</script>