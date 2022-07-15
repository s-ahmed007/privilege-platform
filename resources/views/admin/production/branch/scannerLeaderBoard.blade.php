@include('admin.production.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>
<div class="right_col" role="main">
   <div class="page-title">
      <div class="title_left">
         @if (session('updated'))
         <div class="alert alert-success">
            {{ session('updated') }}
         </div>
         @elseif (session('user_deleted'))
         <div class="alert alert-danger">
            {{ session('user_deleted') }}
         </div>
         @elseif (session('created'))
         <div class="alert alert-success">
            {{ session('created') }}
         </div>
         @endif
         <h3>Partner Outlet Leaderboard</h3>
         <form class="form-horizontal form-label-left mtb-10" method="post" action="{{url('sort-scanner-leaderboard')}}">
            {{csrf_field()}}
            <div class="form-group" style="text-align: center;margin: 0 auto;">
               <div class="col-md-3 sort-head">Sort by Month/Year</div>
               <div class="col-md-3 sort-year">
                  <select class="form-control" name="year">
                     <option disabled selected>Year</option>
                     <?php
                        for ($i = 2018; $i <= date('Y'); $i++) {
                            $selected = $year == $i ? 'selected' : '';
                            echo "<option value='$i' $selected>$i</option>";
                        }
                        ?>
                  </select>
               </div>
               <div class="col-md-3 sort-month">
                  <select class="form-control" name="month">
                     <option disabled selected>Month</option>
                     <option value="01" {{$month == '01' ? 'selected' : ''}}>January</option>
                     <option value="02" {{$month == '02' ? 'selected' : ''}}>February</option>
                     <option value="03" {{$month == '03' ? 'selected' : ''}}>March</option>
                     <option value="04" {{$month == '04' ? 'selected' : ''}}>April</option>
                     <option value="05" {{$month == '05' ? 'selected' : ''}}>May</option>
                     <option value="06" {{$month == '06' ? 'selected' : ''}}>June</option>
                     <option value="07" {{$month == '07' ? 'selected' : ''}}>July</option>
                     <option value="08" {{$month == '08' ? 'selected' : ''}}>August</option>
                     <option value="09" {{$month == '09' ? 'selected' : ''}}>September</option>
                     <option value="10" {{$month == '10' ? 'selected' : ''}}>October</option>
                     <option value="11" {{$month == '11' ? 'selected' : ''}}>November</option>
                     <option value="12" {{$month == '12' ? 'selected' : ''}}>December</option>
                  </select>
               </div>
               <div class="col-md-3">
                  <button type="submit" class="form-control btn btn-primary" style="margin: 0">Sort</button>
               </div>
            </div>
         </form>
      </div>
   </div>
   <div class="clearfix"></div>
   <div class="container">
      <div class="row">
         <div class="col-xs-12">
            <div class="table-responsive">
               <table id="leaderBoard" class="table table-bordered table-hover table-striped projects">
                  <thead>
                     <tr>
                        <th>#</th>
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
                        <td>{{ $i }}</td>
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
                        <td>{{ $value['partner_name'] }}<br>{{ $value['area'] }}</td>
                        <td>{{ $value['point'] }}</td>
                     </tr>
                     <?php $i++; ?>
                     @endforeach
                     @endif
                  </tbody>
                  <tfoot>
                     <tr>
                     </tr>
                  </tfoot>
               </table>
            </div>
         </div>
      </div>
   </div>
</div>
@include('admin.production.footer')
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
   $(document).ready(function () {
       $('#leaderBoard').DataTable({
           //"paging": false
           "order": []
       });
   });
</script>