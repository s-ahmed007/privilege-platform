 $(document).ready(function () {
     "use strict";
     // toat popup js
     // $.toast({
     //     heading: 'Welcome to Ample admin',
     //     text: 'Use the predefined ones, or specify a custom position object.',
     //     position: 'top-right',
     //     loaderBg: '#fff',
     //     icon: 'warning',
     //     hideAfter: 3500,
     //     stack: 6
     // })


     //ct-visits
  //    new Chartist.Line('#ct-visits', {
  //        labels: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30'],
  //        series: [
  //   // [5, 2, 7, 4, 5, 3, 5, 4] ,
  //   [2, 5, 2, 6, 2, 5, 2, 4, 2, 7, 2, 6, 2, 5, 2, 4, 2, 1, 2, 6, 2, 8, 2, 4, 2, 5, 2, 6, 2, 8]
  // ]
  //    }, {
  //        top: 0,
  //        low: 1,
  //        showPoint: true,
  //        fullWidth: true,
  //        plugins: [
  //   Chartist.plugins.tooltip()
  // ],
  //        axisY: {
  //            labelInterpolationFnc: function (value) {
  //                return (value / 1) + 'k';
  //            }
  //        },
  //        showArea: true
  //    });
     // counter
     $(".counter").counterUp({
         delay: 100,
         time: 1200
     });
     var base_url = window.location.origin;
     var csrf_token = $('meta[name="csrf-token"]').attr("content");
     var sparklineLogin = function () {
         $.ajax({
             type: "POST",
             url: base_url + "/" + 'partner/branch/get_dashboard_data',
             async: false,
             headers: {
                 "X-CSRF-TOKEN": csrf_token
             },
             data: {
                 _token: csrf_token
             },
             success: function(data) {
                 $('#sparklinedash').sparkline(data.tran_values, {
                     type: 'bar',
                     height: '30',
                     barWidth: '4',
                     resize: true,
                     barSpacing: '5',
                     barColor: '#7ace4c'
                 });
                 $('#sparklinedash2').sparkline(data.visit_values, {
                     type: 'bar',
                     height: '30',
                     barWidth: '4',
                     resize: true,
                     barSpacing: '5',
                     barColor: '#1cc22b'
                 });
             }
         });
     }
     var sparkResize;
     $(window).on("resize", function (e) {
         clearTimeout(sparkResize);
         sparkResize = setTimeout(sparklineLogin, 500);
     });
     sparklineLogin();
 });
