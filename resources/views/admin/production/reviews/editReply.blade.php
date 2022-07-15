@include('admin.production.header')
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Edit Review Reply</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="panel-body col-sm-10">
        @if (isset($reply))
            <form action="{{ url('admin/edit-review-reply/'. $reply->id) }}" class="form-horizontal" method="post">
                <div class="form-group">
                    <label for="review_heading">Review Reply: </label>
                    <div>
                        <input type="text" name="review_reply" class="form-control" id="review_reply"
                               value="{{ $reply->comment }}" placeholder="Review Heading">
                    </div>
                </div>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group">
                        <button type="submit" class="btn btn-activate pull-right">Submit</button>
                </div>
            </form>
        @endif
    </div>
</div>

@include('admin.production.footer')