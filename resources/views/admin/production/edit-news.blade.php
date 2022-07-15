@include('admin.production.header')

<div class="right_col" role="main">
        <div class="page-title">
            <div class="title_left">
                <h3>Edit News</h3>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="panel-body">
            @if (isset($news))
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" style="padding-top: 30px;text-align: right;">Previous Image</label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        <img src="{{asset($news['press_image'])}}" width="20%" class="img-circle" style="margin-bottom: 10px">
                    </div>
                </div>
                <br>
                <br>
                <form class="form-horizontal form-label-left" method="post" action="{{ url('edit-news/'.$news['id']) }}" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Press Name</label>
                        <span style="color: red;">
                            @if ($errors->getBag('default')->first('press_name'))
                                {{ $errors->getBag('default')->first('press_name') }}
                            @endif
                        </span>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <input type="text" class="form-control" value="{{$news['press_name']}}" name="press_name" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Sub Title</label>
                        <span style="color: red;">
                            @if ($errors->getBag('default')->first('sub_title'))
                                {{ $errors->getBag('default')->first('sub_title') }}
                            @endif
                        </span>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <input type="text" class="form-control" value="{{$news['sub_title']}}" name="sub_title" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Press Details</label>
                        <span style="color: red;">
                            @if ($errors->getBag('default')->first('press_details'))
                                {{ $errors->getBag('default')->first('press_details') }}
                            @endif
                        </span>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <input type="text" class="form-control"  value="{{$news['press_details']}}" name="press_details" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Press Link</label>
                        <span style="color: red;">
                            @if ($errors->getBag('default')->first('press_link'))
                                {{ $errors->getBag('default')->first('press_link') }}
                            @endif
                        </span>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <input type="text" class="form-control" value="{{$news['press_link']}}" name="press_link" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Date</label>
                        <span style="color: red;">
                            @if ($errors->getBag('default')->first('date'))
                                {{ $errors->getBag('default')->first('date') }}
                            @endif
                        </span>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <input type="date" class="form-control" value="{{$news['date']}}" name="date" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Select News Image</label>
                        <span style="color: red;">
                            @if ($errors->getBag('default')->first('press_image'))
                                {{ $errors->getBag('default')->first('press_image') }}
                            @endif
                        </span>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <input id="file-0c" class="file " name="press_image" type="file">
                        </div>
                    </div>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="ln_solid"></div>
                    <div class="form-group">
                        <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                            {{--<button type="button" class="btn btn-primary">Cancel</button>--}}
                            {{--<button type="reset" class="btn btn-secondary">Reset</button>--}}
                            <button type="submit" class="btn btn-activate pull-right">Submit</button>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>

@include('admin.production.footer')