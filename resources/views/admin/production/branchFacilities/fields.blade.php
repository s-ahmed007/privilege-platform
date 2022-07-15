<div class="row">
    <div class="col-md-9">
        <div class="form-group">
            <label class="control-label col-md-4 col-sm-6 col-xs-12">Name:</label>
            <div class="col-md-8 col-sm-6 col-xs-12">
                <input type="text" class="form-control" name="name" value="{{$branchFacility->name ?? ''}}" placeholder="Ex: WiFi">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-4 col-sm-6 col-xs-12">Category:</label>
            <div class="col-md-8 col-sm-6 col-xs-12">
                @foreach($categories as $category)
                    <input type="checkbox" id="{{$category->type}}" name="{{$category->type}}" value="1"
                            {{ isset($branchFacility) && in_array($category->id, $branchFacility->category_ids) ? 'checked' : ''}}>
                    <label for="{{$category->type}}">{{$category->name}}</label><br>
                @endforeach
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-4 col-sm-6 col-xs-12">Icon:<span style="color:red;font-size: 1.5em">
                    {{isset($branchFacility) ? '' : '*'}}</span></label>
            <div class="col-md-8 col-sm-6 col-xs-12">
                <input type="file" name="icon" {{isset($branchFacility) ? '' : 'required'}}>
            </div>
        </div>
    </div>
    <div class="col-md-3"></div>
</div>