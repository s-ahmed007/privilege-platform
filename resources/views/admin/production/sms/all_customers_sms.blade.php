@include('admin.production.header')
<script src="https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=37yoj87gdrindjk3ksaos96cpb8uwpwlf8nyk2rmrqa37n3v"></script>

<script>tinymce.init({selector: '#textarea', plugins: "lists, advlist"});</script>

{{--SMS HUB-SMS TO ALL--}}
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if (session('operation complete'))
                <div class="alert alert-success">
                    {{ session('operation complete') }}
                </div>
            @endif
            <h3>Send SMS To Current Members</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <form class="form-horizontal form-label-left" method="post" action="{{ url('sendAllAdminSMS') }}"
                          enctype="multipart/form-data">
                        <div class="form-group">
                            <br>
                            <div class="row">
                                <label class="control-label col-md-2 col-sm-2 col-xs-12">Language</label>
                                <div class="col-md-8 col-sm-8 col-xs-12">
                                    <select class="browser-default custom-select" name="language"
                                            style="display: block;
                                            width: 100%;
                                            margin: 5px 0px 10px 0;
                                            padding: 5px 0px 5px 0;
                                            border: 1px solid #ccc;">
                                        <option selected value="english">English</option>
                                        <option value="bangla">Bengali</option>
                                    </select>
                                </div>
                                <br>
                            </div>
                            <div class="row">
                                <label class="control-label col-md-2 col-sm-2 col-xs-12">SMS Receiver Type</label>
                                <div class="col-md-8 col-sm-8 col-xs-12">
                                    <select class="browser-default custom-select" name="customer_type"
                                            style="display: block;
                                            width: 100%;
                                            margin: 5px 0px 10px 0;
                                            padding: 5px 0px 5px 0;
                                            border: 1px solid #ccc;" onchange="userTypeChange(this.value)">
                                        <option selected value="all_customers">All Members</option>
                                        <option value="cardholders">All Premium Members</option>
                                        <option value="trial">Trial Members</option>
                                        <option value="guest">Guest Members</option>
                                        <option value="influencer">All Influencers</option>
                                        <option value="expired">All Expired Members</option>
                                        <option value="active">Active Members</option>
                                        <option value="inactive">Inactive Members</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row date" style="display: none;">
                                <label class="control-label col-md-2 col-sm-2 col-xs-12">Date</label>
                                <div class="col-md-8 col-sm-8 col-xs-12">
                                    <span style="color: red;">Skip this if want to send SMS to all trial members</span>
                                    <input type="date" class="form-control" name="date">
                                </div>
                                <br>
                            </div>
                            <br>
                            <div class="row">
                                <label class="control-label col-md-2 col-sm-2 col-xs-12">Message</label>
                                <div class="col-md-8 col-sm-8 col-xs-12">
                                <textarea rows="7" name="text_message" onkeyup="countChars();" id="text_message"
                                          class="form-control" placeholder="Write a message"
                                          required>{{old('coupon_tnc')}}</textarea>
                                </div>
                            </div>
                        </div>
                        <p class="center"> Characters Count :
                            <span id="charNum">0</span>
                        </p>
                        <p class="center">160 Characters = 1 SMS</p>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="ln_solid"></div>
                        <div class="form-group">
                            <p class="center">
                                <button type="submit" class="btn btn-activate pull-right">Submit</button>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.production.footer')

<script>
    function countChars() {
        var no_of_chars = $("#text_message").val();
        $("#charNum").text(no_of_chars.length);
    }
    function userTypeChange(type) {
        if(type == 'trial'){
            $(".date").css('display', 'block');
        }else{
            $(".date").css('display', 'none');
        }
    }
</script>