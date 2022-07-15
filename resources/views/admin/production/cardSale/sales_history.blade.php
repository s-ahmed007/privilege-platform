@include('admin.production.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Sales History of ({{$history->first_name.' '.$history->last_name}})</h3>
        </div>

    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-4">
            <ul>
                <li>Current Commission: <b>{{ $history->account->balance->credit }}</b></li>
                <li>Given Commission: <b>{{ $history->account->balance->credit_used }}</b></li>
                <li>Total Commission: <b>{{ $history->account->balance->credit + $history->account->balance->credit_used}}</b></li>
            </ul>
        </div>
        <div class="col-md-4">
            <ul>
                <li>Current Due: <b>{{ $history->account->balance->debit }}</b></li>
                <li>Paid Due: <b>{{ $history->account->balance->debit_used }}</b></li>
                <li>Total Due: <b>{{ $history->account->balance->debit + $history->account->balance->debit_used}}</b></li>
            </ul>
        </div>
        <div class="col-md-4">
            <ul>
                <li>Current Sales: <b>{{ $history->account->balance->credit + $history->account->balance->debit }}</b></li>
                <li>Previous Sales: <b>{{ $history->account->balance->credit_used + $history->account->balance->debit_used }}</b></li>
                <li>Total Sales: <b>{{ $history->account->balance->credit + $history->account->balance->debit +
                    $history->account->balance->credit_used + $history->account->balance->debit_used}}</b></li>
            </ul>
        </div>
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">

                    <table id="cardList" class="table table-striped projects">
                        <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Type</th>
                            <th>Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($history->salesHistory as $key => $value)
                            <tr>
                                <td>
                                    {{ $value->customerInfo->customer_full_name }}<br>
                                    {{ $value->customerInfo->customer_contact_number }}<br>
                                    {{ $value->customerInfo->customer_email }}
                                </td>
                                <td>
                                    @if($value->type == 1)
                                        <p class="card-type-premium">Premium Member</p>
                                    @elseif($value->type == 2)
                                        <p class="card-type-guest">Virtual Card</p>
                                    @elseif($value->type == 3)
                                        <p class="card-type-trial">Trial User</p>
                                    @endif
                                    @if($value->type != 3)
                                        <b>{{$value->sslInfo->month}}{{$value->sslInfo->month > 1 ? ' months':' month'}}</b>
                                        @if($value->sslInfo->sellerCommission)
                                            <br>Commission earned: {{$value->sslInfo->sellerCommission->commission.'tk'}}
                                        @endif
                                    @endif
                                </td>
                                <td>{{ date("F d, Y", strtotime($value->sslInfo->tran_date)) }}</td>
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
