// dynamically load sub category after selecting  partner category
var base_url = window.location.origin;
var cat_rel_array = [];
$("#category_list").change(function () {
    var url = base_url + "/" + "admin/load_sub_cats";

    $.ajax({
        type: "POST",
        url: url,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
        },
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
            category_id: $(this).val()
        },
        success: function (data) {
            subCatView(data);
        }
    });
});

function subCatView(object) {
    var output = '';
    if (typeof object.first_obj.sub_cat_2 === 'undefined') {
        output += '<div class="row"><div class="col-md-6">';
        $.each(object.sub_cats, function(index, sub_cat_1) {
            output += '<div class="checkbox"><label>';
            output += '<input type="checkbox" class="flat" value="'+sub_cat_1.id+'" onclick="storeCatRelId(this.value)">'+sub_cat_1.cat_name;
            output += '</label>';
            output += '</div>';
        });
        output += '</div></div>';
    }else{
        output += '<div class="row">';        
        $.each(object.sub_cats, function(index, sub_cat_1) {
            output += '<div class="col-md-3">';
            output += '<span><br><b>'+sub_cat_1.cat_name+'<b></span>';
            $.each(sub_cat_1.sub_cat_2, function(index, sub_cat_2) {
                output += '<div class="checkbox"><label>';
                output += '<input type="checkbox" class="flat" value="'+sub_cat_2.id+'" onclick="storeCatRelId(this.value)">'+sub_cat_2.cat_name;
                output += '</label>';
                output += '</div>';
            });
            output += '</div>';
        });
        output += '</div>';
    }
    $("#partner_type").hide().html(output).fadeIn('slow');
}
function storeCatRelId(id) {
    cat_rel_array.push(id);
    $("#cat_rel_ids").val(JSON.stringify(cat_rel_array));
}
