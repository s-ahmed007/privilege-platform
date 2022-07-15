@include('partner-dashboard.header')
<style>
    .img-40 {
        height: 40px;
        width: 40px;
    }
    ol, ul {
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .activity-feed .feed-item {
        position: relative;
        min-height: 60px;
        margin-bottom: 25px;
        /* padding-left: 30px; */
        /* border-left: 2px solid #ddd; */
    }
    .activity-feed .feed-item section {
        padding: 10px 15px;
        border-radius: 4px;
        border: 1px solid #f0f0f0;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }
    .notification_line{
        padding-left:10px
    }
</style>
<div class="container-fluid">
<div class="row bg-title">
        <div class="col-lg-5 col-md-4 col-sm-4 col-xs-12">
            <h3 class="d-inline-block">{{__('partner/notification.all_notifications')}}</h3>
                <h5 class="d-inline-block float-right">{{__('partner/notification.find_your_interactions')}}</h5>
        </div>
    </div>
    <div class="title_right">
    </div>
    <div class="row" style="background: white;">
        <div class="col-md-12 col-xs-12">
            <?php
            $notifications = (new \App\Http\Controllers\TransactionRequest\v2\functionController())
                ->getAllNotificationView($allNotifications);
            echo $notifications;
            ?>
        </div>
    </div>
</div>
@include('partner-dashboard.footer')