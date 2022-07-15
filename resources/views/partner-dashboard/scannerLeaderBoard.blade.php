@include('partner-dashboard.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>
<style>
    .dataTables_length label select{
        padding: 5px 12px;
        background-color: #fff;
        border-radius: 2px;
        border: 1px solid #e4e7ea
    }
</style>
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-5 col-md-4 col-sm-4 col-xs-12">
            <h3 class="d-inline-block">{{__('partner/leaderboard.partner_leaderboard')}}</h3>
            @if($prize)
                <h5 class="d-inline-block float-right">Reward for {{$prize->month_name}} {{'"'.$prize->prize_text.'"'}}</h5>
            @endif
        </div>
    </div>
    <!-- /.row -->
    <!-- .row -->
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <table id="leaderBoard" class="table table-striped projects">
                <thead>
                <tr>
                    <th>Position</th>
                    <th>Branch</th>
                    <th>Point</th>
                </tr>
                </thead>
                <tbody>
                @if(!empty($leaderBoard))
                    <?php $i=1; ?>
                    @foreach ($leaderBoard as $key => $value)
                        <tr>
                            <td>{{ $i }}
                                <span style="margin-left: 10px">
                                <?php
                                if($value['prev_index'] != null){
                                    if($value['prev_index'] == $key){
                                        echo '<i class="fa fa-minus"></i>';
                                    }elseif($value['prev_index'] > $key){
                                        echo '<i class="fa fa-arrow-up"></i>';
                                    }elseif($value['prev_index'] < $key){
                                        echo '<i class="fa fa-arrow-down"></i>';
                                    }
                                }else{
                                    echo '<i class="fa fa-minus"></i>';
                                }
                                ?>
                                </span>
                            </td>
                            <td>
                                <img src="{{$value['profile_image']}}" alt="Profile Image" width="50" height="50" class="rounded-circle">
                                {{ $value['partner_name'] }}, {{ $value['area'] }}
                            </td>
                            <td>{{ $value['point'] }}</td>
                        </tr>
                        <?php $i++; ?>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
    </div>
    <!-- /.row -->
</div>
    <!-- /.container-fluid -->

@include('partner-dashboard.footer')
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">
    $(document).ready(function () {
        $('#leaderBoard').DataTable({
            //"paging": false
            "order": []
        });
    });
</script>