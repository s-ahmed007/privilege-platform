@include('admin.production.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if (session('assign_success'))
                <div class="alert alert-success">
                    {{ session('assign_success')}}
                    @if(session('double_entry') > 0)
                        <span style="color: #000; float: right;">{{session('double_entry'). ' entry skipped'}}</span>
                    @endif
                </div>
                @if(session('card_exists'))
                    <div class="alert alert-danger">
                        <ul>
                        @foreach(session('card_exists') as $card)
                                <li>{{$card. ' this id already exists'}}</li>
                        @endforeach
                        </ul>
                    </div>
                @endif
            @elseif (session('update_success'))
                <div class="alert alert-success">
                    {{ session('update_success')}}
                </div>
            @elseif (session('deleted'))
                <div class="alert alert-danger">
                    {{ session('deleted')}}
                </div>
            @endif
            <h3>All Assigned Cards of ({{$user->info->first_name.' '.$user->info->last_name}})</h3>
            <a type="button" class="btn btn-create" href="{{ url('/assign-card/'.$user->id) }}" style="margin-left: unset;">Assign card</a>
        </div>

    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <table id="cardList" class="table table-striped projects">
                        <thead>
                        <tr>
                            <th>Card</th>
                            <th>Type</th>
                            <th>Sales Info</th>
                            <th>Assigned on</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($assigned_card as $key => $value)
                            <tr>
                                <td>{{ $value->card_number }}</td>
                                <td>{{ $value->card_type == 2 ? 'Royalty Card' : ''}}</td>
                                <td>
                                    @if($value->status == 1)
                                        <b>Month: {{ $value->ssl[0]->month }}</b><br>
                                        @php
                                            if(count($value->cardPromoUsage) > 0){
                                                $amount = (int)$value->ssl[0]->amount;
                                                $promo = $value->cardPromoUsage[0]->promoCode->code;
                                                echo '<b>'.'Promo used: '.$promo.'</b>'.'<br>';
                                            }elseif(count($value->ssl) > 0 ){
                                                $amount = (int)$value->ssl[0]->amount;
                                            }
                                        @endphp
                                        <b>Amount: {{ $amount }}</b><br>
                                    @endif
                                </td>
                                <td>{{ date("F d, Y h:i A", strtotime($value->assigned_on)) }}</td>
                                <td>
                                    @if($value->status == 0)
                                        <span class="label label-success">Available</span>
                                    @else
                                        {{date("F d, Y h:i A", strtotime($value->sold_on))}}
                                        <span class="label label-warning">Sold</span>
                                    @endif
                                </td>
                                <td align="center">
                                    <select id="card_action_{{$value->id}}" onchange="card_action('{{$value->id}}')"
                                        class="selectChangeOff">
                                        <option disabled selected>--Options--</option>
                                        <option value="1">Edit</option>
                                        <option value="2">Delete</option>
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.production.footer')
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

<script>
    function card_action(id) {
        var option_type = document.getElementById("card_action_" + id).value;

        if (option_type == 1) {
            var url = "{{ url('/edit-assigned-card') }}" + '/' + id;
            window.location = url;
        } else if (option_type == 2) {
            if(confirm('Are you sure?')){
                var url = "{{ url('/delete-assigned-card') }}" + '/' + id;
                window.location = url;
            }
        }
    }
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#cardList').DataTable({
            //"paging": false
            "order": []
        });
    });
    //to keep select option unselected from prev page
    $(document).ready(function () {
        $(".selectChangeOff").each(function () {
            $(this).val($(this).find('option[selected]').val());
        });
    })
</script>
