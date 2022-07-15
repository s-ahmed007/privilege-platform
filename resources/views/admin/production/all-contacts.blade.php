@include('admin.production.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>
<div class="right_col" role="main">
   <div class="page-title">
      <div class="title_left">
         @if (session('contact-deleted'))
         <div class="alert alert-success">
            {{ session('contact-deleted') }}
         </div>
         @endif
         <h3>All Contacts</h3>
      </div>
   </div>
   <div class="clearfix"></div>
   <div class="container">
      <div class="row">
         <div class="col-xs-12">
            <div class="table-responsive">
               @if($contacts)
               <table id="contactList" class="table table-bordered table-hover table-striped projects">
                  <thead>
                     <tr>
                        <th style="white-space: nowrap;">Name</th>
                        <th style="white-space: nowrap;">Email</th>
                        <th style="white-space: nowrap;">Comment</th>
                        <th style="white-space: nowrap; width: 10%;">Time</th>
                        <th>Action</th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach ($contacts as $contact)
                     <tr class="promo_row" data-promo-id='{{ $contact->id }}'>
                        <td><span style="font-size: 14px;">{{ $contact->name }}</span></td>
                        <td><span class="label label-info" style="font-size: 14px;">{{ $contact->email }}</span></td>
                        <td>{!! html_entity_decode($contact->comment) !!}</td>
                        <td>{{ date("F d, Y", strtotime($contact->posted_on))}}</td>
                        <td style="white-space: nowrap;">
                           <button class="btn btn-delete deleteBtn" data-contact-id='{{ $contact->id }}'>
                           <i class="fa fa-trash-alt"></i></button>
                        </td>
                     </tr>
                     @endforeach
                  </tbody>
                  <tfoot>
                     <tr>
                     </tr>
                  </tfoot>
               </table>
               @else
               <div style="font-size: 1.4em; color: red;">
                  {{ 'No Promo Code found.' }}
               </div>
               @endif
            </div>
         </div>
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
           var contactId = $(this).attr('data-contact-id');
           var url = "{{ url('/delete-contact') }}";
           url += '/' + contactId;
   
           $('<form action="' + url + '" method="GET">' +
               '<input type="hidden" name="_token" value="{{ csrf_token() }}"/>' +
               '<input type="hidden" name="_method" value="DELETE"/>' +
               '</form>').appendTo($(document.body)).submit();
       }
       return false;
   });
</script>
<script type="text/javascript">
   $(document).ready(function () {
       $('#contactList').DataTable({
           //"paging": false
           "order": []
       });
   });
</script>