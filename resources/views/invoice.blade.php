<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type"
          content="text/html; charset=utf-8">
    <title>Invoice - Royalty</title>

    <style>
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
            font-size: 16px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            text-align: left;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }

        /** RTL **/
        .rtl {
            direction: rtl;
            font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        }

        .rtl table {
            text-align: right;
        }

        .rtl table tr td:nth-child(2) {
            text-align: left;
        }
    </style>
</head>

<body>
<div class="invoice-box">
    <table cellpadding="0" cellspacing="0">
        <tr class="top">
            <td colspan="2">
                <table>
                    <tr>
                        <td class="title">
                            <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/icon/logo-pdf.png"
                                 style="width:150px; height: 150px;">
                        </td>
                        <td>
                            Transaction ID #: {{$exist->tran_id}}<br>
                            Date: {{ date('d M Y') }}<br>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr class="information">
            <td colspan="2">
                <table>
                    <tr>
                        <td>
                            Royalty Bangladesh Ltd.<br>
                            Bashundhara R/A,<br>
                            Dhaka 1229.<br>
                            Phone: +880-963-862-0202<br>
                            E-mail: support@royaltybd.com
                        </td>

                        <td>
                            {{$exist->customer_full_name}}<br>
                            {{$exist->customer_contact_number}}<br>
                            {{$exist->customer_email}}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>


        <tr class="heading">
            <td>
                Payment Method
            </td>

            <td>
                Online Payment
            </td>
        </tr>


        <tr class="heading">
            <td>
                Item
            </td>

            <td>
                Price
            </td>
        </tr>

        @if($exist->delivery_type == $renew_delivery_type)
            <tr class="item">
                @if($promo_used)
                    <td>
                        Membership Renewal Fee (Promo applied)
                    </td>
                @else
                    <td>
                        Membership Renewal Fee
                    </td>
                @endif

                <td>
                    {{ $price }} BDT
                </td>
            </tr>

            <tr class="total">
                <td></td>

                <td>
                    Total: {{ round($price) }} BDT
                </td>
            </tr>

        @else
            <tr class="item">
                @if($promo_used)
                    <td>
                        Membership Fee (Promo applied)
                    </td>
                @else
                    <td>
                        Membership Fee
                    </td>
                @endif
                <td>
                    {{ round($price)}} BDT
                </td>
            </tr>
            <tr class="total">
                <td></td>

                <td>
                    Total: {{ round($price) }} BDT
                </td>
            </tr>
        @endif

    </table>
</div>
</body>
</html>