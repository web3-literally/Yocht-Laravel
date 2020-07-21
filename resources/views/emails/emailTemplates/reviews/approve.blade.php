@extends('emails/layouts/emailTemplate')

@section('content')
    <table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
        <tbody>
        <tr>
            <td style="color:#141d23;font-family: 'Montserrat',Arial, sans-serif;font-size:18px;padding-right:30px;padding-left:30px;font-weight:500;letter-spacing:1px;line-height:30px;" data-bgcolor="Title" data-color="Title" data-size="Title" data-min="12" data-max="60">
                <p>Was posted a new review. Please see details below.</p>
                <br>
                <p>Title: <strong>{{ $review->title }}</strong></p>
                <p>Message:<br>{{ $review->message }}</p>
                <br>
                <p>Please, click button below to approve or decline it.<br>
                    <a href="{{ $approveUrl }}">Approve</a> <a href="{{ $declineUrl }}">Decline</a>
                </p>
                <br>
                <p><small>This is a system notification.</small></p>
            </td>
        </tr>
        </tbody>
    </table>
@endsection