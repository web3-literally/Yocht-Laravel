@extends('emails/layouts/emailTemplate')

@section('content')
    <table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
        <tbody>
        <tr>
            <td style="color:#141d23;font-family: 'Montserrat',Arial, sans-serif;font-size:18px;padding-right:30px;padding-left:30px;font-weight:500;letter-spacing:1px;line-height:30px;" data-bgcolor="Title" data-color="Title" data-size="Title" data-min="12" data-max="60">
                Hello, {{ $userName }}.<br>
                <br>
                <p>Your {{ config('app.name') }} Membership period ends at {{ $subscription->asBraintreeSubscription()->nextBillingDate->format('M j, Y') }}</p>
                <br>
                <p>This is a system notification.</p>
            </td>
        </tr>
        </tbody>
    </table>
@endsection