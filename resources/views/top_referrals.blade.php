@include('header')
<div class="container top-ref-container">
    <div class="banner_top_image_top-referrals">
    </div>
    <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/top-referrals/referrals_logo.png"
         class="lazyload refer_circle_img" alt="Royalty">
    <div class="refer_howitworks">
        <p style="margin-top: 20px;font-size: 1.8em;">Be amongst the top Royals</p>
        <p style="margin-top: 20px;font-size: 2em; color: #007bff;font-weight: bold;">Follow the steps</p>
        <div class="row" style="margin-top: 30px;">
            <div class="col-md-4 col-sm-4 col-xs-12">
                <aside id="info-block">
                    <section class="file-marker">
                        <div>
                            <div class="box-title">
                                <i class="bx bxs-star yellow"></i>
                            </div>
                            <div class="box-contents">
                                <div id="audit-trail">
                                    Get rewarded when your friends use your referral code to subscribe.
                                </div>
                            </div>
                        </div>
                    </section>
                </aside>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12">
                <aside id="info-block">
                    <section class="file-marker">
                        <div>
                            <div class="box-title">
                                <i class="bx bxs-star yellow"></i>
                            </div>
                            <div class="box-contents">
                                <div id="audit-trail">
                                    Stay active through likes and receive Credits even when you are exploring the
                                    offers.
                                </div>
                            </div>
                        </div>
                    </section>
                </aside>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12">
                <aside id="info-block">
                    <section class="file-marker">
                        <div>
                            <div class="box-title">
                                <i class="bx bxs-star yellow"></i>
                            </div>
                            <div class="box-contents">
                                <div id="audit-trail">
                                    Share your opinion by posting reviews and get instant bonus Credits.
                                </div>
                            </div>
                        </div>
                    </section>
                </aside>
            </div>
        </div>
        <div class="row" style="margin-top: 30px;">
            <div class="col-md-6">
                <p style="margin-top: 20px;font-size: 2em; color: #007bff;font-weight: bold;">REFERRALS</p><br>
                <div class="row">
                    <div class="col-md-6">
                        <div style="font-weight:900!important;">
                            <hr style="margin:4px 0;">
                            <span style="color:#DC143C"></span> Referrals
                            <hr style="margin:4px 0;">
                        </div>
                        <div>
                            Top the leader board by referring all your friends.
                            Earn up to 60 Royalty credit per referral.
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="font-weight:900!important;">
                            <hr style="margin:4px 0px;">
                            <span style="color:#DC143C">Royalty Premium Membership</span> Referrals
                            <hr style="margin:4px 0px;">
                        </div>
                        <div>
                            Always get a little extra with your Royalty Premium Membership.
                            Earn 100 bonus Credits per referral.
                        </div>
                    </div>
                </div>
                <p style="margin-top: 20px;font-size: 2em; color: #007bff;font-weight: bold;">TOP REFERRERS</p><br>
                <table style="border: 1px solid #007bff;border-collapse: collapse; width: 100%;">
                    <tr>
                        <th style="text-align: center">Rank</th>
                        <th style="text-align: center">Referrer's Photo ID</th>
                        <th style="text-align: center">Name</th>
                        <th style="text-align: center">Referral Status</th>
                    </tr>
                    <?php $i = 1;?>
                    <?php foreach ($topReferrer as $ref_row) {?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><img align="middle" alt="Royalty Referrer" style="margin:10px 0; height: 50px; width: 50px;"
                                 src="{{asset($ref_row->customer_profile_image)}}" class="img-circle"></td>
                        <td><?php echo $ref_row->customer_first_name . " " . $ref_row->customer_last_name;?></td>
                        <td>
                            <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/all/star.png" class="lazyload" alt="Royalty star">
                            <p>
                                <?php if ($ref_row->reference_used > 1) {
                                    echo $ref_row->reference_used . " Credits";
                                } else {
                                    echo $ref_row->reference_used . " Credit";
                                }?>
                            </p>
                        </td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
            <div class="col-md-6">
                <p style="margin-top: 20px;font-size: 2em; color: #007bff;font-weight: bold;">REVIEWERS</p><br>
                <div class="row">
                    <div class="col-md-6">
                        <div style="font-weight:900!important;">
                            <hr style="margin:4px 0px;">
                            <span style="color:#DC143C">Comments</span> Reviews
                            <hr style="margin:4px 0px;">
                        </div>
                        <div>
                            Post reviews to give feedback about our partners.
                            Earn 1 bonus Credits per review.
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="font-weight:900!important;">
                            <hr style="margin:4px 0px;">
                            <span style="color:#DC143C">Like</span> Reviews
                            <hr style="margin:4px 0px;">
                        </div>
                        <div>
                            Help our community to grow with likes and get rewarded.
                            Earn 1 bonus Credit per review.
                        </div>
                    </div>
                </div>
                <p style="margin-top: 20px;font-size: 2em; color: #007bff;font-weight: bold;">TOP REVIEWERS</p><br>
                <table style="border: 1px solid #007bff;border-collapse: collapse; width: 100%;">
                    <tr>
                        <th style="text-align: center">Rank</th>
                        <th style="text-align: center">Reviewer's Photo ID</th>
                        <th style="text-align: center">Name</th>
                        <th style="text-align: center">Reviewer's Status</th>
                    </tr>
                    <?php $i = 1;?>
                    <?php foreach ($allCustomers as $rev_row) {?>
                    <?php if ($rev_row['total'] != 0) {?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><img align="middle" alt="Royalty Reviewer" style="margin:10px 0; height: 50px; width: 50px;"
                                 src="{{asset($rev_row['customer_profile_image'] != '' ?
                                 $rev_row['customer_profile_image'] : 'images/user.png')}}" class="img-circle"></td>
                        <td><?php echo $rev_row['customer_first_name'] . " " . $rev_row['customer_last_name'];?></td>
                        <td>
                            <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/all/star.png" class="lazyload" alt="Royalty star">
                            <p>
                                <?php if ($rev_row['total'] > 1) {
                                    echo $rev_row['total'] . " Credits";
                                } else {
                                    echo $rev_row['total'] . " Credit";
                                }?>
                            </p>
                        </td>
                    </tr>
                    <?php }} ?>
                </table>
            </div>
        </div>
    </div>
</div>
@include('footer')