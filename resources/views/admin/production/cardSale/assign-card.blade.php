@include('admin.production.header')

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if (session('assign_success'))
                <div class="alert alert-success">
                    {{ session('assign_success') }}
                </div>
            @elseif (session('assign_fail'))
                <div class="alert alert-warning">
                    {{ session('assign_fail') }}
                </div>
            @endif
            <h3>Assign Card</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <form class="form-horizontal form-label-left" method="post" action="{{ url('/store-assigned-card/'.$userId) }}"
                          enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group" id="card">
                            <button type="button" name="add" id="add" class="btn btn-primary">+ Add Card</button>
                            {{--<h4>Total Card <span id="card_numbers" class="badge"></span></h4>--}}
                            <button type="button" class="btn btn-primary" style="float: right">Total Card <span id="card_numbers" class="badge"></span></button>
                        </div>
                        <div class="form-group">
                            <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                                <button type="submit" class="btn btn-primary submit">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.production.footer')
<script type="text/javascript">
    totalCard();
    $(document).ready(function () {
        var i = 0;
        $('#add').click(function () {
            i++;
            $('#card').append(
                '<div id="row' + i + '" class="total_card"><br><br>'
                + '<div class="col-md-4 col-sm-4 col-xs-12">'
                + '<input type="text" class="form-control" placeholder="Card Number" name="card_number[]" minlength="16" maxlength="16" required>'
                + '</div>'
                + '<div class="col-md-3 col-sm-3 col-xs-12">'
                + '<select class="form-control" name="card_type[]">'
                + '<option value="2">Royalty Premium Membership</option>'
                + '</select>'
                + '</div>'
                + '<div class="col-md-2 col-sm-2 col-xs-12">'
                + '<button name="remove" id="' + i + '" class="btn btn-danger btn_remove">Remove</button>'
                + '</div>'
                + '</div>'
            );
            totalCard();
        });

        $(document).on('click', '.btn_remove', function () {
            var button_id = $(this).attr("id");
            $('#row' + button_id + '').remove();
            totalCard();
        });
    });
    function totalCard() {
        var totalCard = $('.total_card').length;
        $("#card_numbers").html(totalCard);
        if(totalCard > 0){
            $(".submit").show();
        }else{
            $(".submit").hide();
        }
    }
</script>