<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Decline</title>
</head>

<body style="background:#fff; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:25px;">
<table border="0" cellspacing="0" cellpadding="0" style="background:#eaeaea; max-width:800px; width:100%;padding:0px 15px;" align="center">
  <tr>
    <td><table align="center" border="0" cellspacing="0" cellpadding="0" style="margin-bottom: 35px; max-width:700px; width:100%">
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding:40px 0 20px;">
        <tr>
           <td> <img src="{{ $orderinfo->company->logo }}" alt="logo" style="display:block; border:0;"></td>
          <!-- <td><img src="https://www.trtcle.com/user_assets/images/logo.png" alt="" style="display:block; border:0;"></td> -->
          <!-- <td style="text-align: right; font-weight: bold; color: #666666; font-size: 120%;"><a style="text-decoration:none;color:#666;" href="tel:+18006726253">+1-800-672-6253</a></td> -->
        </tr>
      </table></td>
  </tr>
  <tr>
    <td bgcolor="#ffffff" align="center">
    	<table width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width:700px;">
        <tr>
          <td style=" background-image:url({{  asset('assets/web/images/header-bg.png') }}; min-height: 185px; color: #ffffff; text-align: center; font-size: 26px; line-height: 38px; background-size: 100% auto; background-repeat: no-repeat; padding:7% 0; background-color:#667ab8;"><p style="margin:0; color:#ffffff;"></p></td>
        </tr>
        <tr>
          <td><table align="center" width="100%" border="0" cellspacing="0" cellpadding="0" style="">
              <tr>
                <td>
                  <br/>
                  @if($orderinfo->mail_type=='user')
                    Hello {{ $orderinfo->contact_name }}, <br/><br/> Your Order No: {{ $orderinfo->order_number }} is Cancelled
                  @else
                    Hello Admin, <br/><br/> Order No: {{ $orderinfo->order_number }} is Cancelled
                  @endif
                </td>
              </tr>
              <tr>

              </tr>
              <tr>
               
              <tr>
                <td style="padding-top:5%;">
                  @if($orderinfo->mail_type=='user')
                  <p style="margin:0; color:#000000; text-align:justify;">Thank you for choosing us. We sincerely appreciate your business.
                  </p>
                  @endif
                  <br>
                  Regards,<br>

           {{ $orderinfo->company->name??"" }} <br>
          <a target="_blank" href="{{  $orderinfo->company->url??'javascript:void(0)' }}">{{  $orderinfo->company->url??"" }}</a><br>
              </td>
              </tr>

              
              <tr>
                <td style="padding-top:5%;"></td>
              </tr>  
            </table></td>
        </tr>
      </table></td>
  </tr>
  
</table>
</td>
</tr>
</table>
</body>
</html>
