@include('b2b2c.layout.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            <h3>Members</h3>
        </div>
        <div class="title_right"></div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                @if($customers)
                        <table class="table table-striped projects" id="userList">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th style="width: 10%">Image</th>
                                <th style="width: 10%">Customer ID</th>
                                <th style="width: 15%">Customer Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>DOB</th>
                                <th>Gender</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($customers as $customer)
                                <tr>
                                    <th>{{ $customer->serial }}</th>
                                    <td><img src="{{ $customer->customerInfo->customer_profile_image }}" width="100%" style="border-radius: 50%"></td>
                                    <td>{{ $customer->customer_id }}</td>
                                    <td>{{ $customer->customerInfo->customer_first_name.' '.$customer->customerInfo->customer_last_name }}</td>
                                    <td>{{ $customer->customerInfo->customer_email }}</td>
                                    <td>{{ $customer->customerInfo->customer_contact_number }}</td>
                                    <td>{{ $customer->customerInfo->customer_dob != null ? $customer->customerInfo->customer_dob : 'N/A' }}</td>
                                    <td>{{ $customer->customerInfo->customer_gender != null ? $customer->customerInfo->customer_gender : 'N/A' }}</td>
                                    <td style="text-align: center;">
                                        <select id="customer_edit_{{$customer->customer_id}}"
                                                onchange="customer_edit({{$customer->customer_id}})">
                                            <option value="0" disabled selected>--Options--</option>
                                            <option value="1">Edit Info</option>
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div style="font-size: 1.4em; color: red;">
                            {{ 'No customers found.' }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@include('b2b2c.layout.footer')
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">
    $(document).ready(function () {
        $('#userList').DataTable({
            //"paging": false
        });
    });
</script>
<script>
    function customer_edit(customer_id) {
        var option_type = document.getElementById("customer_edit_" + customer_id).value;
        //return false;
        if (option_type == 1) {
            var url = "{{url('/client/edit-customer')}}" + '/' + customer_id;
            window.location = url;
        }
    }
</script>