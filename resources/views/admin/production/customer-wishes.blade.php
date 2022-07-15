@include('admin.production.header')

<div class="right_col" role="main">
    <div class="page-title">
        @if(session('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif
        <div class="title_left">
            <h3>All Wishes</h3>
        </div>
    </div>
    <div class="container">
      <div class="row">
         <div class="col-xs-12">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped projects">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer Info</th>
                        <th>Comment</th>
                        <th>Posted On</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php $i=1; @endphp
                    @foreach($wishes as $wish)
                        <tr>
                            <th>{{$i}}</th>
                            <td>{{$wish->customer_id}}<br>
                                @if($wish->account->info->customerHistory->type == \App\Http\Controllers\Enum\CustomerType::card_holder)
                                    <span class="premium-label">Premium Member</span><br>
                                @elseif($wish->account->info->customerHistory->type == \App\Http\Controllers\Enum\CustomerType::trial_user)
                                    <span class="trial-label">Trial</span><br>
                                @endif
                                {{$wish->account->info->customer_full_name}}<br>
                                {{$wish->account->info->customer_email}}
                            </td>
                            <td>{{$wish->comment}}</td>
                            <td>
                                {{$wish->posted_on != null ? date("F d, Y h:i A", strtotime($wish->posted_on)) : 'N/A'}}
                            </td>
                            <td>
                                <input type="button" class="btn btn-delete" value="Delete" onclick="delete_wish('{{$wish->id}}')">
                            </td>
                        </tr>
                        @php $i++; @endphp
                    @endforeach
                    </tbody>
                </table>
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

<script>
    function delete_wish(wish_id) {
        if (confirm('Are you sure to delete the wish?')) {
            window.location.href = "{{ url('/delete_wish') }}" + '/' + wish_id;
        }
    }
</script>