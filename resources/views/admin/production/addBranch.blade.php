@include('admin.production.header')

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Add Branch Information</h3>
        </div>
    </div>
    <div class="col-md-12 col-xs-12">
        <div class="x_panel">
            <div class="title_left">
                @if(session('try_again'))
                    <div class="title_right alert alert-warning"
                         style="text-align: center; float: right;"> {{ session('try_again') }} </div>
                @elseif(session('branch added'))
                    <div class="title_right alert alert-success"
                         style="text-align: center; float: right;"> {{ session('branch added') }} </div>
                @elseif(session('main branch exists'))
                    <div class="title_right alert alert-danger"
                         style="text-align: center; float: right;"> {{ session('main branch exists') }} </div>
                @elseif(session('partner_basic_info_added'))
                    <div class="title_right alert alert-warning"
                         style="text-align: center; float: right;"> {{ session('partner_basic_info_added') }} </div>
                @endif
            </div>
            <div class="clearfix"></div>
            <div class="panel panel-default">
               <div class="panel-body">
                  <form class="form-horizontal form-label-left" method="post" action="{{ url('store-branch') }}"
                      enctype="multipart/form-data">
                     <div class="row">
                        <div class="col-md-12">
                           <span style="color: #E74430;" class="error_admin_code">
                                @if ($errors->getBag('default')->first('admin_code'))
                                    {{ $errors->getBag('default')->first('admin_code') }}
                                @endif
                            </span>
                           <label class="control-label">Select Partner</label>
                           <select class="form-control" name="partner" id="select_partner">
                              <option value="{{$partner->partner_account_id}}">{{$partner->partner_name}}</option>
                           </select>
                        </div>
                     </div>
                     <!-- <div class="row">
                        <div class="col-sm-6">
                           <div class="form-group">
                              <label class="control-label">Username</label>
                               <span style="color: #E74430;">
                                @if ($errors->getBag('default')->first('username'))
                                    {{ $errors->getBag('default')->first('username') }}
                                @endif
                            </span>
                              <input type="text" class="form-control" placeholder="Username" name="username" value="{{old('username')}}">
                           </div>
                        </div>
                        <div class="col-sm-6">
                           <div class="form-group">
                              <label class="control-label">Password</label>
                              <span style="color: #E74430;">
                                @if ($errors->getBag('default')->first('password'))
                                    {{ $errors->getBag('default')->first('password') }}
                                @endif
                            </span>
                                <input type="text" class="form-control" placeholder="Password (0-9, A-Z, a-z), Minimum 8 characters"
                                       name="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                                       title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters">
                           </div>
                        </div>
                     </div> -->
                     <!-- Row -->
                     <div class="row">
                        <div class="col-sm-6">
                           <div class="form-group">
                              <label class="control-label">Email</label>
                              <span style="color: #E74430;" class="error_email">
                                @if ($errors->getBag('default')->first('email'))
                                    {{ $errors->getBag('default')->first('email') }}
                                @endif
                            </span>
                              <input type="email" class="form-control" placeholder="Email" name="email" id="email"
                                       value="{{old('email')}}">
                           </div>
                        </div>
                        <!-- Col -->
                        <div class="col-sm-6">
                           <div class="form-group">
                              <label class="control-label">Contact</label>
                              <span style="color: #E74430;" class="error_phone">
                                 @if ($errors->getBag('default')->first('contact'))
                                    {{ $errors->getBag('default')->first('contact') }}
                                @endif
                            </span>
                              @if(old('contact'))
                                    <input type="text" class="form-control" placeholder="Phone Number" id="branchContact"
                                       name="contact" value="{{old('contact')}}" maxlength="15">
                                @else
                                    <input type="text" class="form-control" id="branchContact" name="contact"
                                       placeholder="Contact Number " maxlength="15">
                                @endif
                           </div>
                        </div>
                     </div>
                     <!-- Row -->
                     <div class="row">
                        <div class="col-sm-12">
                           <div class="form-group">
                              <label class="control-label">Address</label>
                              <span style="color: #E74430;">
                                 @if ($errors->getBag('default')->first('address'))
                                    {{ $errors->getBag('default')->first('address') }}
                                @endif
                            </span>
                            <input type="text" class="form-control" placeholder="Address" name="address" value="{{old('address')}}">
                           </div>
                        </div>
                     </div>
                     <!-- Row -->
                     <div class="row">
                        <div class="col-sm-12">
                           <div class="form-group">
                              <label class="control-label">Map Location</label>
                              <span style="color: #E74430;">
                                @if ($errors->getBag('default')->first('location'))
                                    {{ $errors->getBag('default')->first('location') }}
                                @endif
                            </span>
                              <input type="text" class="form-control" placeholder="Location" name="location" value="{{old('location')}}">
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-sm-4">
                           <div class="form-group">
                              <label class="control-label">Latitude</label>
                              <span style="color: #E74430;" class="error_latitude">
                                @if ($errors->getBag('default')->first('latitude'))
                                    {{ $errors->getBag('default')->first('latitude') }}
                                @endif
                            </span>
                             <input type="text" class="form-control" placeholder="Latitude" id="latitude"
                                       name="latitude" value="{{old('latitude')}}">
                           </div>
                        </div>
                        <!-- Col -->
                        <div class="col-sm-4">
                           <div class="form-group">
                              <label class="control-label">Longitude</label>
                              <span style="color: #E74430;" class="error_longitude">
                                @if ($errors->getBag('default')->first('longitude'))
                                    {{ $errors->getBag('default')->first('longitude') }}
                                @endif
                            </span>
                              <input type="text" class="form-control" placeholder="Longitude" id="longitude"
                                       name="longitude" value="{{old('longitude')}}">
                           </div>
                        </div>
                        <!-- Col -->
                        <div class="col-sm-4">
                           <div class="form-group">
                              <label class="control-label">Zip</label>
                              <span style="color: #E74430;" class="error_zipCode">
                                @if ($errors->getBag('default')->first('zipCode'))
                                    {{ $errors->getBag('default')->first('zipCode') }}
                                @endif
                            </span>
                              <input type="text" class="form-control" placeholder="Zip Code" id="zipCode" name="zipCode">
                           </div>
                        </div>
                        <!-- Col -->
                     </div>
                     <!-- Row -->
                     <div class="row">
                        <div class="col-sm-6">
                           <div class="form-group">
                              <label class="control-label">Select Area</label>
                              <span style="color: #E74430;">
                                @if ($errors->getBag('default')->first('area'))
                                   {{ $errors->getBag('default')->first('area') }}
                               @endif
                            </span>
                              <select class="form-control" name="area">
                                    <option selected disabled>-----</option>
                                    @foreach($all_areas as $area)
                                        <option value="{{$area->area_name}}">{{$area->area_name}}</option>
                                    @endforeach
                                </select>
                           </div>
                        </div>
                        <!-- Col -->
                        <div class="col-sm-6">
                           <div class="form-group">
                               <span style="color: #E74430;">
                                @if ($errors->getBag('default')->first('division'))
                                   {{ $errors->getBag('default')->first('division') }}
                               @endif
                            </span>
                              <label class="control-label">Select Division</label>
                              <select class="form-control" name="division">
                                    <option selected disabled>-----</option>
                                    @foreach($all_divs as $division)
                                        <option value="{{$division->name}}">{{$division->name}}</option>
                                    @endforeach
                                </select>
                           </div>
                        </div>
                     </div>
                     <!-- Row -->
                     <div class="row">
                        <div class="col-sm-6">
                           <div class="form-group">
                              <label class="control-label">Opening Hours</label><br>
                              <p style="text-align: unset;">Format to follow:
                              <ul>
                              <li>
                              hh:mm aa - hh:mm aa (ex. 09:00 AM - 12:00 PM, 02:00 PM - 08:00 PM)
                              </li>
                              <li>
                              Closed
                              </li>
                              <li>
                              Always Open
                              </li>
                              </ul>
