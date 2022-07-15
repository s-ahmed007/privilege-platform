@include('admin.production.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @elseif (session('delete'))
                <div class="alert alert-danger">
                    {{ session('delete') }}
                </div>
            @elseif(session('try_again'))
                <div class="alert alert-warning">
                    {{ session('try_again') }}
                </div>
            @endif
            <h3>{{$branch_info->info->partner_name.' - '.$branch_info->partner_area}}</h3>
            <h3>Offers /Edit /Display</h3>
             <a type="button" class="btn btn-create" href="{{ url('/branch-offers/create?id='.$id) }}" style="margin-left: unset;">+ Create A New Offer</a>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    @if($offers)
                        <table id="offersList" class="table table-striped projects">
                            <thead>
                            <tr>
                                <th>Offer Heading</th>
                                <th>Date Duration</th>
                                <th>Weekdays</th>
                                <th>Times</th>
                                <th>Credit</th>
                                <th>Priority</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($offers as $offer)
                                <?php
                                    $dates = $offer->date_duration[0];
                                    $days = $offer->weekdays[0];
                                    $times = $offer['time_duration'];
                                ?>
                                <tr class="opening_row" data-offer-id='{{ $offer->id }}'>
                                    <td>{{ $offer->offer_description }}</td>
                                    <td>{{ 'From: '.$dates['from']}}<br>{{'To: '.$dates['to'] }}</td>
                                    <td>
                                        <b>sat:</b> {{$days['sat']}}
                                        <b>sun:</b>  {{$days['sun']}}
                                        <b>mon:</b>  {{$days['mon']}}
                                        <b>tue:</b>  {{$days['tue']}}
                                        <b>wed:</b> {{$days['wed']}}
                                        <b>thu:</b> {{$days['thu']}}
                                        <b>fri:</b> {{$days['fri']}}
                                    </td>
                                    <td>
                                        <?php
                                        $i=0;
                                            foreach ($times as $key => $time){
                                                echo 'From: '.date('h:i A', strtotime($time['from']));
                                                echo ' To: '.date('h:i A', strtotime($time['to']));
                                                echo $i != count($times)-1 ? ',' : '';
                                                echo '<br>';
                                                $i++;
                                            }
                                        ?>
                                    </td>
                                    <td>{{ $offer->point}}</td>
                                    <td>{{ $offer->priority }}</td>
                                    <td>
                                        <button class="btn btn-edit editBtn" title="Edit" data-offer-id='{{ $offer->id }}'>
                                            <i class="fa fa-edit"></i>
                                        </button>
{{--                                        <button class="btn btn-delete deleteBtn" title="Delete" data-offer-id='{{ $offer->id }}'>--}}
{{--                                            <i class="fa fa-trash-alt"></i>--}}
{{--                                        </button>--}}
                                        @if($offer->active == 1)
                                            <button class="btn btn-deactivate deactiveBtn" title="Deactivate"
                                                data-offer-id='{{ $offer->id }}'>
                                                <i class="glyphicon glyphicon-pause"></i>
                                            </button>
                                        @else
                                            <button class="btn btn-activate activeBtn" title="Activate" data-offer-id='{{ $offer->id }}'>
                                                <i class="glyphicon glyphicon-play"></i>
                                            </button>
                                        @endif
                                        {{-- @if($offer->point_customize_id == null)
                                            <button class="btn btn-primary addCusPoint" title="Add custom point"
                                                data-offer-id='{{ $offer->id }}'>
                                                <i class="glyphicon glyphicon-plus"></i><i class="glyphicon glyphicon-tag"></i>
                                            </button>
                                        @else
                                            <button class="btn btn-primary editCusPoint" title="Edit custom point"
                                                data-offer-id='{{ $offer->id }}'>
                                                <i class="fa fa-edit"></i><i class="glyphicon glyphicon-tag"></i>
                                            </button>
                                        @endif --}}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div style="font-size: 1.4em; color: red;">
                            {{ 'No Offer found.' }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.production.footer')
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

{{-- ============================================================================================================
  ========================Opening edit & delete====================
============================================================================================================= --}}
<script>
    $('.deleteBtn').on('click', function (event) {
        if (confirm("Are you sure to delete?")) {
            //fetch the opening id
            var offerId = $(this).attr('data-offer-id');
            var url = "{{ url('/branch-offers') }}";
            url += '/' + offerId;

            $('<form action="' + url + '" method="POST">' +
                '<input type="hidden" name="_token" value="{{ csrf_token() }}"/>' +
                '<input type="hidden" name="_method" value="DELETE"/>' +
                '</form>').appendTo($(document.body)).submit();
        }
        return false;
    });

    $('.editBtn').on('click', function (event) {
        //fetch the opening id
        var offerId = $(this).attr('data-offer-id');
        var url = "{{ url('/branch-offers') }}";
        url += '/' + offerId + '/edit';
        window.location.href = url;
    });

    $('.activeBtn').on('click', function (event) {
        if(confirm('Are you sure to activate?')){
            //fetch the opening id
            var offerId = $(this).attr('data-offer-id');
            var url = "{{ url('/active-offer') }}" + '/' + offerId;
            window.location.href = url;
        }
    });

    $('.deactiveBtn').on('click', function (event) {
        if(confirm('Are you sure to deactivate?')) {
            //fetch the opening id
            var offerId = $(this).attr('data-offer-id');
            var url = "{{ url('/deactive-offer') }}" + '/' + offerId;
            window.location.href = url;
        }
    });

    $('.addCusPoint').on('click', function (event) {
        //fetch the opening id
        var offerId = $(this).attr('data-offer-id');
        var url = "{{ url('/add-custom-point') }}" + '/' + offerId;
        window.location.href = url;
    });

    $('.editCusPoint').on('click', function (event) {
        //fetch the opening id
        var offerId = $(this).attr('data-offer-id');
        var url = "{{ url('/edit-custom-point') }}" + '/' + offerId;
        window.location.href = url;
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('#offersList').DataTable({
            //"paging": false
            "order": []
        });
    });
</script>