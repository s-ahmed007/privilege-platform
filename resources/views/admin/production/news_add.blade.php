@include('admin.production.header')
<div class="right_col" role="main">
    <div class="col-md-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Add News Information</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <form class="form-horizontal form-label-left" method="post" action="{{ url('addNews') }}"
                      enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">News Name</label>
                        <span style="color: red;">
                            @if ($errors->getBag('default')->first('press_name'))
                                {{ $errors->getBag('default')->first('press_name') }}
                            @endif
                        </span>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <input type="text" class="form-control" placeholder="Name" name="press_name" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">News Sub Title</label>
                        <span style="color: red;">
                            @if ($errors->getBag('default')->first('sub_title'))
                                {{ $errors->getBag('default')->first('sub_title') }}
                            @endif
                        </span>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <input type="text" class="form-control" placeholder="Sub Title" name="sub_title"
                                   required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Press Details(45 words)</label>
                        <span style="color: red;">
                            @if ($errors->getBag('default')->first('press_details'))
                                {{ $errors->getBag('default')->first('press_details') }}
                            @endif
                        </span>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <input type="text" class="form-control"
                                   placeholder="Enter the textarea input here.. (limited to 210 characters)"
                                   name="press_details" id="my-input" maxlength="210" required>
                            <span id='remainingC'></span>
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
                            <input type="text" class="form-control" placeholder="News link" name="press_link"
                                   required>
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
                            <input type="date" class="form-control" name="date" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">
                            <p>News Image(3w:1h)</p>
                            <p>Image size should not cross 2MB</p>
                        </label>
                        <span style="color: red;">
                            @if ($errors->getBag('default')->first('press_image'))
                                {{ $errors->getBag('default')->first('press_image') }}
                            @endif
                        </span>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <input id="file-0c" class="file" name="press_image" type="file" required>
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
@include('admin.production.footer')

<script>
    $(document).ready(function () {
        var len = 0;
        var maxchar = 210;

        $('#my-input').keyup(function () {
            len = this.value.length
            if (len > maxchar) {
                return false;
            }
            else if (len > 0) {
                $("#remainingC").html("Remaining characters: " + (maxchar - len));
            }
            else {
                $("#remainingC").html("Remaining characters: " + (maxchar));
            }
        })
    });
</script>