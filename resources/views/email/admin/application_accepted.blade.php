@extends('layout.email')
@section('content')
<table cellpadding="0" cellspacing="0" role="presentation" width="100%">
    <tbody>
        <tr>
            <td
                style="background: #f15a0e; padding: 30px; border-top-right-radius: 16px; border-top-left-radius: 16px;">
                <h1 style="
                        text-align: center;
                        font-family: 'Red Hat Display', sans-serif;
                        font-size: 24px;
                        font-weight: 500;
                        line-height: 32px;
                        color: #fff;
                        padding-bottom: 20px;
                      "> Dear {{ $name}}</h1>
                <p style="
                        color: #fff;
                        font-family: 'Red Hat Display', sans-serif;
                        font-size: 16px;
                        font-style: normal;
                        font-weight: 400;
                        line-height: 21.5px;
                      ">This email is to inform you that your application form has been reviewed and forwarded to Officer. Please login your profile in exam.nhpc.gov.np and check your status.<br>
                      तपाईले नाम दर्ता परीक्षाको लागि परिषदको अनलाईन माध्यम बाट दिनु भएको आवेदन फाराम चेकजाँच गर्दा परिषदले माग गरे अनुसारको आवश्यक कागजात ठिक भएकोले अर्को प्रकृयाको लागि अगाडी बढाईएको जानकारी गराउँदछौ साथै भहबm।लजउअ।नयख।लउ मा गएर तपाईले दिनु भएको आवेदनको प्रकृया बारे जानकारी पाउन सक्नु हुनेछ ।
                      <br> धन्यबाद
                </p>
            </td>
        </tr>
        <tr>
            <td
                style="background-color: #faffe1; padding: 32px; border-bottom-right-radius: 16px; border-bottom-left-radius: 16px;">
                <p style="
                      color: #1b1d1f;
                      font-family: 'Red Hat Display', sans-serif;
                      font-size: 14px;
                      font-style: normal;
                      line-height: 30px;
                      padding-bottom: 16px;
                    ">Thank you for your timely submission. If you have any further questions, feel free to reach out.<br>
                    तपाईंको समयमै फारम submission लागि धन्यवाद। यदि तपाइँसँग कुनै थप प्रश्नहरू छन् भने, सम्पर्क गर्न नहिचकिचाउनुहोस्।

                </p>
            </td>
        </tr>
    </tbody>
</table>
@endsection