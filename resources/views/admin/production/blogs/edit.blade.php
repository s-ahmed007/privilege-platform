@include('admin.production.header')
<script src="https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=37yoj87gdrindjk3ksaos96cpb8uwpwlf8nyk2rmrqa37n3v"></script>
<script>tinymce.init({selector: '#blogBody', plugins: "lists, advlist, image, link, media"});</script>

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <h3>Edit blog post</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <form class="form-horizontal form-label-left" method="post" action="{{ url('/admin/blog-post/'.$blog->id) }}"
                          enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="PUT"/>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Category:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <select class="form-control" name="blogCategory" id="category_list" required>
                                    <option selected disabled>Category</option>
                                    @foreach($all_categories as $category)
                                        <option value="{{$category->id}}" {{$blog->category_id == $category->id ? 'selected' : ''}}>
                                            {{$category->category}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Header:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                  <span style="color: red;">
                                    @if ($errors->getBag('default')->first('blogHeader'))
                                       {{ $errors->getBag('default')->first('blogHeader') }}
                                    @endif
                                  </span>
                                <input type="text" class="form-control" placeholder="Header" name="blogHeader"
                                       required="required" value="{{$blog->heading}}"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Body:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                  <span style="color: red;">
                                    @if ($errors->getBag('default')->first('blogBody'))
                                          {{ $errors->getBag('default')->first('blogBody') }}
                                      @endif
                                  </span>
                                <textarea id="blogBody" name="blogBody" value="{{old('blogBody')}}">{{$blog->details}}</textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Posted on:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                  <span style="color: red;">
                                      @if ($errors->getBag('default')->first('blogPostedOn'))
                                          {{ $errors->getBag('default')->first('blogPostedOn') }}
                                      @endif
                                  </span>
                                <?php
                                    $date = date_create($blog->posted_on);
                                    $time = date_format($date, "d-m-Y H:i:s");
                                ?>
                                <input type="text" class="form-control" placeholder="YYYY-MM-DD H:M:S" name="blogPostedOn" value="{{ $time }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-6 col-xs-12">Priority:</label>
                            <div class="col-md-9 col-sm-6 col-xs-12">
                                <input type="text" name="priority" class="form-control" value="{{$blog->priority}}"
                                   placeholder="(Ex: 100/200/300/...)" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Image:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="file" class="form-control" style="height: unset;" name="blogImage"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                                <button type="submit" class="btn btn-activate pull-right">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.production.footer')
