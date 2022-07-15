@include('transactionRequest.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3 class="d-inline-block">Merchant Leader Board</h3>
            @if($prize)
                <h5 class="d-inline-block float-right">Reward for {{$prize->month_name}} {{'"'.$prize->prize_text.'"'}}</h5>
            @endif
        </div>
        <div class="title_right">
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <table id="leaderBoard" class="table table-striped projects">
                        <thead>
                        <tr>
                            <th>Position</th>
                            <th>Branch</th>
                            <th>Point</th>
                            <th>Activity</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(!empty($leaderBoard))
                            <?php $i=1; ?>
                            @foreach ($leaderBoard as $key => $value)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>
                                        <img src="{{$value['profile_image']}}" alt="Profile Image" width="50" height="50" class="rounded-circle">
                                        {{ $value['partner_name'] }}, {{ $value['area'] }}
                                    </td>
                                    <td>{{ $value['point'] }}</td>
                                    <td>
                                        <?php
                                            if($value['prev_index'] != null){
                                                if($value['prev_index'] == $key){
                                                    echo '<i class="fas fa-minus"></i>';
                                                }elseif($value['prev_index'] > $key){
                                                    echo '<i class="fas fa-arrow-up"></i>';
                                                }elseif($value['prev_index'] < $key){
                                                    echo '<i class="fas fa-arrow-down"></i>';
                                                }
                                            }else{
                                                echo '<i class="fas fa-minus"></i>';
                                            }
                                        ?>
                                    </td>
                                </tr>
                                <?php $i++; ?>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('transactionRequest.footer')
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">
    $(document).ready(function () {
        $('#leaderBoard').DataTable({
            //"paging": false
            "order": []
        });
    });
</script>