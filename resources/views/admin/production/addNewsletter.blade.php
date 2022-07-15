@include('admin.production.header')

{{--SMS-HUB - SEND NEWSLETTER--}}
<div class="right_col" role="main">
    <div class="col-md-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Subscribers Total: ({{ count($subscribed_mails) }}) People</h2>
                <div class="clearfix"></div>
                <h4>User Type: {{ $type }}</h4>
                <a class="btn btn-all" href="{{url('addNewsletter')}}">All</a>
                <a class="btn btn-premium" href="{{url('addNewsletter?type=card_user')}}">Royalty Premium Member</a>
                <a class="btn btn-guest" href="{{url('addNewsletter?type=guest')}}">Guest</a>
{{--                <a class="btn btn-nonuser" href="{{url('addNewsletter?type=non-user')}}">Subscribed (Home)</a>--}}
                <a class="btn btn-expired" href="{{url('addNewsletter?type=expired_trial')}}">Expired Trial</a>
                <a class="btn btn-expired" href="{{url('addNewsletter?type=expired_card_user')}}">Expired Members</a>
                <a class="btn btn-premium" href="{{url('addNewsletter?type=active')}}">Active</a>
                <a class="btn btn-premium" href="{{url('addNewsletter?type=inactive')}}">Inactive</a>
            </div>
            <div class="x_content">
                <div style="font-size: 15px;">
                    <?php $i = 1; ?>
                    <?php if (isset($subscribed_mails)) {
                        foreach ($subscribed_mails as $emails) {
                            echo $emails['email'];
                            if (count($subscribed_mails) != $i) {
                                echo '<br>';
                                $i++;
                            }
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.production.footer')