<html>

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
</head>

<body style="background:#fff; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:25px;">
    <table border="0" cellspacing="0" cellpadding="0"
        style="background:#eaeaea; max-width:800px; width:100%; padding:0px 15px;" align="center">
<tr>
<td>
<table border="0" cellspacing="0" cellpadding="0" align="center"
style="margin-bottom: 35px;max-width:700px;width:95%">
<tr>
<td>
<table width="100%" border="0" cellspacing="0" cellpadding="0"
style="padding: 40px 0px 20px;">
<tr>
<td> <img src="{{ $user->company->logo }}" alt="logo" style="display:block; border:0;"></td>
</tr>
</table>
</td>
</tr>
<tr>
<td style="background:#fff;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td style="text-align: justify; padding: 5%">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td> Dear {{ $user->name }} <br /><br /> We have received a request to reset
                the password for the {{  $user->company->name??"" }} account associated with this email
                address. If you made this request, please follow the instructions
                below. <br />
                <br />
                Click the link below to reset your password using our secure server:
                <br />
                <a href="{{ $user->url }}">Reset Password</a>
                <br /><br>
                If clicking the above link does not seem to work, you can
                copy and paste the link into your browser\'s address window, or
                retype it there. Once you have returned to <a target="_blank" href="{{ url('/admin') }}">{{  url('/admin') }}</a>, we will give
                instructions for resetting your password.<br /> <br /> If you did
                not request to have your password reset, you can safely ignore this
                email. Rest assured that your account is secure. <br /> <br /> At
                {{  $user->company->name??"" }} we take the safety of your personal information seriously -
                we will never email you and ask you to disclose or verify your
                password or credit card number. If you receive a suspicious email
                requesting you update your account information, immediately report
                it to <a href="mailto:{{$user->company->adminmail}}">{{$user->company->adminmail}}</a><br />
                <br />
            </td>
            <tr>
                <td style="padding-top:5%;">
                  <p style="margin:0; color:#000000; text-align:justify;">Thank you for choosing us. We sincerely appreciate your business.
                  </p><br>
                  Regards,<br>

                   {{  $user->company->name??"" }} <br>
                    <a target="_blank" href="{{  $user->company->company_url }}">{{  $user->company->company_url }}</a><br>
                    </td>

                   
              </tr>
        </tr>
    </table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
    </table>
</body>

</html>