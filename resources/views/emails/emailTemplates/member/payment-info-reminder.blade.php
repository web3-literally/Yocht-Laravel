@extends('emails/layouts/emailTemplate')

@section('content')
    <table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
        <tbody>
        <tr>
            <td style="color:#141d23;font-family: 'Montserrat',Arial, sans-serif;font-size:18px;padding-right:30px;padding-left:30px;font-weight:500;letter-spacing:1px;line-height:30px;" data-bgcolor="Title" data-color="Title" data-size="Title" data-min="12" data-max="60">
                Hello, {{ $userName }}.<br>
                <br>
                <p>Your account on {{ config('app.name') }} will be inactive unless you become a member.</p>
                <p>Please click on the button bellow to become a member.</p>
                <br>
                <a href="{{ route('subscription.plans') }}">Become a member</a>
            </td>
        </tr>
        </tbody>
    </table>
@endsection