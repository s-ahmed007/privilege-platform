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
         @if (session('status'))
             <div class="alert alert-success">{{ session('status') }}</div>
         @elseif (session('delete'))
             <div class="alert alert-danger">{{ session('delete') }}</div>
         @elseif(session('try_again'))
             <div class="alert alert-warning">{{ session('try_again') }}</div>
         @endif
        @if($show_branch_info)
         <h3>Partner Rewards</h3>
        @else
         @if($branch_info == null)
             <h3>Royalty Rewards</h3>
         @else
             <h3>Rewards of {{$branch_info->info->partner_name.' - '.$branch_info->partner_area}}</h3>
         @endif
        @endif
         @if($show_tabs)
         <a class="btn btn-guest" href="{{url('admin/reward/'.\App\Http\Controllers\Enum\AdminScannerType::royalty_branch_id)}}">Royalty Rewards</a>
         <a class="btn btn-premium" href="{{url('admin/partner_rewards/')}}">Partner Rewards</a>
         @endif
      </div>
   </div>
   <div class="clearfix"></div>
   <div class="container">
      <div class="row">
         <div class="col-xs-12">
            <div class="table-responsive">
               @if($offers)
               <table id="rewardsList" class="table table-bordered table-hover table-striped projects">
                  <thead>
                     <tr>
                        <th>S/N</th>
                        <th>Image</th>
                        <th>Description</th>
                        <th>Credits Required</th>
                         <th>Validity</th>
                         <th>Priority</th>
                         <th>Available</th>
                         <th>Used</th>
                         {{--                        <th>Weekdays</th>--}}
                        <th>Action</th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach ($offers as $key => $offer)
                     <?php
                        $dates = $offer->date_duration[0];
                        $days = $offer->weekdays[0];
                        $times = $offer['time_duration'];
                        ?>
                     <tr class="opening_row" data-offer-id='{{ $offer->id }}'>
                         <td>{{$key+1}}</td>
                        <td><img src="{{$offer->image}}" alt="Reward Image" width="100" height="100"></td>
                        <td><b>{{ $offer->offer_description }}</b>
                        @if($show_branch_info)
                                <br><span>{{$offer->branch->info->partner_name.' - '.$offer->branch->partner_area}}</span>
                        @endif
                            <br>
                            @if(date('Y-m-d', strtotime($dates['to'])) < date('Y-m-d'))
                                <span class="redeemed-reward-expired">EXPIRED</span>
                            @endif
                        </td>
                         <td>{{ $offer->selling_point}}</td>
                         <td>{{ 'From: '.$dates['from']}}<br>{{'To: '.$dates['to'] }}</td>
                         <td>{{ $offer->priority }}</td>
                         <td>
                             @if($offer->counter_limit)
                                {{$offer->counter_limit - $offer->rewardRedeems->sum('quantity')}}
                             @else
                                Unlimited
                             @endif
                         </td>

                         <td>{{$offer->rewardRedeems->sum('quantity')}}</td>
{{--                        <td>--}}
{{--                           <b>sat:</b> {{$days['sat']}}--}}
{{--                           <b>sun:</b>  {{$days['sun']}}--}}
{{--                           <b>mon:</b>  {{$days['mon']}}--}}
{{--                           <b>tue:</b>  {{$days['tue']}}--}}
{{--                           <b>wed:</b> {{$days['wed']}}--}}
{{--                           <b>thu:</b> {{$days['thu']}}--}}
{{--                           <b>fri:</b> {{$days['fri']}}--}}
{{--                        </td>--}}
                        <td>
                           <button class="btn btn-edit editBtn" title="Edit" data-offer-id='{{ $offer->id }}'>
                           <i class="fa fa-edit"></i>
                           </button>
                           @if(Session::get('admin') == \App\Http\Controllers\Enum\AdminRole::superadmin)
                               <button class="btn btn-delete deleteBtn" title="Delete" data-offer-id='{{ $offer->id }}'>
                               <i class="fa fa-trash-alt"></i>
                               </button>
                           @endif
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
<script>
   $('.deleteBtn').on('click', function (event) {
       if (confirm("Are you sure to delete?")) {
           //fetch the opening id
           var offerId = $(this).attr('data-offer-id');
           var url = "{{ url('/admin/reward') }}";
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
       var url = "{{ url('/admin/reward') }}";
       url += '/' + offerId + '/edit';
       window.location.href = url;
   });
   
   $('.activeBtn').on('click', function (event) {
       if (confirm('Are you sure to activate?')) {
           //fetch the opening id
           var offerId = $(this).attr('data-offer-id');
           var url = "{{ url('/activate-reward') }}" + '/' + offerId;
           window.location.href = url;
       }
   });
   
   $('.deactiveBtn').on('click', function (event) {
       if (confirm('Are you sure to deactivate?')) {
           //fetch the opening id
           var offerId = $(this).attr('data-offer-id');
           var url = "{{ url('/deactivate-reward') }}" + '/' + offerId;
           window.location.href = url;
       }
   });
</script>
<script type="text/javascript">
   $(document).ready(function () {
       $('#rewardsList').DataTable({
           //"paging": false
           "order": []
       });
   });
</script>