@include('admin.production.header')
{{--Crop image--}}
<script src="{{asset('js/imageCrop/jquery.js')}}"></script>
<script src="{{asset('js/imageCrop/croppie.js')}}"></script>
<link href="{{asset('admin/vendors/croppie/croppie.css')}}" rel="stylesheet">

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Edit Review</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="panel-body col-sm-10">
        @if (isset($reviewInfo))
            <form action="{{ url('reviewEditDone/'. $reviewInfo['id']) }}" class="form-horizontal">
                <div class="form-group">
                    <label for="review_heading">Review Heading: </label>
                    <div>
                        <input type="text" name="review_heading" class="form-control" id="review_heading"
                               value="{{ $reviewInfo['heading'] }}" placeholder="Review Heading">
                    </div>
                </div>
                <div class="form-group">
                    <label for="review_comment">Review Description: </label>
                    <div>
               <textarea name="review_comment" rows="6" style="width: 100%"
                         placeholder="Review Texts">{{ $reviewInfo['body'] }}</textarea>
                    </div>
                </div>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="prev_url" value="{{url()->previous()}}"/>
                <div class="form-group">
                        <button type="submit" class="btn btn-activate pull-right">Submit</button>
                </div>
            </form>
        @endif
    </div>
</div>

@include('admin.production.footer')