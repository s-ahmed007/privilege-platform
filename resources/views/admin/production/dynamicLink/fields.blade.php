@php
use \App\Http\Controllers\Enum\DynamicLinkType;
@endphp
<div class="row">
    <div class="col-md-9">
        <div class="form-group">
            <label class="control-label col-md-4 col-sm-6 col-xs-12">Tag:<span style="color:red;font-size: 1.5em">*</span></label>                                    
            <div class="col-md-8 col-sm-6 col-xs-12">
                <select class="form-control" name="tag" required>
                    <option selected disabled>Select</option>
                    <option value="{{DynamicLinkType::HOMEPAGE_BANNER}}" {{isset($dynamicLink) && $dynamicLink->tag == DynamicLinkType::HOMEPAGE_BANNER ? 'selected' : ''}}>Homepage banner</option>
                    <option value="{{DynamicLinkType::APP_HOMEPAGE_POPUP}}" {{isset($dynamicLink) && $dynamicLink->tag == DynamicLinkType::APP_HOMEPAGE_POPUP ? 'selected' : ''}}>App homepage popup</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-md-4 col-sm-6 col-xs-12">Redirect Url:</label>
            <div class="col-md-8 col-sm-6 col-xs-12">
                <input type="text" class="form-control" name="redirect_url" value="{{$dynamicLink->values[0]['redirect_url'] ?? ''}}" placeholder="www.royaltybd.com">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-4 col-sm-6 col-xs-12">Active:</label>
            <div class="col-md-1 col-sm-1 col-xs-12">
                <input type="checkbox" class="form-control" name="active" value="1" {{ isset($dynamicLink) && $dynamicLink->values[0]['active'] == 1 ? 'checked' : ''}}>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-4 col-sm-6 col-xs-12">Image:<span style="color:red;font-size: 1.5em">{{isset($dynamicLink) ? '' : '*'}}</span></label>
            <div class="col-md-8 col-sm-6 col-xs-12">
                <input type="file" name="banner_image" {{isset($dynamicLink) ? '' : 'required'}}>
            </div>
        </div>                  
    </div>
    <div class="col-md-3"></div>
</div>