</p>
                              <label class="control-label">SAT</label>
                                <input type="text" class="form-control" placeholder="00:00 AM - 00:00 PM" name="sat"
                                       value="{{old('sat')}}">
                            <label class="control-label">SUN</label>
                           
                                <input type="text" class="form-control" placeholder="00:00 AM - 00:00 PM" name="sun"
                                       value="{{old('sun')}}">
                           
                          
                            <label class="control-label">MON</label>
                           
                                <input type="text" class="form-control" placeholder="00:00 AM - 00:00 PM" name="mon"
                                       value="{{old('mon')}}">
                     
                       
                            <label class="control-label">TUE</label>
                        
                                <input type="text" class="form-control" placeholder="00:00 AM - 00:00 PM" name="tues"
                                       value="{{old('tues')}}">
                     
                        
                            <label class="control-label">WED</label>
                        
                                <input type="text" class="form-control" placeholder="00:00 AM - 00:00 PM" name="wed"
                                       value="{{old('wed')}}">
                      
                          
                            <label class="control-label">THU</label>
                   
                                <input type="text" class="form-control" placeholder="00:00 AM - 00:00 PM" name="thu"
                                       value="{{old('thu')}}">
                        
                          
                            <label class="control-label">FRI</label>
                       
                                <input type="text" class="form-control" placeholder="00:00 AM - 00:00 PM" name="fri"
                                       value="{{old('fri')}}">
                        
                           </div>
                        </div>
                        <!-- Col -->
                        <div class="col-sm-6">
                           <div class="form-group">
                              <label class="control-label">Select The Facilities</label>
                              <div id="attributeArea">
                                @foreach($facilities as $facility)
                                  <div class="checkbox">
                                      <label>
                                          <input type="checkbox" class="flat" name="{{str_replace(' ', '_', $facility->name)}}">{{$facility->name}}
                                      </label>
                                  </div>
                                @endforeach
                                </div>
                           </div>
                        </div>
                     </div>
                        <div class="row">
                           <div class="col-sm-12">
                              <input type="hidden" name="_token" value="{{ csrf_token() }}">
                               <div class="form-group">
                        <div class="pull-right">
                            <button type="reset" class="btn btn-secondary">Reset</button>
                            <button type="submit" class="btn btn-activate pull-right">Submit</button>
                        </div>
                           </div>
                        </div>
                     </div>
                  </form>
               </div>
            </div>
        </div>
    </div>

    @include('admin.production.footer')

    <script>
        $('#file-fr').fileinput({
            language: 'fr',
            uploadUrl: '#',
            allowedFileExtensions: ['jpg', 'png', 'gif']
        });
        $('#file-es').fileinput({
            language: 'es',
            uploadUrl: '#',
            allowedFileExtensions: ['jpg', 'png', 'gif']
        });
        $("#file-0").fileinput({
            'allowedFileExtensions': ['jpg', 'png', 'gif']
        });
        $("#file-1").fileinput({
            uploadUrl: '#', // you must set a valid URL here else you will get an error
            allowedFileExtensions: ['jpg', 'png', 'gif'],
            overwriteInitial: false,
            maxFileSize: 1000,
            maxFilesNum: 10,
            //allowedFileTypes: ['image', 'video', 'flash'],
            slugCallback: function (filename) {
                return filename.replace('(', '_').replace(']', '_');
            }
        });

        $(document).ready(function () {
            $("#test-upload").fileinput({
                'showPreview': false,
                'allowedFileExtensions': ['jpg', 'png', 'gif'],
                'elErrorContainer': '#errorBlock'
            });
            $("#kv-explorer").fileinput({
                'theme': 'explorer',
                'uploadUrl': '#',
                overwriteInitial: false,
                initialPreviewAsData: true,
                initialPreview: [
                    "http://lorempixel.com/1920/1080/nature/1",
                    "http://lorempixel.com/1920/1080/nature/2",
                    "http://lorempixel.com/1920/1080/nature/3"
                ],
                initialPreviewConfig: [
                    {caption: "nature-1.jpg", size: 329892, width: "120px", url: "{$url}", key: 1},
                    {caption: "nature-2.jpg", size: 872378, width: "120px", url: "{$url}", key: 2},
                    {caption: "nature-3.jpg", size: 632762, width: "120px", url: "{$url}", key: 3}
                ]
            });
        });
    </script>