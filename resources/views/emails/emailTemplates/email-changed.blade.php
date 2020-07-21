@extends('emails/layouts/emailTemplate')

@section('content')
    <table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
        <tbody>
        <tr>
            <td style="color:#141d23;font-family: 'Montserrat',Arial, sans-serif;font-size:18px;padding-right:30px;padding-left:30px;font-weight:500;letter-spacing:1px;line-height:30px;" data-bgcolor="Title" data-color="Title" data-size="Title" data-min="12" data-max="60">
                Hello, {{ $userName }}.<br>
                <br>
                <p>Your {{ config('app.name') }} account email has been changed to {{ $confirmation->email }}.</p>
                <br>
                <p>Please, ignore this system message if email was changed by you, or contact to support team.</p>
            </td>
        </tr>
        </tbody>
    </table>
@endsection