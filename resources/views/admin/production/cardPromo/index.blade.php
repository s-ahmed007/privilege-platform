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
      <div class="alert alert-success">
         {{ session('status') }}
      </div>
      @elseif (session('delete promo'))
      <div class="alert alert-danger">
         {{ session('delete promo') }}
      </div>
      @elseif(session('try_again'))
      <div class="alert alert-warning">
         {{ session('try_again') }}
      </div>
      @endif
      <h3>All Promo Codes</h3>
      <a type="button" class="btn btn-create" href="{{ url('/card-promo/create') }}" style="margin-left: unset;">+ Add Promo Code</a>
   </div>
</div>
<div class="clearfix"></div>
<div class="container">
   <div class="row">
      <div class="col-xs-12">
         <div class="table-responsive">
            @if($promos)
            <table id="promosList" class="table table-bordered table-hover table-striped projects">
               <thead>
                  <tr>
                     <th style="white-space: nowrap;">Promo Code</th>
                     <th>Type</th>
                     <th style="white-space: nowrap;">Promo Detail</th>
                     <th style="white-space: nowrap;">Flat rate</th>
                     <th>Percentage</th>
                     <th style="white-space: nowrap;">Expiry</th>
                     <th style="white-space: nowrap;">Promo Used</th>
                     <th style="white-space: nowrap;">Usage Limit</th>
                     <th>Action</th>
                  </tr>
               </thead>
               <tbody>
                  @foreach ($promos as $promo)
                  <tr class="promo_row" data-promo-id='{{ $promo->id }}'>
                     <td>
                        <span class="label label-info" style="font-size: 14px;">{{ $promo->code }}</span>
                        @if($promo->seller)
                             <br><b>Seller: {{$promo->seller}}</b>
                        @endif
                        @if($promo->influencer_id != null && $promo->userInfo != null)
                        <br><br>
                        <p><span style="color: #12A03E"><i class="bx bxs-star yellow"></i><b>INFLUENCER</b><br></span>
                           <span>
                           {{$promo->userInfo->customer_full_name}}<br>
                           {{$promo->userInfo->customer_contact_number}}
                           </span>
                        </p>
                        @endif
                     </td>
                      <td>
                          @if($promo->membership_type)
                              @if($promo->membership_type == \App\Http\Controllers\Enum\PromoType::RENEW)
                                  <span class="label renew-label" style="font-size: 14px;">Renew</span>
                              @elseif($promo->membership_type == \App\Http\Controllers\Enum\PromoType::CARD_PURCHASE)
                                  <span class="label new-purchase-label" style="font-size: 14px;">New Purchase</span>
                              @elseif($promo->membership_type == \App\Http\Controllers\Enum\PromoType::UPGRADE)
                                  <span class="label upgrade-label" style="font-size: 14px;">Upgrade</span>
                              @else
                                  <span class="label all-label" style="font-size: 14px;">All</span>
                              @endif
                              @if($promo->month)
                                  For {{$promo->month}}{{$promo->month > 1 ? ' months':' month'}}
                              @endif
                          @endif
                      </td>
                     <td>{!! html_entity_decode($promo->text) !!}</td>
                     @if($promo->flat_rate == null)
                     <td align="middle">N/A</td>
                     @else
                     <td align="middle">{{ $promo->flat_rate }}</td>
                     @endif
                     @if($promo->percentage == null)
                     <td align="middle">N/A</td>
                     @else
                     <td align="middle">{{ $promo->percentage }}</td>
                     @endif
                     <?php $today = date('Y-m-d');
                        $expiry_date = date('Y-m-d',strtotime($promo->expiry_date));
                        if($today <= $expiry_date){
                            echo '<td align="middle">'.$expiry_date.'</td>';
                        } else {
                            echo '<td align="middle" style="font-weight: bold; color: red;">Expired</td>';
                        }
                        ?>
                     <td align="middle">{{ $promo->promo_usage_count }}</td>
                     <td align="middle">{{ $promo->usage }}</td>
                     <td style="white-space: nowrap;">
                        <button class="btn btn-edit editBtn" data-promo-id='{{ $promo->id }}' title="Edit">
                        <i class="fa fa-edit"></i></button>
                        <button class="btn btn-delete deleteBtn" data-promo-id='{{ $promo->id }}' title="Delete"
                           <?php if($promo->promo_used > 0) echo 'title="cant be deleted" disabled';?>>
                        <i class="glyphicon glyphicon-trash icon-white"></i></button>
                         <br>
                        @if($promo->active == 1)
                            <button class="btn btn-deactivate deactiveBtn" data-promo-id='{{ $promo->id }}' title="Deactivate">
                            <i class="glyphicon glyphicon-pause"></i>
                            </button>
                        @else
                            <button class="btn btn-activate activeBtn" data-promo-id='{{ $promo->id }}' title="Activate">
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
               {{ 'No Promo Code found.' }}
            </div>
            @endif
         </div>
         <!--end of .table-responsive-->
      </div>
   </div>
</div>
@include('admin.production.footer')
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
{{-- ============================================================================================================
========================Opening edit & delete====================
============================================================================================================= --}}
<script>
   $('.deleteBtn').on('click', function (event) {
       if (confirm("Are you sure?")) {
           //fetch the promo id
           var promoId = $(this).attr('data-promo-id');
           var url = "{{ url('/card-promo') }}";
           url += '/' + promoId;
   
           $('<form action="' + url + '" method="POST">' +
               '<input type="hidden" name="_token" value="{{ csrf_token() }}"/>' +
               '<input type="hidden" name="_method" value="DELETE"/>' +
               '</form>').appendTo($(document.body)).submit();
       }
       return false;
   });
   
   $('.editBtn').on('click', function (event) {
       //fetch the promo id
       var promoId = $(this).attr('data-promo-id');
       var url = "{{ url('/card-promo') }}";
       url += '/' + promoId + '/edit';
       window.location.href = url;
   });
   
   $('.activeBtn').on('click', function (event) {
       //fetch the promo id
       var promoId = $(this).attr('data-promo-id');
       var url = "{{ url('/active-card-promo') }}" + '/' + promoId;
       window.location.href = url;
   });
   
   $('.deactiveBtn').on('click', function (event) {
       //fetch the promo id
       var promoId = $(this).attr('data-promo-id');
       var url = "{{ url('/deactive-card-promo') }}" + '/' + promoId;
       window.location.href = url;
   });
</script>
<script type="text/javascript">
   $(document).ready(function () {
       $('#promosList').DataTable({
           //"paging": false
           "order": []
       });
   });
</script>