@include('admin.production.header')
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Interested Partners</h3>
        </div>
        @if (Session::has('new partner deleted'))
            <div class="title_right alert alert-success"
                 style="text-align: center;">{{ Session::get('new partner deleted') }}</div>
        @elseif(session('try_again'))
            <div class="alert alert-warning"> {{ session('try_again') }} </div>
        @endif
    </div>
   <div class="col-md-12 col-xs-12">
      <div class="x_panel">
         <div class="title_left">
            <div class="clearfix"></div>
            <div class="container">
               <div class="row">
                  <div class="col-xs-12">
                     <div class="table-responsive">
                     @if($newPartners)
                        <table class="table table-bordered table-hover table-striped projects">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Info</th>
                                    <th>Address</th>
                                    <th>Owner</th>
                                    <th>Area</th>
                                    <th>Category</th>
                                    <th>Links</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($newPartners as $key => $partner)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{ $partner->business_name }}<br>
                                        {{ $partner->business_number }}<br>
                                        {{ $partner->business_email }}
                                    </td>
                                    <td>{{ $partner->business_address }}</td>
                                    <td>{{ $partner->full_name }}</td>
                                    <td>{{ $partner->business_area }}</td>
                                    <td>{{ $partner->business_category }}</td>
                                    <td>
                                        {{ wordwrap($partner->fb_link,15,"\n", true) }}<br>
                                        {{ wordwrap($partner->website,15,"\n", true) }}
                                    </td>
                                    <td>{{ date("F d, Y h:i A", strtotime($partner->date)) }}</td>
                                    <td>
                                        <a href="{{ url('delete-new-partner/'.$partner->id) }}">
                                            <button class="btn btn-delete" onclick="return confirm('Are you sure?')">Delete
                                            </button>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @else
                    <div style="font-size: 1.4em; color: red;">
                        {{ 'No request.' }}
                    </div>
                @endif
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

@include('admin.production.footer')