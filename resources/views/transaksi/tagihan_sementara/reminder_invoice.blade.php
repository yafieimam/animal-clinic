<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>Reminder Invoice</title>

    <style type="text/css">
        /* Outlines the grids, remove when sending */
        table td {
            /* border: 1px solid cyan; */
        }

        /* CLIENT-SPECIFIC STYLES */
        body,
        table,
        td,
        a {
            color: #F2F2F2;
            font-family: 'Helvetica', 'Arial', sans-serif;
            text-decoration: none;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table,
        td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            -ms-interpolation-mode: bicubic;
        }

        /* RESET STYLES */
        img {
            border: 0;
            outline: none;
            text-decoration: none;
        }

        table {
            border-collapse: collapse !important;
        }

        body {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }

        /* iOS BLUE LINKS */
        a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }

        /* ANDROID CENTER FIX */
        div[style*="margin: 16px 0;"] {
            margin: 0 !important;
        }


        .detail p {
            margin: 5px 0px !important;
        }


        /* MEDIA QUERIES */
        @media all and (max-width:639px) {
            .wrapper {
                width: 100% !important;
                padding: 0 !important;
            }

            .container {
                width: 300px !important;
                padding: 0 !important;
            }

            .mobile {
                width: 300px !important;
                display: block !important;
                padding: 0 !important;
            }

            .img {
                width: 100% !important;
                height: auto !important;
            }

            *[class="mobileOff"] {
                width: 0px !important;
                display: none !important;
            }

            *[class*="mobileOn"] {
                display: block !important;
                max-height: none !important;
            }
        }

    </style>
</head>

