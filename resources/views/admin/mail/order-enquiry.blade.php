<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Enquiry</title>
</head>

<body style="background:#fff; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:25px;">
<table border="0" cellspacing="0" cellpadding="0" style="background:#eaeaea; max-width:800px; width:100%;padding:0px 15px;" align="center">
  <tr>
    <td><table align="center" border="0" cellspacing="0" cellpadding="0" style="margin-bottom: 35px; max-width:700px; width:100%">
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding:40px 0 20px;">
        <tr>
         <td> <img src="{{ $company->logo }}" alt="logo" style="display:block; border:0;"></td>
          <!-- <td><img src="https://www.trtcle.com/user_assets/images/logo.png" alt="" style="display:block; border:0;"></td> -->
          <!-- <td style="text-align: right; font-weight: bold; color: #666666; font-size: 120%;"><a style="text-decoration:none;color:#666;" href="tel:+18006726253">+1-800-672-6253</a></td> -->
        </tr>
      </table></td>
  </tr>
  <tr>
    <td bgcolor="#ffffff" align="left">
    	<table width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width:700px;">
        <tr>
          <td style=" background-image:url({{  asset('assets/web/images/header-bg.png') }}; min-height: 185px; color: #ffffff; text-align: left; font-size: 26px; line-height: 38px; background-size: 100% auto; background-repeat: no-repeat; padding:7% 0; background-color:#667ab8;"><p style="margin:0; color:#ffffff;">Welcome to Orchid, </p></td>
        </tr>
        <tr>
          <td><table align="left" width="100%" border="0" cellspacing="0" cellpadding="0" style="">
              <tr>
                <td>Hello {{ $company->user_type }},<br/> Please find order info details as below:
                  <br/>
                </td>
              </tr>
              <tr>

                <table style="width:100%"  border="1" cellspacing="0" cellpadding="0" style="padding:40px 0 20px;" align="left">
                  <tr>
                    <th style="padding:5px">Order Id</th>
                    <th style="padding:5px">Product Name</th>
                    <th style="padding:5px">Quantity</th>
                    <th style="padding:5px">Delivery Date</th>
                    <th style="padding:5px">Comment</th>

                  </tr>
                
                  @if(!empty($orderinfo) && count($orderinfo)>0)
                    @foreach($orderinfo as $key=>$info)
                      @if($key!=='product_name')
                      <tr>
                        <td style="padding:5px">{{ $info['order_number'] }} </td>
                        <td style="padding:5px">{{ $orderinfo['product_name'][$key] }} </td>
                        <td style="padding:5px">{{ $info['quantity'] }} </td>
                        <td style="padding:5px">{{ $info['delivery_date'] }} </td>
                        <td style="padding:5px">{{ $info['comment'] }} </td>
                      </tr>
                      @endif
                    @endforeach
                  @endif
                 </table>
                 
              </tr>
              <tr>
                
              <tr>
                <td style="padding-top:5%;">
                  Regards,<br>

           {{ $company->name??"" }} <br>
          <a target="_blank" href="{{  $company->url }}">{{  $company->url }}</a><br>
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
