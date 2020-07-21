@extends('emails/layouts/emailTemplate')

@section('content')
    <table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
        <tbody>
        <tr>
            <td style="color:#141d23;font-family: 'Montserrat',Arial, sans-serif;font-size:18px;padding-right:30px;padding-left:30px;font-weight:500;letter-spacing:1px;line-height:30px;" data-bgcolor="Title" data-color="Title" data-size="Title" data-min="12" data-max="60">
                <p>Ticket #{{ $job->ticket->id }} was changed.</p>
                <br>
                <p><a href="{{ route('account.tickets.details', $job->ticket->id) }}">View Ticket Details</a></p>
            </td>
        </tr>
        </tbody>
    </table>
@endsection