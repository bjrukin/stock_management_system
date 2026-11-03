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
                      "> After careful review, your submitted application has not been approved, This file has been sent back to you for careful review and update documents. Please upload below requested documents on time before deadline.<br>
                      सावधानीपूर्वक समीक्षा पछि, तपाइँको पेश गरिएको आवेदन स्वीकृत गरिएको छैन, यो फाइल सावधानीपूर्वक समीक्षा र कागजातहरू अद्यावधिक गर्नको लागि तपाइँलाई फिर्ता पठाइएको छ। कृपया तलको अनुरोध गरिएका कागजातहरू समय सीमा अघि अपलोड गर्नुहोस्।

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
                    "> The following remarks were noted by the application form operator:

                </p>
                <p style="
                      color: #1b1d1f;
                      font-family: 'Red Hat Display', sans-serif;
                      font-size: 14px;
                      font-style: normal;
                      line-height: 30px;
                      padding-bottom: 16px;
                    ">{{$remarks}}
                </p>
                <p style="
                      color: #1b1d1f;
                      font-family: 'Red Hat Display', sans-serif;
                      font-size: 14px;
                      font-style: normal;
                      line-height: 30px;
                      padding-bottom: 16px;
                    ">Please update your profile in a timely manner. If you have any questions or would like further
                    feedback, please feel free to contact us.

                </p>
            </td>
        </tr>
    </tbody>
</table>
@endsection
