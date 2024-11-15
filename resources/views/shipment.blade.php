<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>بوليصة الشحنة رقم {{ $shipment->number }}</title>

    <style>
        /* CSS styles */

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
            font-size: 12px;
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

        .bottom-left {
            position: fixed;
            left: 10px;
            bottom: 10px;
        }

        @page {
            margin: 10px;
            padding: 10px;
        }
    </style>
</head>

<body>

    @php
    $receiverFields = [
    'إسم المستلم' => $shipment->receiver_name,
    'هاتف المستلم' => $shipment->receiver_phone,
    ];

    $coordinatesFields = [
    'المدينة' => $shipment->city,
    'الشارع' => $shipment->street,
    'الحي' => $shipment->neighborhood,
    ];

    $otherFields = [
    'إسم المتجر' => $shipment->store_name,
    'مكان التخزين' => $shipment->warehouse_location,
    'التفاصيل' => $shipment->details,
    'فاشلة' => $shipment->failed,
    'تبادل' => $shipment->exchange,
    'مبردة' => $shipment->refrigerated,
    'مرتجعة' => $shipment->return_back,
    'تاريخ الشحن' => $shipment->shipping_date,
    'تاريخ الإستلام' => $shipment->pickup_date,
    'وزن الشحنة' => $shipment->weight,
    'عدد القطع' => $shipment->number_of_pieces,
    'المصدر' => $shipment->source,
    'رقم طلب سلة' => $shipment->salla_order_id,
    ];
    @endphp

    <div class="invoice-box rtl">
        <table cellpadding="0" cellspacing="0">
            <tr>
                <td class="barcode">
                    @php echo '<img
                        src="data:image/png;base64,' . DNS1D::getBarcodePNG($shipment->number, 'C39', 1.5, 50, array(5, 5, 5), ) . '"
                        alt="barcode" />'; @endphp
                    <p>{{ $shipment->number }}</p>
                </td>

                <td class="title">
                    <img src="https://alnaqil.sa/images/logo1.png"
                        style="width: 100%; max-width: 130px; margin-bottom: 20px" />
                    <br />
                    التاريخ :
                    {{ $shipment->created_at->format('Y-m-d') }}
                </td>

            </tr>
            @foreach ($receiverFields as $fieldName => $fieldValue)
            <tr>
                <td>{{ $fieldName }}:</td>
                <td>{{ $fieldValue }}</td>
            </tr>
            @endforeach
        </table>
    </div>

    <div class="invoice-box rtl">
        <table cellpadding="0" cellspacing="0">
            @foreach ($coordinatesFields as $fieldName => $fieldValue)
            <tr>
                <td>{{ $fieldName }}:</td>
                <td>{{ $fieldValue }}</td>
            </tr>
            @endforeach
        </table>
    </div>

    <div class="invoice-box rtl">
        <table cellpadding="0" cellspacing="0">
            @foreach ($otherFields as $fieldName => $fieldValue)
            <tr>
                <td>{{ $fieldName }}:</td>
                <td>{{ $fieldValue }}</td>
                <td></td>
            </tr>
            @endforeach

            <tr>
                <td></td>
                <td>
                    <div class="bottom-left">
                        @php
                        echo '<img
                            src="data:image/png;base64,' . DNS2D::getBarcodePNG(route('shipments.show', $shipment->id), 'QRCODE', 4, 4) . '"
                            alt="qrcode" />';
                        @endphp
                    </div>
                </td>
                <td></td>
            </tr>
        </table>
    </div>

</body>

</html>