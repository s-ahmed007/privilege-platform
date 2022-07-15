@include('admin.production.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>

<div class="right_col" role="main">
    <div class="row">
        <h3>Active Members</h3>
        <form class="form-inline" action="{{url('admin/sort_monthly_active_member')}}" method="post">
            {{csrf_field()}}
            <div class="form-group">
                <label for="from">From</label>
                @if(isset($from))
                    <input type="date" id="from" name="from" class="form-control" value="{{$from}}">
                @else
                    <input type="date" id="from" name="from" class="form-control" value="{{date('Y-m-01')}}">
                @endif
            </div>
            <div class="form-group">
                <label for="to">To</label>
                @if(isset($from))
                    <input type="date" id="to" name="to" class="form-control" value="{{$to}}">
                @else
                    <input type="date" id="to" name="to" class="form-control" value="{{date('Y-m-d')}}">
                @endif
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary" onclick="">Sort</button>
            </div>
        </form>
        <table id="memberList" class="table table-bordered table-hover table-striped projects">
            <thead>
            <tr>
                <th>S/N</th>
                <th>Name</th>
                <th>Total Transaction</th>
            </tr>
            </thead>
            <tbody>
            <?php $i = 1; ?>
            @foreach ($active_users as $key => $user)
                <tr>
                    <td>{{$i}}</td>
                    <td>{{$user->customer_full_name}}</td>
                    <td>{{$user->monthlyTranCount}}</td>
                </tr>
                <?php $i++; ?>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="row">
        <h3>Recurring Members</h3>
        <table id="recurringMemberList" class="table table-bordered table-hover table-striped projects">
            <thead>
            <tr>
                <th>S/N</th>
                <th>Name</th>
            </tr>
            </thead>
            <tbody>
            <?php $i = 1; ?>
            @foreach ($recurring_users as $user)
                <tr>
                    <td>{{$i}}</td>
                    <td>{{$user->customer_full_name}}</td>
                </tr>
                <?php $i++; ?>
            @endforeach
            </tbody>
        </table>
    </div>

</div>

@include('admin.production.footer')
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">
    $(document).ready(function () {
        $('#memberList').DataTable({
            "order": []
        });
        $('#recurringMemberList').DataTable({
            "order": []
        });
    });
</script>