@include('admin.production.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>
<style>
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        color: #337ab7 !important;background-color: #eee !important}
    .dataTables_wrapper .dataTables_paginate .paginate_button{
        color: #337ab7 !important; background-color: #ffffff !important}
    .dataTables_wrapper .dataTables_paginate .paginate_button.current{
        color: #ffffff !important; background-color: #337ab7 !important}
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover{
        color: #ffffff !important; background-color: #337ab7 !important}
</style>
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Pending Review Reply</h3>
        </div>
        <div class="title_right">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="table-responsive">
                    @if($allReviewReplies)
                        <table id="reviewList" class="table table-bordered table-hover table-striped projects">
                            <thead>
                            <tr>
                                <th>S/N<br></th>
                                <th>Customer Details</th>
                                <th>Rating</th>
                                <th>Review</th>
                                <th>Reply</th>
                                <th>partner</th>
                                <th>Platform</th>
                                <th>Reply Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($allReviewReplies as $key => $reply)
                                <?php
                                $posted_on = date("Y-M-d H:i:s", strtotime($reply->review->posted_on));
                                $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));
                                ?>
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>
                                        {{ $reply->review->customer->customer_full_name }}<br>
                                        {{ $reply->review->customer->customer_email }}<br>
                                        {{ $reply->review->customer->customer_contact_number }}<br>
                                        {!! date_format($created, "d-m-y &#9202; h:i A") !!}<br>
                                    </td>
                                    <td>
                                        @for($k=0; $k<$reply->review->rating; $k++)
                                            <i class="bx bxs-star yellow" style="color:#ffc107; font-size: 10px"></i>
                                        @endfor
                                    </td>
                                    <td>
                                        <b>{{ $reply->review->heading }}</b><br><br>{{ $reply->review->body }}
                                    </td>
                                    <td><b>{{ $reply->comment }}</b></td>
                                    <td>{{ $reply->review->partnerInfo->partner_name.', ' }}
                                        @if($reply->review->transaction)
                                            {{$reply->review->transaction->branch->partner_area}}
                                        @elseif($reply->review->dealPurchase)
                                            {{$reply->review->dealPurchase->voucher->branch->partner_area}}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if($reply->review->platform == \App\Http\Controllers\Enum\PlatformType::web)
                                            <span class="website-label">Website</span>
                                        @elseif($reply->review->platform == \App\Http\Controllers\Enum\PlatformType::android)
                                            <span class="android-label">Android</span>
                                        @elseif($reply->review->platform == \App\Http\Controllers\Enum\PlatformType::ios)
                                            <span class="ios-label">iOS</span>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="center">
                                        <a class="btn btn-primary"
                                           href="{{url('/admin/edit-review-reply/'.$reply->id)}}" title="Edit">
                                            <i class="fa fa-edit"></i>
                                        </a><br>
                                        <a class="btn btn-success"
                                           href="{{url('admin/approveReviewReply/'.$reply->id)}}"
                                           onclick="return confirm('Are you sure to approve?')">
                                            <i class="fa fa-check"></i>
                                        </a><br>
                                        <a class="btn btn-danger" href="{{url('admin/rejectReviewReply/'.$reply->id)}}"
                                           onclick="return confirm('Are you sure to reject?')">
                                            <i class="fa fa-times"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div style="font-size: 1.4em; color: red;">
                            {{ 'No Reviews found.' }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@include('admin.production.footer')
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">
    $(document).ready(function () {
        $('#reviewList').DataTable({
            //"paging": false
            "order": []
        });
    });
</script>






















