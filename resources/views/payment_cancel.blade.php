@include('header')
<style>td{text-align: -webkit-center}</style>

<link href="{{asset('css/donate-css/payment.css')}}" rel="stylesheet">
<section id="hero">
   <div class="container">
      <div class="section-title-hero" data-aos="fade-up">
         <h2>Royalty Premium Membership Purchase</h2>
         <p>Payment Cancelled!</p>
      </div>
   </div>
</section>
<section>
<body class="payment-body">
      <div style="background:#ffffff;background-color:#ffffff;margin:0px auto;border-radius:8px;max-width:600px;">
         <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background: #ffffff;background-color: #ffffff;width: 100%;border-radius: 8px;border-collapse: collapse;">
            <tbody>
            <tr>
                  <td style="direction: ltr;font-size: 0px;padding: 20px 0;text-align: center;vertical-align: top;border-collapse: collapse;">
                     <div class="mj-column-per-100 outlook-group-fix" style="font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                        <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align: top;border-collapse: collapse;" width="100%">
                           <tr>
                              <td align="center" style="font-size: 0px;padding: 10px 25px;border-collapse: collapse;">
                                 <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;border-spacing:0px;">
                                    <tbody>
                                       <tr>
                                          <td style="width: 130px;border-collapse: collapse;"> <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/all/payment-cancel.png" style="border: 0;display: block;outline: none;text-decoration: none;height: auto;width: 100%;line-height: 100%;" width="130" alt="fbd logo">
                                          </td>
                                       </tr>
                                    </tbody>
                                 </table>
                              </td>
                           </tr>
                        </table>
                     </div>
                  </td>
               </tr>
               <tr>
                  <td style="direction: ltr;font-size: 0px;padding: 0px;text-align: center;vertical-align: top;border-collapse: collapse;">
                     <div style="margin:0px auto;max-width:600px;">
                        <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width: 100%;border-collapse: collapse;">
                           <tbody>
                              <tr>
                                 <td style="direction: ltr;font-size: 0px;padding: 30px 30px 20px 30px;text-align: center;vertical-align: top;border-collapse: collapse;">
                                    <div class="mj-column-per-100 outlook-group-fix" style="font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                                       <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align: top;border-collapse: collapse;" width="100%">
                                          <tr>
                                             <td align="center" style="font-size: 0px;padding: 10px 25px;border-collapse: collapse;">
                                                <div class="payment-head"> Membership Purchase Payment Cancelled! </div>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td align="center" style="font-size: 0px;padding: 0px;border-collapse: collapse;">
                                                <div style="font-family:Lato, Helvetica, sans;font-size:15px;line-height:24px;text-align:center;color:#545465;">
                                                   <p class="payment-body-t">You have cancelled your payment for Royalty Membership Purchase.<br>If you have done it by mistake, then please try again.<br>
                                                      <span class="bottom-text">In case of any support, please contact us at <a href="mailto:support@royaltybd.com">
                                                      support@royaltybd.com
                                                      </a> or call us at
                                                      +880-963-862-0202.</span>
                                                   </p>
                                                   <div class="mj-column-per-100 outlook-group-fix" style="font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                                                      <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align: top;border-collapse: collapse;" width="100%">
                                                         <tr>
                                                            <td align="center" style="font-size: 0px;padding: 0;border-collapse: collapse;">
                                                               <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:separate;line-height:100%;border-spacing: 10px;">
                                                                 
                                                                  @if(isset($username))
                                                                  <tr>
                                                                  <td align="center" bgcolor="#586EE0" class="signuplogin-btn" valign="middle"> 
                                                                  <a href="{{url('users/'.$username->customer_username)}}" class="signuplogin">
                                                                        Go to your Account
                                                                        </a>
                                                                  </td>
                                                                  </tr>
                                                                  <tr>
                                                                  <td align="center" class="" valign="middle"> 
                                                                  <a href="{{url('select-card')}}" class="payment-try-text">
                                                                        I want to try again
                                                                        </a>
                                                                  </td>
                                                                  </tr>
                                                                  @else
                                                                  <tr>
                                                                     <td align="center" bgcolor="#586EE0" class="signuplogin-btn" valign="middle"> 
                                                                        <a href="{{url('login')}}" class="signuplogin">Login
                                                                        </a>
                                                                     </td>
                                                                     </tr>
                                                                     @endif
                                                               
                                                               </table>
                                                            </td>
                                                         </tr>
                                                      </table>
                                                   </div>
                                                </div>
                                             </td>
                                          </tr>
                                       </table>
                                    </div>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </div>
                     <div style="margin:0px auto;max-width:600px;">
                        <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width: 100%;border-collapse: collapse;">
                           <tbody>
                              <tr>
                                 <td style="direction: ltr;font-size: 0px;padding: 0 0 35px 0;text-align: center;vertical-align: top;border-collapse: collapse;">
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </div>
                  </td>
               </tr>
            </tbody>
         </table>
      </div>
</body>
</section>
@include('footer')