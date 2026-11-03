@extends('layout.email')
@section('content')
<table cellpadding="0" cellspacing="0" role="presentation" width="100%">
    <tbody>
        <tr>
            <td
                style="background: #f36f21; padding: 30px; border-top-right-radius: 16px; border-top-left-radius: 16px;">
                <h1
                    style="text-align:center; font-family:'Red Hat Display', sans-serif; font-size:24px; font-weight: 500; line-height: 32px; color: #fff; padding-bottom: 20px;">
                    Hi {{ $name }}</h1>
                <p style="
                        color: #fff;
                        font-family: 'Red Hat Display', sans-serif;
                        font-size: 16px;
                        font-style: normal;
                        font-weight: 400;
                        line-height: 21.5px;
                        text-align: center;
                      "> There was recently a request to change the password for your account. If you requested this
                    change, set a new password using this OTP</p>
            </td>
        </tr>
        <tr>
            <td
                style="background-color: #faffe1; padding: 32px; border-bottom-right-radius: 16px; border-bottom-left-radius: 16px;">
                <p style="
                      color: #1b1d1f;
                      text-align: center;
                      font-family: 'Red Hat Display', sans-serif;
                      font-size: 32px;
                      font-style: normal;
                      font-weight: 300;
                      line-height: 30px;
                      letter-spacing: 23.36px;
                      padding-bottom: 16px;
                    "> {{ $otp }} </p>
                <hr style="max-width: 304px; margin: auto" color="ECF2CE" />
                <p style="
                      text-align: center;
                      font-family: 'Inter', sans-serif;
                      font-size: 14px;
                      font-style: normal;
                      font-weight: 400;
                      line-height: 24px;
                      padding-top: 16px;
                      padding-inline: 10px;
                    "> If this email was not intended for you, kindly disregard it
                    or reach out to our <br />
                    customer support at admin@nhpc.gov.np for further
                    assistance. </p>
                <br />
            </td>
        </tr>
    </tbody>
</table>
@endsection