<body style="margin:0; padding:0; background-color:#F2F2F2;">
    <span style="display: block; width: 640px !important; max-width: 640px; height: 1px" class="mobileOn"></span>
    <center>
        <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#F2F2F2">
            <tr>
                <td align="center" valign="top">

                    <table width="640" cellpadding="0" cellspacing="0" border="0" class="wrapper"
                        bgcolor="#FFFFFF">
                        <tr>
                            <td height="10" style="font-size:10px; line-height:10px;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td align="center" valign="top">

                                <table width="600" cellpadding="0" cellspacing="0" border="0" class="container">
                                    <tr>
                                        <td align="center" valign="top">
                                            <img src="https://pjt-acm.amoreanimalclinic.com/dist/images/amoretext.png"
                                                height="100" style="margin:0; padding:0; border:none; display:block;"
                                                border="0" class="img" alt="">
                                        </td>
                                    </tr>
                                </table>

                            </td>
                        </tr>
                        <tr>
                            <td height="10" style="font-size:10px; line-height:10px;">&nbsp;</td>
                        </tr>
                    </table>
                    <table width="640" cellpadding="0" cellspacing="0" border="0" class="wrapper"
                        bgcolor="#FFFFFF">

                        <tr>
                            <td align="center" valign="top">
                                <hr style=" border: 1px solid #F2F2F2;">
                            </td>
                        </tr>

                    </table>
                    <table width="640" cellpadding="0" cellspacing="0" border="0" class="wrapper"
                        bgcolor="#FFFFFF">
                        <tr>
                            <td height="10" style="font-size:10px; line-height:10px;">
                                <br>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" valign="top">

                                <table width="600" cellpadding="0" cellspacing="0" border="0" class="container">
                                    <tr>
                                        <td align="left" valign="top">
                                            <span style="color:black">Kepada {{ $data->nama_owner }}</span>
                                            <p
                                                style="padding-top: 30px;padding-bottom:10p;color:black;line-height: 24px">
                                                Terima kasih atas kepercayaan anda dengan kami <br>
                                                Berikut ini adalah <b>E-INVOICE</b> dari Amore Animal Clinic
                                                <br>
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td height="10" style="font-size:10px; line-height:10px;">&nbsp;</td>
                        </tr>
                    </table>
                    <table width="640" cellpadding="0" cellspacing="0" border="0" class="wrapper"
                        bgcolor="#FFFFFF">
                        <tr>
                            <td align="center" valign="top">
                                <table width="600" cellpadding="0" cellspacing="0" border="0" class="container detail">
                                    <tr>
                                        <td align="left" valign="top">
                                            <p style="color:black">
                                                No Invoice
                                            </p>
                                        </td>
                                        <td style="max-width: 600px">
                                            <p style="color:black">
                                                <b>{{ $data->kode }}</b>
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left" valign="top">
                                            <p style="color:black">
                                                Nama Customer
                                            </p>
                                        </td>
                                        <td style="max-width: 600px">
                                            <p style="color:black">
                                                <b>
                                                    {{ $data->nama_owner }}
                                                </b>
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left" valign="top">
                                            <p style="color:black">
                                                Tanggal Invoice
                                            </p>
                                        </td>
                                        <td style="max-width: 600px">
                                            <p style="color:black">
                                                <b>
                                                    {{ carbon\carbon::parse($data->created_at)->format('d F Y') }}
                                                </b>
                                            </p>
                                        </td>
                                    </tr>
                                    @if ($data->sisa_pelunasan)
                                        <tr>
                                            <td align="left" valign="top">
                                                <p style="color:black">
                                                    Sisa Pembayaran
                                                </p>
                                            </td>
                                            <td style="max-width: 600px">
                                                <p style="color:black">
                                                    <b>
                                                        Rp {{ number_format($data->sisa_pelunasan, 2, ',', '.') }}
                                                    </b>
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr align="left" valign="top">
                                        <td colspan="2" style="color:black">
                                            <br>
                                            <p>
                                                Hormat Kami <br>
                                                Amore Animal Clinic
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <table width="640" cellpadding="0" cellspacing="0" border="0" class="wrapper"
                        bgcolor="#FFFFFF">
                        <tr>
                            <td align="center" valign="top">
                                &nbsp;
                            </td>
                        </tr>
                    </table>
                    <table width="640" cellpadding="0" cellspacing="0" border="0" class="wrapper"
                        bgcolor="#FFFFFF">
                        <tr>
                            <td align="center">

                                <table width="640" cellpadding="0" cellspacing="0" border="0" class="wrapper">
                                    <tr>
                                        <td width="640" style="position: relative" class="wrapper">
                                            <br>
                                            <img src="https://jpmexpress.co.id/storage/assets/png/Artboard%2033@1.5x-min%20(1).png"
                                                width="640" height="400"
                                                style="margin:0; padding:0; border:none; object-fit: fill" border="0"
                                                class="imgClass" alt="" />
                                        </td>
                                    </tr>
                                </table>

                            </td>
                        </tr>
                    </table>
                    <table width="640" cellpadding="0" cellspacing="0" border="0" class="wrapper"
                        bgcolor="#FFFFFF">
                        <tr>
                            <td align="center" valign="top">
                                &nbsp;
                            </td>
                        </tr>
                    </table>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
                        <tr>
                            <td valign="top" align="center" class="p30-15" style="padding: 50px 0px 50px 0px;">
                                <table width="650" border="0" cellspacing="0" cellpadding="0" class="mobile-shell">
                                    <tr>
                                        <td class="td"
                                            style="width:650px; min-width:650px; font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal;">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td align="center" style="padding-bottom: 30px;">
                                                        <table border="0" cellspacing="0" cellpadding="0">
                                                            <tr>
                                                                <td class="img"
                                                                    style="font-size:0pt; line-height:0pt; text-align:center;">
                                                                    <a href="https://www.facebook.com/PT-Jawa-Pratama-Mandiri-107557964059623"
                                                                        target="https://www.facebook.com/PT-Jawa-Pratama-Mandiri-107557964059623"><img
                                                                            src="https://jpmexpress.co.id/storage/assets/png/t7_ico_facebook.jpg"
                                                                            width="34" height="34" mc:edit="image_23"
                                                                            style="max-width:34px;" border="0"
                                                                            alt="" /></a>
                                                                    <a href="https://www.instagram.com/jpm.express/"
                                                                        target="https://www.instagram.com/jpm.express/"><img
                                                                            src="https://jpmexpress.co.id/storage/assets/png/t7_ico_instagram.jpg"
                                                                            width="34" height="34" mc:edit="image_25"
                                                                            style="max-width:34px;" border="0"
                                                                            alt="" /></a>

                                                                    <a href="https://id.linkedin.com/company/jawa-pratama-mandiri"
                                                                        target="https://id.linkedin.com/company/jawa-pratama-mandiri"><img
                                                                            src="https://jpmexpress.co.id/storage/assets/png/t7_ico_linkedin.jpg"
                                                                            width="34" height="34" mc:edit="image_26"
                                                                            style="max-width:34px;" border="0"
                                                                            alt="" /></a>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-footer1 pb10"
                                                        style="color:#999999; font-family:'Lato', Arial,sans-serif; font-size:14px; line-height:20px; text-align:center; padding-bottom:10px;">
                                                        <div mc:edit="text_44">Amore Animal Clinic
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-footer2 pb20"
                                                        style="color:#999999; font-family:'Lato', Arial,sans-serif; font-size:12px; line-height:26px; text-align:center; padding-bottom:20px;">
                                                        <div mc:edit="text_45">{{ $data->branch->alamat }}</div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="img"
                                                        style="font-size:0pt; line-height:0pt; text-align:left;">
                                                        <div mc:edit="text_47">
                                                            <!--[if !mso]><!-->
                                                            *|LIST:DESCRIPTION|*
                                                            *|LIST:ADDRESS|*
                                                            *|REWARDS_TEXT|*
                                                            <!--<![endif]-->
                                                        </div>
                                                    </td>
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
    </center>
    <span style="opacity: 0"> {{ carbon\carbon::now() }} </span>

</body>

</html>
