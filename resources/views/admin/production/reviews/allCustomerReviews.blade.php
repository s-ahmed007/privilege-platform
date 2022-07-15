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
            <h3>Review Monitor</h3>
            <a href="{{url('admin/allCustomerReviews')}}" class="btn btn-all">All Reviews</a>
            <a href="{{url('admin/allCustomerDeletedReviews')}}" class="btn btn-expired">Deleted Reviews</a>
        </div>
        <div class="title_right">
            @if (session('review_deleted'))
                <div class="alert alert-danger">{{ session('review_deleted') }}</div>
            @elseif(session('review_edited'))
                <div class="alert alert-success">{{ session('review_edited') }}</div>
            @elseif(session('try_again'))
                <div class="alert alert-warning">{{ session('try_again') }}</div>
            @endif
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="table-responsive">
                    @if($allReviews)
                        <table id="reviewList" class="table table-bordered table-hover table-striped projects">
                            <thead>
                            <tr>
                                <th>S/N<br></th>
                                <th>Customer Details</th>
                                <th>Rating</th>
                                <th>Review</th>
                                <th>Partner Reply</th>
                                <th>Platform</th>
                                @if($delete)
                                    <th>Review Action</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($allReviews as $key => $review)
                                <?php
                                $posted_on = date("Y-M-d H:i:s", strtotime($review->posted_on));
                                $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));
                                ?>
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>
                                        {{ $review->customer->customer_full_name }}<br>
                                        {{ $review->customer->customer_email }}<br>
                                        {{ $review->customer->customer_contact_number }}<br>
                                        @if(!$delete)
                                            <span class="text-danger">{{$review->admin_id == null ? 'Self ' : 'Admin '}}
                                                Deleted
                                            </span><br>
                                        @endif
                                        {!! date_format($created, "d-m-y &#9202; h:i A") !!}<br>
                                        <span style="color: red;">
                                            {{ 'Deleted : Self ('.$review->customer->selfDeletedReviews().') Admin ('.
                                                $review->customer->adminDeletedReviews().')' }}
                                        </span>
                                    </td>
                                    <td>
                                        @for($k=0; $k<$review->rating; $k++)
                                            <i class="bx bxs-star yellow" style="color:#ffc107; font-size: 10px"></i>
                                        @endfor
                                        {{-- @if($review->transaction)
                                            <p class="card-type-guest">Offer</p>
                                        @elseif($review->dealPurchase)
                                            <p class="card-type-premium">Deal</p>
                                        @endif --}}
                                    </td>
                                    <td>
                                        <b>{{ $review->heading }}</b><br><br>
                                        {{ $review->body }}
                                    </td>
                                    <td>
                                        @if(count($review->comments) > 0)
                                            {{$review->comments[0]->comment}}
                                        @endif
                                            <br>
                                        <b>{{ $review->partnerInfo->partner_name.', ' }}
                                        @if($review->transaction)
                                            {{$review->transaction->branch->partner_area}}
                                        @elseif($review->dealPurchase)
                                            {{$review->dealPurchase->voucher->branch->partner_area}}
                                        @else
                                            N/A
                                        @endif
                                        </b>
                                    </td>
                                    <td>
                                        @if($review->platform == \App\Http\Controllers\Enum\PlatformType::web)
                                        <span class="website-label">Website</span>
                                        @elseif($review->platform == \App\Http\Controllers\Enum\PlatformType::android)
                                        <span class="android-label">Android</span>
                                        @elseif($review->platform == \App\Http\Controllers\Enum\PlatformType::ios)
                                        <span class="ios-label">iOS</span>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    @if($delete)
                                        <td class="center">
                                            <a class="btn btn-primary" href="{{url('/edit-review/'.$review->id)}}" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a><br>
                                            <a class="btn btn-delete" href="{{url('admin/deleteReview/'.$review->id)}}"
                                               onclick="return confirm('Are you sure?')" title="Delete">
                                                <i class="fa fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    @endif
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






















