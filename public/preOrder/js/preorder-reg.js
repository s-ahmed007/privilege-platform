$(function () {
    var date1  = new Date();
    var dd = date1.getDate();
    var mm = date1.getMonth()+1;
    var yyyy = date1.getFullYear();
    var compareDate1 = new Date(yyyy,mm,dd);
    compareDate1.setHours(0,0,0,0);
    var todayDate = mm + "/" + dd + "/" + yyyy;

    var myDate = new Date();
    var date = myDate.getDate()+1;
    var month = myDate.getMonth()+1;
    var year = myDate.getFullYear();
    var tommorow = month + "/" + date  + "/" + year;
    $('#datetimepicker').datetimepicker({
        format: 'DD-MM-YYYY',
        maxDate: today,
        disabledDates: [tommorow]
    });
});

