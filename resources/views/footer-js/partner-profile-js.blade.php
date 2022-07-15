{{--script to show individual offer details modal & open specific tab --}}
<script>
    $( document ).ready( function () {
        $(".offerDetails").click(function () {
            var offer_id = $(this).data("offer-id");
            var offer_tab = $(this).data("offer-tab");
            if(offer_tab === 'details'){
                $('a[href^="#tncTab'+offer_id+'"]').parent().removeClass('active');
                $('a[href^="#detailsTab'+offer_id+'"]').parent().addClass('active');
                $("#tncTab"+offer_id).removeClass('active');
                $("#detailsTab"+offer_id).addClass('active');
            }else if(offer_tab === 'tnc'){
                $('a[href^="#detailsTab'+offer_id+'"]').parent().removeClass('active');
                $('a[href^="#tncTab'+offer_id+'"]').parent().addClass('active');
                $("#detailsTab"+offer_id).removeClass('active');
                $("#tncTab"+offer_id).addClass('active');
            }
            $("#offerDetails_"+offer_id).modal('show');
        })
    });
</script>

{{--scrolling animation for side bar in partner profile--}}
<script>
$('a[href^="#gallery"]').on('click', function (e) {
        $(".nav-pills li").removeClass("active");
        $(this).parents('li').addClass('active');
        e.preventDefault();
        var target = $('#gallery');
        $('html, body').stop().animate({
            scrollTop: target.offset().top - 120
        }, 1000);
});
$('a[href^="#about"]').on('click', function (e) {
    $(".nav-pills li").removeClass("active");
    $(this).parents('li').addClass('active');
    e.preventDefault();
    var target = $('#about');
    $('html, body').stop().animate({
        scrollTop: target.offset().top - 120
    }, 1000);
});
$('a[href^="#timing"]').on('click', function (e) {
    $(".nav-pills li").removeClass("active");
    $(this).parents('li').addClass('active');
    e.preventDefault();
    var target = $('#timing');
    $('html, body').stop().animate({
        scrollTop: target.offset().top - 120
    }, 1000);
});
$('a[href^="#offers"]').on('click', function (e) {
    $(".nav-pills li").removeClass("active");
    $(this).parents('li').addClass('active');
    e.preventDefault();
    var target = $('#offers');
    $('html, body').stop().animate({
        scrollTop: target.offset().top - 120
    }, 1000);
});
$('a[href^="#terms"]').on('click', function (e) {
    $(".nav-pills li").removeClass("active");
    $(this).parents('li').addClass('active');
    e.preventDefault();
    var target = $('#terms');
    $('html, body').stop().animate({
        scrollTop: target.offset().top - 120
    }, 1000);
});
$('a[href^="#menu"]').on('click', function (e) {
    $(".nav-pills li").removeClass("active");
    $(this).parents('li').addClass('active');
    e.preventDefault();
    var target = $('#menu');
    $('html, body').stop().animate({
        scrollTop: target.offset().top - 120
    }, 1000);
});
$('a[href^="#reviews"]').on('click', function (e) {
    $(".nav-pills li").removeClass("active");
    $(this).parents('li').addClass('active');
    e.preventDefault();
    var target = $('#reviews');
    $('html, body').stop().animate({
        scrollTop: target.offset().top - 120
    }, 1000);
});
$('a[href^="#branches"]').on('click', function (e) {
    $(".nav-pills li").removeClass("active");
    $(this).parents('li').addClass('active');
    e.preventDefault();
    var target = $('#branches');
    $('html, body').stop().animate({
        scrollTop: target.offset().top - 120
    }, 1000);
});
$('a[href^="#nearbyPartners"]').on('click', function (e) {
    $(".nav-pills li").removeClass("active");
    $(this).parents('li').addClass('active');
    e.preventDefault();
    var target = $('#nearbyPartners');
    $('html, body').stop().animate({
        scrollTop: target.offset().top - 120
    }, 1000);
});
</script>

{{-- Sticky menu  start --}}
<script>
    $(window).scroll(function() {
        var scroll = $(window).scrollTop();
        if (scroll > 290) {
            $(".partner-profile-nav-box").addClass(" prof-nav-active");
            $(".partner-profile-navbar").css("border","0");
        }
        else {
            $(".partner-profile-nav-box").removeClass(" prof-nav-active");
            $(".partner-profile-navbar").css("border","1px solid #eeeeee");
        }
        $('.partner-profile-nav-box a').each(function () {
            var currLink = $(this);
            var refElement = $(currLink.attr("href"));
            if (refElement.position().top  <= scroll + 120) {
                $('.partner-profile-nav-box li').removeClass("active");
                currLink.parents('li').addClass('active');
            }
            else{
                currLink.parents('li').removeClass('active');
            }
        });
    });
</script>

<script>
    // Google Analytics
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-49610253-3', 'auto');
    ga('send', 'pageview');
</script>

{{-- Sticky menu  end --}}
