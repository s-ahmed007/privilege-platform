</div>
</div>
<!-- jQuery -->
<script src="{{ asset('admin/vendors/jquery/dist/jquery.min.js') }}"></script>
<!-- Bootstrap -->
<script src="{{ asset('admin/vendors/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<!-- FastClick -->
<script src="{{ asset('admin/vendors/fastclick/lib/fastclick.js') }}"></script>
<!-- NProgress -->
<script src="{{ asset('admin/vendors/nprogress/nprogress.js') }}"></script>
<!-- Chart.js -->
<script src="{{ asset('admin/vendors/Chart.js/dist/Chart.min.js') }}"></script>
<!-- gauge.js -->
<script src="{{ asset('admin/vendors/gauge.js/dist/gauge.min.js') }}"></script>
<!-- bootstrap-progressbar -->
<script src="{{ asset('admin/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js') }}"></script>
<!-- iCheck -->
<script src="{{ asset('admin/vendors/iCheck/icheck.min.js') }}"></script>
<!-- Skycons -->
<script src="{{ asset('admin/vendors/skycons/skycons.js') }}"></script>
<!-- Flot -->
<script src="{{ asset('admin/vendors/Flot/jquery.flot.js') }}"></script>
<script src="{{ asset('admin/vendors/Flot/jquery.flot.pie.js') }}"></script>
<script src="{{ asset('admin/vendors/Flot/jquery.flot.time.js') }}"></script>
<script src="{{ asset('admin/vendors/Flot/jquery.flot.stack.js') }}"></script>
<script src="{{ asset('admin/vendors/Flot/jquery.flot.resize.js') }}"></script>

<!-- DateJS -->
<script src="{{ asset('admin/vendors/DateJS/build/date.js') }}"></script>
<!-- JQVMap -->
<script src="{{ asset('admin/vendors/jqvmap/dist/jquery.vmap.js') }}"></script>
<script src="{{ asset('admin/vendors/jqvmap/dist/maps/jquery.vmap.world.js') }}"></script>
<script src="{{ asset('admin/vendors/jqvmap/examples/js/jquery.vmap.sampledata.js') }}"></script>
<!-- bootstrap-daterangepicker -->
<script src="{{ asset('admin/vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>

<!-- Custom Theme Scripts -->
<script src="{{ asset('admin/build/js/custom.min.js') }}"></script>
{{--====================================================--}}
{{--javascript to save gallery caption--}}
{{--========================================================--}}
<script>
    $(document).on('click', '#save-gallery-caption', function () {
        var image_id = this.value;
        var caption = $('#add-gallery-caption-' + image_id).val();
        var url = "{{ url('/addGalleryCaption') }}";
        $.ajax({
            type: "POST",
            url: url,
            data: {'_token': '<?php echo csrf_token(); ?>', 'id' : image_id, 'caption': caption},
            success: function (data) {
                if(data[0] == 'updated'){
                    $("#captionUpdated").modal('toggle');
                    $("#caption-updated").html('Caption updated successfully');
                    $("#caption-updated").css({'color': 'green'});
                }else{
                    $("#captionUpdated").modal('toggle');
                    $("#caption-updated").html('Something went wrong');
                    $("#caption-updated").css({'color': 'red'});
                }
            }
        });
    });

</script>

{{--====================================================--}}
{{--javascript to limit Menu Images--}}
{{--========================================================--}}
<script>
$('input#file-4c').change(function(){
	var count_prev_img = $('#img_count').val();
	var limit_remaining = 20 - count_prev_img;
    var files = $(this)[0].files;
    if(files.length > limit_remaining){
        $('#menu_image_names').html('');
        alert("you have exceeded your limit!");
        $('input#file-4c').val('');
    }else{
        
    }
});
</script>

{{--====================================================--}}
{{--javascript to limit Gallery Images--}}
{{--========================================================--}}
<script>
$('input#file-3c').change(function(){
	var count_prev_img = $('#img_count').val();
	var limit_remaining = 20 - count_prev_img;
    var files = $(this)[0].files;
    if(files.length > limit_remaining){
        $('#gallery_image_names').html('');
        alert("you have exceeded your limit!");
        $('input#file-3c').val('');
    }else{
        
    }
});
</script>
</body>
</html>