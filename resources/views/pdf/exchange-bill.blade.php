<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>بوليصة الشحنة رقم {{ $shipment->number }}</title>

    <style>
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 10px;
            line-height: 8px;
            direction: rtl;
            font-family: 'XBRiyaz', sans-serif;
            color: #555;
            position: relative;


        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: right;
        }

        .invoice-box table td p {
            line-height: 30px
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
            /* border: 1px solid #000 */
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: left;
        }

        .invoice-box table tr table td {
            padding-bottom: 10px;
        }


        .qrcode {
            text-align: center;
            padding: 30px 0;
        }

        td.barcode {
            text-align: center;
        }

        span {
            font-weight: bold;
            font-size: 12px;
        }

        .invoice-box table tr td.qrcode {
            padding: 0 0 0 30px;
        }

        @page {
            margin: 10px;
            padding: 10px;
            /* size: 10cm 10cm; */

        }
    </style>
</head>

<body>
    <div class="invoice-box rtl">
        <table cellpadding="0" cellspacing="0">

            <tr>
                <td class="barcode">
                    @php echo '<img
                        src="data:image/png;base64,' . DNS1D::getBarcodePNG($shipment->number, 'C39',1.5,50,array(5,5,5), ) . '"
                        alt="barcode" />';@endphp
                    <p>{{ $shipment->number }}</p>
                </td>

                <td class="title">
                    {{-- <img src="https://alnaqil.sa/images/logo1.png"
                        style="width: 100%; max-width: 130px; margin-bottom: 20px" /> --}}
                </td>

            </tr>

            <tr>
                <td colspan="2">

                    {{ $shipment->store->name }}

                </td>
            </tr>

            <tr>
                <td colspan="2">
                    {{ $shipment->store->city }}
                    ,{{ $shipment->store->neighborhood }}
                </td>
            </tr>





            <tr>
                <td style="width: 30%">
                    {{ $shipment->store->user->phone }}
                </td>
                <td style="width: 30%">

                    <p>
                        <span style="font-size: 20px">
                            @if ($shipment->return_back) <span style="color:green; font-size: 20px; font-weight: bold">
                                &#9745;</span>
                            @else <span style="font-size: 20px; font-weight: bold"> &#9744; </span>
                            @endif
                        </span>
                        استرجاع
                    </p>

                </td>

                <td style="width: 30%">
                    <p>
                        <span>
                            @if ($shipment->exchange) <span style="color:green ; font-size: 20px; font-weight: bold">
                                &#9745; </span>
                            @else <span style="font-size: 20px; font-weight: bold"> &#9744; </span>
                            @endif
                        </span>
                        استبدال
                    </p>
                </td>
            </tr>

            <tr>
                <td>
                    عدد القطع:
                    {{ $shipment->number_of_pieces }}

                </td>


            </tr>



        </table>






    </div>


</body>

</html>