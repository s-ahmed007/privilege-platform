<script type="text/javascript">
    function showLocationModal(param) {
        var url = "{{ url('/partner-locations-for-modal') }}";
        $.ajax({
            type: "POST",
            url: url,
            data: {
                '_token': '<?php echo csrf_token(); ?>',
                'partner_id' : param
            },
            success: function (data) {
                $(".partner-name-in-modal").text(data['name']);//set modal title
                $("#branch_list").html(data['locations']);//set modal body
                $('#profile-modal').modal('toggle');//show modal
            }
        });
    }

    function ResetFilter() {
        if(localStorage.getItem("sorted_object") != null){//sort with prev sorted list if exists
            $('.page_loader').fadeIn();//show loading gif
            // var discount = null;
            var division = null;
            var area = null;
            var attribute = [];
            var cat_rel_id = [];

            localStorage.removeItem('my_attributes');
            localStorage.removeItem('my_sub_cat_1');

            var sorted_object = localStorage.getItem("sorted_object");
            var parsedObject = JSON.parse(sorted_object);
            sortedPartnerList(division, area, attribute, cat_rel_id, parsedObject);
        }
    }

    $('#filter-discount').change(function(){
        $('.page_loader').fadeIn();//show loading gif
        var discount = $(this).val();
        var division = $("#filter-division").val();
        var area = $("#filter-area").val();
        //check if attribute is selected or not
        if(localStorage.getItem("my_attributes") != null){
            var my_attribute_arr = JSON.parse(localStorage.getItem("my_attributes"));
        }else{
            var my_attribute_arr = [];
        }
        //check if sub cat 1 is selected or not
        if(localStorage.getItem("my_sub_cat_1") != null){
            var my_sub_cat_1_arr = JSON.parse(localStorage.getItem("my_sub_cat_1"));
        }else{
            var my_sub_cat_1_arr = [];
        }
        if(localStorage.getItem("sorted_object") != null){//sort with prev sorted list if exists
            var sorted_object = localStorage.getItem("sorted_object");
            var parsedObject = JSON.parse(sorted_object);
            sortedPartnerList(discount, division, area, my_attribute_arr, my_sub_cat_1_arr, parsedObject);
            return false;
        }

        var url = "{{ url('/sort-offers') }}";
        $.ajax({
            type: "POST",
            url: url,
            data: {
                '_token': '<?php echo csrf_token(); ?>',
                'category' : '<?php echo isset($selected_category) ? $selected_category : ''; ?>'
            },
            success: function (data) {
                //append filtered partners in view page
                localStorage.setItem("sorted_object", JSON.stringify(data));
                sortedPartnerList(discount, division, area, my_attribute_arr, my_sub_cat_1_arr, data);
            }
        });
    });

    $('#filter-division').change(function(){
        $('.page_loader').fadeIn();//show loading gif
        // var discount = $("#filter-discount").val();
        var division = $(this).val();
        var area = $("#filter-area").val();
        //sort division wise area
        var url = "{{ url('/get-division-wise-area') }}";
        $.ajax({
            type: "POST",
            url: url,
            data: {
                '_token': '<?php echo csrf_token(); ?>',
                'division' : division
            },
            success: function (data) {
                var output = '';
                output += "<option disabled selected>&nbsp;&nbsp;&nbsp;Area</option>\n";
                $.each(data, function(index, value) {
                    output += "<option value=\""+value['area_name']+"\">&nbsp;&nbsp;&nbsp;"+value['area_name']+
                        "</option>";
                });
                $('#filter-area').empty().html(output);
            }
        });
        //check if attribute is selected or not
        if(localStorage.getItem("my_attributes") != null){
            var my_attribute_arr = JSON.parse(localStorage.getItem("my_attributes"));
        }else{
            var my_attribute_arr = [];
        }
        //check if sub cat 1 is selected or not
        if(localStorage.getItem("my_sub_cat_1") != null){
            var my_sub_cat_1_arr = JSON.parse(localStorage.getItem("my_sub_cat_1"));
        }else{
            var my_sub_cat_1_arr = [];
        }
        if(localStorage.getItem("sorted_object") != null){//sort with prev sorted list if exists
            var sorted_object = localStorage.getItem("sorted_object");
            var parsedObject = JSON.parse(sorted_object);
            sortedPartnerList(division, area, my_attribute_arr, my_sub_cat_1_arr, parsedObject);
            return false;
        }
        var url = "{{ url('/sort-offers') }}";
        $.ajax({
            type: "POST",
            url: url,
            data: {
                '_token': '<?php echo csrf_token(); ?>',
                'category' : '<?php echo isset($selected_category) ? $selected_category : ''; ?>'
            },
            success: function (data) {
                //append filtered partners in view page
                localStorage.setItem("sorted_object", JSON.stringify(data));
                sortedPartnerList(division, area, my_attribute_arr, my_sub_cat_1_arr, data);
            }
        });
    });

    $('#filter-area').change(function(){
        $('.page_loader').fadeIn();//show loading gif
        // var discount = $("#filter-discount").val();
        var division = $("#filter-division").val();
        var area = $(this).val();
        //check if attribute is selected or not
        if(localStorage.getItem("my_attributes") != null){
            var my_attribute_arr = JSON.parse(localStorage.getItem("my_attributes"));
        }else{
            var my_attribute_arr = [];
        }
        //check if sub cat 1 is selected or not
        if(localStorage.getItem("my_sub_cat_1") != null){
            var my_sub_cat_1_arr = JSON.parse(localStorage.getItem("my_sub_cat_1"));
        }else{
            var my_sub_cat_1_arr = [];
        }
        if(localStorage.getItem("sorted_object") != null){//sort with prev sorted list if exists
            var sorted_object = localStorage.getItem("sorted_object");
            var parsedObject = JSON.parse(sorted_object);
            sortedPartnerList(division, area, my_attribute_arr, my_sub_cat_1_arr, parsedObject);
            return false;
        }
        var url = "{{ url('/sort-offers') }}";
        $.ajax({
            type: "POST",
            url: url,
            data: {
                '_token': '<?php echo csrf_token(); ?>',
                'category' : '<?php echo isset($selected_category) ? $selected_category : ''; ?>'
            },
            success: function (data) {
                //append filtered partners in view page
                localStorage.setItem("sorted_object", JSON.stringify(data));
                sortedPartnerList(division, area, my_attribute_arr, my_sub_cat_1_arr, data);
            }
        });
    });

    function filterAttribute(attribute){
        $('.page_loader').fadeIn();//show loading gif
        // var discount = $("#filter-discount").val();
        var division = $("#filter-division").val();
        var area = $("#filter-area").val();
        //check if attribute is selected or not
        if(localStorage.getItem("my_attributes") == null){//initialize new array if doesn't exist
            // $('.'+attribute).css({'color': '#fff', 'background-color': '#283965', 'font-weight': '500'});
            var my_attribute_arr = [];
            my_attribute_arr.push(attribute);
            localStorage.setItem("my_attributes",  JSON.stringify(my_attribute_arr));
        }else{//work with previously initialized array from local storage
            var my_attribute_arr = JSON.parse(localStorage.getItem("my_attributes"));
            if(my_attribute_arr.includes(attribute)==false){//if element doesn't exist then push to array
                // $('.'+attribute).css({'color': '#fff', 'background-color': '#283965', 'font-weight': '500'});
                my_attribute_arr.push(attribute);
                localStorage.setItem("my_attributes", JSON.stringify(my_attribute_arr));
            }else{//if element exists then remove from array
                // $('.'+attribute).css({'color': '', 'background-color': '', 'font-weight': ''});
                var index = my_attribute_arr.indexOf(attribute);
                if (index > -1) {
                    my_attribute_arr.splice(index, 1);
                    localStorage.setItem("my_attributes", JSON.stringify(my_attribute_arr));
                }
            }
        }
        //check if sub cat 1 is selected or not
        if(localStorage.getItem("my_sub_cat_1") != null){
            var my_sub_cat_1_arr = JSON.parse(localStorage.getItem("my_sub_cat_1"));
        }else{
            var my_sub_cat_1_arr = [];
        }
        //check if all partner object is created or not
        if(localStorage.getItem("sorted_object") != null){//sort with prev sorted list if exists
            var sorted_object = localStorage.getItem("sorted_object");
            var parsedObject = JSON.parse(sorted_object);
            sortedPartnerList(division, area, my_attribute_arr, my_sub_cat_1_arr, parsedObject);
            return false;
        }

        var url = "{{ url('/sort-offers') }}";

        $.ajax({
            type: "POST",
            url: url,
            data: {
                '_token': '<?php echo csrf_token(); ?>',
                'category' : '<?php echo isset($selected_category) ? $selected_category : ''; ?>'
            },
            success: function (data) {
                //append filtered partners in view page
                localStorage.setItem("sorted_object", JSON.stringify(data));
                sortedPartnerList(division, area, my_attribute_arr, my_sub_cat_1_arr, data);
            }
        });
    }

    function filterSubcategory(cat_rel_id){
        $('.page_loader').fadeIn();//show loading gif
        // var discount = $("#filter-discount").val();
        var division = $("#filter-division").val();
        var area = $("#filter-area").val();
        cat_rel_id = Number(cat_rel_id);
        var sub_cat_class = 'cat_rel_id_'+cat_rel_id;//make class to show selected

        //check if attribute is selected or not
        var my_attribute_arr = [];
        if(localStorage.getItem("my_attributes") != null){
            my_attribute_arr = JSON.parse(localStorage.getItem("my_attributes"));
        }

        //check if sub cat 1 is selected or not
        if(localStorage.getItem("my_sub_cat_1") == null){//initialize new array if doesn't exist
            $('.'+sub_cat_class).css({'color': '#fff', 'background-color': '#283965', 'font-weight': '500'});

            var my_sub_cat_1_arr = [];
            my_sub_cat_1_arr.push(cat_rel_id);
            localStorage.setItem("my_sub_cat_1", JSON.stringify(my_sub_cat_1_arr));
        }else{//work with previously initialized array from local storage
            var my_sub_cat_1_arr = JSON.parse(localStorage.getItem("my_sub_cat_1"));
            if(my_sub_cat_1_arr.includes(cat_rel_id)==false){//if element doesn't exist then push to array
                $('.'+sub_cat_class).css({'color': '#fff', 'background-color': '#283965', 'font-weight': '500'});
                my_sub_cat_1_arr.push(cat_rel_id);
                localStorage.setItem("my_sub_cat_1", JSON.stringify(my_sub_cat_1_arr));
            }else{//if element exists then remove from array
                $('.'+sub_cat_class).css({'color': '', 'background-color': '', 'font-weight': ''});
                var index = my_sub_cat_1_arr.indexOf(cat_rel_id);
                if (index > -1) {
                    my_sub_cat_1_arr.splice(index, 1);
                    localStorage.setItem("my_sub_cat_1", JSON.stringify(my_sub_cat_1_arr));
                }
            }
        }

        //check if all partner object is created or not
        if(localStorage.getItem("sorted_object") != null){//sort with prev sorted list if exists
            var sorted_object = localStorage.getItem("sorted_object");
            var parsedObject = JSON.parse(sorted_object);
            sortedPartnerList(division, area, my_attribute_arr, my_sub_cat_1_arr, parsedObject);
            return false;
        }

        var url = "{{ url('/sort-offers') }}";

        $.ajax({
            type: "POST",
            url: url,
            data: {
                '_token': '<?php echo csrf_token(); ?>',
                'category' : '<?php echo isset($selected_category) ? $selected_category : ''; ?>'
            },
            success: function (data) {
                //append filtered partners in view page
                localStorage.setItem("sorted_object", JSON.stringify(data));
                sortedPartnerList(division, area, my_attribute_arr, my_sub_cat_1_arr, data);
            }
        });
    }

    function sortedPartnerList(division, area, attribute, cat_rel_id, object) {
        //sort discount wise
        // if (discount != null) {
        //     if (discount === 'lh') {
        //         //sorting array according to discount (low to high)
        //         object.sort(function (a, b) {
        //             return a['discount'][0]['discount_percentage'] - b['discount'][0]['discount_percentage']
        //         });
        //     } else {
        //         //sorting array according to discount (high to low)
        //         object.sort(function (a, b) {
        //             return b['discount'][0]['discount_percentage'] - a['discount'][0]['discount_percentage']
        //         });
        //     }
        // }
        //sort division wise
        if(division != null){
            var division_result = [];
            var k = 0;
            for (var i=0; i < object.length; i++){
                for (var j=0; j < object[i]['branches'].length; j++){
                    if(object[i]['branches'][j]['partner_division'] === division){
                        division_result[k] = object[i];
                        k++;
                        break;
                    }
                }
            }
            object = division_result;
        }
        //sort area wise
        if(area != null){
            var area_result = [];
            var k = 0;
            for (var i=0; i < object.length; i++){
                for (var j=0; j < object[i]['branches'].length; j++){
                    if(object[i]['branches'][j]['partner_area'] === area){
                        area_result[k] = object[i];
                        k++;
                    }
                }
            }
            object = area_result;
        }
        //sort facilities wise
        if(attribute.length !== 0){
            var attribute_result = [];
            var object_length = object.length;
            var r = 0;
            for (var m=0; m < object_length; m++){
                $.each(object[m]['branches'], function(index, value) {
                    if (value.facilities != null) {
                        var attr_length = attribute.length;
                        var attr_match = 0;
                        for (var y=0; y < attr_length; y++) {
                            if (value.facilities.includes(attribute[y])==true) {
                                attr_match++;
                            }
                        }
                        if(attr_length === attr_match){
                            attribute_result[r] = object[m];
                            r++;
                            return false;
                        }
                    }
                });
            }
            object = attribute_result;
        }
        //sort subcategory wise
        if(cat_rel_id.length !== 0){
            var sub_cat_1_result = [];
            var k = 0;
            for (var i=0; i < object.length; i++){
                for (var j=0; j < object[i]['partner_category_relation'].length; j++){
                    var sub_cat_1_length = cat_rel_id.length;
                    for(var m=0; m<sub_cat_1_length; m++){
                        if(object[i]['partner_category_relation'][j]['cat_rel_id'] === cat_rel_id[m]){//for "AND" condition
                            sub_cat_1_result[k] = object[i];
                            k++;
                            break;
                        }
                    }
                }
            }
            object = sub_cat_1_result;
        }
        //remove duplicate partner
        object = checkDuplicateInObject(object);
        var offer_div = $('#offers');
        var output = '';
        if (object.length === 0) {
            output += "<div class=\"col-md-12\">";
            output += "<h4 style=\"text-align: center; color: #007bff;\">Sorry! No match found.</h4>";
            output += "</div>";
            offer_div.empty().html(output);
            $('.page_loader').fadeOut();//hide loading gif
        } else {
            for (var i = 0; i < object.length; i++) {
                var pname = object[i].partner_name.replace("'", "");
                output += "<div class=\"col-sm-6 col-md-4 col-lg-4 mt-4\">";

                if (object[i].branches.length === 1) {
                    output += "<a href=\"/partner-profile/" + pname + "/" + object[i].branches[0].id + "\">";
                } else {
                    output += "<div onclick=\"showLocationModal( '" + object[i].partner_account_id
                        + "' )\" style=\"cursor: pointer\">";
                }
                output += "<div class=\"card card-inverse card-info\">";
                // if (object[i].featured == true) {

                // }
                var image = '';
                if(object[i].profile_image.partner_cover_photo != null){
                    image = object[i].profile_image.partner_cover_photo;
                }else{
                    image = "https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/offers/nobanner.png";
                }
                output += "<img src=\"" + image + "\" alt=\"royalty-partner-cover\" class=\"card-img-top\">";

                output += "<div class=\"card-block\">";

                output += "<h4 class=\"card-title card-partner-name\">" + object[i].partner_name + "</h4>";
                output += "<div class=\"card-text\">";
                output += "<p>";
                output += object[i].location + " - ";
                    <?php $ratings = [1,2,3,4,5]; ?>
                var ratings = ['1.00','2.00','3.00','4.00','5.00'];
                if(object[i].rating.average_rating == 0){
                    output += "<span class=\"partner-box-info-rating\">new</span>";
                }else if(ratings.includes(object[i].rating.average_rating)){
                    output += "<i class=\"bx bxs-star yellow\"></i>";
                    output += "<span class=\"partner-box-info-rating\">"+
                        Math.round(object[i].rating.average_rating*10)/10+ ".0</span>";
                }else{
                    output += "<i class=\"bx bxs-star yellow\"></i>";
                    output += "<span class=\"partner-box-info-rating\">"+
                        Math.round(object[i].rating.average_rating*10)/10+"</span>";
                }
                output += "</p>";
                // output += "<p class='card-partner-type'>" + object[i].partner_type + "</p>";
                output += "</div>";
                output += "</div>";

                output += "<div class=\"card-footer\">";
                output += "<label class=\"label-tag-small\">OFFER</label>";
                output += "<small class=\"bold black\">"+object[i].offer_heading+"</small>";
                output += "</div>";
                output += "</div>";
                output += object[i].branches.length === 1 ? "</a>" : "</div>";

                output += "</div>";
            }
            offer_div.empty().html(output);
            $('.page_loader').fadeOut();//hide loading gif
        }
    }

    function checkDuplicateInObject(inputArray) {
        var object = inputArray.map(JSON.stringify).reverse()
            .filter(function(item, index, inputArray){ return inputArray.indexOf(item, index + 1) === -1; })
            .reverse().map(JSON.parse);
        return object;
    }

    //remove specific local storage item if page is refreshed
    window.onbeforeunload = function() {
        localStorage.removeItem('my_attributes');
        localStorage.removeItem('my_sub_cat_1');
        localStorage.removeItem('sorted_object');
    };
    //unset localstorage value ends
    console.clear();
    document.getElementById("filterForm").reset();//reset all fields of filter on page refresh specially from back button
</script>