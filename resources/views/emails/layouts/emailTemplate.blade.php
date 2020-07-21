<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <title>Josh Admin</title>
    <!-- Jquery Framework -->
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <!-- API 2 -->
    <script type="text/javascript" src="http://www.stampready.net/api2/api.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css?family=Montserrat');
        @import url('https://fonts.googleapis.com/css?family=Open Sans');
    </style>
</head>
<body>
<table width="100%" style="font-family: 'Montserrat',Arial, sans-serif; border-collapse: collapse;" bgcolor="#ececec" border="0" cellspacing="0" cellpadding="0">
    <tbody>
    <tr>
        <td align="center">
            <table style="background-color:#08314f;font-family: 'Montserrat',Arial, sans-serif;font-size:26px;font-weight:500;letter-spacing:1px;line-height:30px;border-collapse: collapse;">
                <tbody>
                <tr>
                    <td width="600" height="120" align="center">
                        <center>
                            <img data-crop="false" style="display:block;" src="{{ asset('assets/img/frontend/logo-white.png') }}" alt="{{ config('name') }}"/>
                        </center>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
<table width="100%" style="font-family: 'Montserrat',Arial, sans-serif; border-collapse: collapse;" bgcolor="#ececec" border="0" cellspacing="0" cellpadding="0">
    <tbody>
    <tr>
        <td align="center">
            <table align="center" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                    <td width="600" height="50" bgcolor="#fff" align="center"></td>
                </tr>
                <tr>
                    <td width="600" bgcolor="#fff" align="center">
                        @yield('content')
                    </td>
                </tr>
                <tr>
                    <td width="600" height="50" bgcolor="#fff" align="center"></td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
<table bg-color="#7f8c8d" style="font-family: 'Montserrat', Arial, sans-serif;color:#7f8c8d; border-collapse: collapse;" width="100%" bgcolor="#fff" align="center" border="0" cellspacing="0" cellpadding="0">
    <tbody>
    <tr>
        <td data-bg="header bg" data-bgcolor="header bg" align="center" bgcolor="#ececec">
            <table align="center" border="0" cellpadding="0" cellspacing="0">
                <tbody>
                <tr>
                    <td width="600" align="center" bgcolor="#08314f">
                        <table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
                            <tbody>
                            <tr>
                                <td height="10"></td>
                            </tr>
                            <tr>
                                <td align="center" data-link-style="text-decoration:none; color:#fff;" data-link-color="Content" data-size="Content" data-color="Content" style="font-family: 'Open Sans', Arial, sans-serif; font-size:15px; color:#a2a9af; line-height:30px;">
                                    <singleline>
                                        @widget('Copyright')
                                    </singleline>
                                </td>
                            </tr>
                            <tr>
                                <td height="5"></td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
</body>

</html>
