@include('partner-dashboard.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-5 col-md-4 col-sm-4 col-xs-12">
            <h3>{{__('partner/point_prize.reward_history')}}</h3>
        </div>
    </div>

<!-- /.row -->
<!-- .row -->
    <div class="row">
    <div class="col-md-12 col-xs-12">
        @if(count($scanner_prize_history) > 0)
            <div class="table-responsive">
                <table id="prizeList" class="table table-bordered table-hover table-striped projects">
                    <thead>
                    <tr>
                        <th>Reward</th>
                        <th>Point</th>
                        <th>Time</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($scanner_prize_history as $row)
                        <tr>
                            <td>{{ $row->text }}
                            </td>
                            <td>
                                {{ $row->point }}
                            </td>
                            <?php
                            $posted_on=date("h:i A | F d, Y", strtotime($row['posted_on']));
                            ?>
                            <td>{{ $posted_on }}</td>
                            <td>
                                @if($row->status == 1)
                                    <span class="badge badge-success">Successful</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p>{{__('partner/point_prize.empty_reward_history')}}</p>
        @endif

    </div>
</div>
<!-- /.row -->

</div>

@include('partner-dashboard.footer')
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#prizeList').DataTable({
            "order": []
        });
    });
</script>