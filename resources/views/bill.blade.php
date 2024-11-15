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
            font-size: 14px;
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
            padding: 10px 5px;
            vertical-align: top;
            line-height: 25px;
            /* border: 1px solid #000 */
            font-size: 20px;
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
            font-size: 14px;
        }

        .invoice-box table tr td.qrcode {
            padding: 0 0 0 30px;
        }

        @page {
            margin: 10px;
            padding: 10px;
        }
    </style>
</head>

<body>

    @for ($i = 0; $i < $shipment->number_of_pieces; $i++)
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
                        <img src="https://alnaqil.sa/images/logo1.png"
                            style="width: 100%; max-width: 180px; margin-bottom: 20px" />
                        <br />
                        التاريخ :
                        {{ $shipment->created_at->format('Y-m-d') }}

                    </td>

                </tr>

                <tr>
                    <td colspan="2" style="font-weight: bold">
                        من:
                    </td>


                </tr>

                <tr>
                    <td colspan="2">

                        @if( !$shipment->return_back ) {{$shipment->pickupAddress && $shipment->store->type ==
                        'platform' ? $shipment->pickupAddress->name : $shipment->store->name }}
                        @else {{ $shipment->receiver_name }}
                        @endif
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        @if( !$shipment->return_back ) {{$shipment->pickupAddress ? $shipment->pickupAddress->city :
                        $shipment->store->city }}
                        @else {{ $shipment->city }}
                        @endif
                        , @if( !$shipment->return_back ) {{$shipment->pickupAddress ?
                        $shipment->pickupAddress->neighborhood : $shipment->store->neighborhood }}
                        @else {{ $shipment->neighborhood }}
                        @endif
                        @if( !$shipment->return_back ) {{ $shipment->pickupAddress ? ', ' .
                        $shipment->pickupAddress->full_address : ''}} @endif
                        @if( $shipment->return_back )
                        , {{ $shipment->street }}
                        @endif
                    </td>
                </tr>





                <tr>
                    <td colspan="2">
                        @if( !$shipment->return_back ) {{$shipment->pickupAddress->phone ??
                        $shipment->store->user->phone }}
                        @else {{ $shipment->receiver_phone }}
                        @endif
                    </td>
                </tr>

                <tr>
                    <td colspan="2">

                    </td>
                </tr>

                <tr>
                    <td>
                        <p>
                            عدد القطع:
                            {{ $shipment->number_of_pieces }}
                        </p>

                        <br>
                        <p>
                            محتويات الشحنة:
                            {{ $shipment->details }}
                        </p>

                        <br>
                        <p>
                            وزن الشحنة:
                            {{ $shipment->weight }} كغ
                        </p>

                    </td>

                    <td class="qrcode">

                        @php echo '<img
                            src="data:image/png;base64,' . DNS2D::getBarcodePNG(route('shipments.show', $shipment->id), 'QRCODE', 4, 4) . '"
                            alt="qrcode" />';@endphp


                    </td>

                </tr>

                <tr class="barcode">
                    <td colspan="2">
                        <table>
                            <tr>
                                <td colspan="2">
                                    <hr style="width: 100%">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

            </table>

            <table style="width: 100%">



                <tr class="bottom">
                    <td style="width: 70%">
                        <table>


                            <tr>
                                <td colspan="2" style="font-weight: bold">
                                    إلى:
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    @if( $shipment->return_back ) {{$shipment->pickupAddress && $shipment->store->type
                                    == 'platform' ? $shipment->pickupAddress->name : $shipment->store->name }}
                                    @else {{ $shipment->receiver_name }}
                                    @endif
                                </td>
                            </tr>


                            <tr>
                                <td colspan="2">
                                    @if( $shipment->return_back ) {{ $shipment->pickupAddress ?
                                    $shipment->pickupAddress->city : $shipment->store->city }}
                                    @else {{ $shipment->city }}
                                    @endif
                                    ، @if( $shipment->return_back ) {{$shipment->pickupAddress ?
                                    $shipment->pickupAddress->neighborhood : $shipment->store->neighborhood }}
                                    @else {{ $shipment->neighborhood }}
                                    @endif
                                    @if( $shipment->return_back ) {{ $shipment->pickupAddress ? ', ' .
                                    $shipment->pickupAddress->full_address : ''}} @endif
                                    @if( !$shipment->return_back )
                                    ،{{ $shipment->street }}
                                    @endif
                                </td>
                            </tr>



                            {{-- <tr>
                                <td colspan="2">
                                    الحي:

                                </td>
                            </tr> --}}



                            <tr>
                                <td colspan="2">
                                    @if( $shipment->return_back ) {{ $shipment->pickupAddress->phone ??
                                    $shipment->store->user->phone }}
                                    @else {{ $shipment->receiver_phone }}
                                    @endif
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2" style="padding-top: 25px; line-height: 14px">
                                    الدفع عند الاستلام <br /> <br />
                                    @if (!$shipment->financial->cod)
                                    <span style="font-size: 1em">دفع مسبق</span>
                                    @else
                                    <span style="font-size: 1em">{{ $shipment->financial->cod }} ريال</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>

                    <td style="padding-top: 100px; width: 30%; text-align: right">
                        <p>
                            <span style="font-size: 20px">
                                @if ($shipment->return_back) <span
                                    style="color:green; font-size: 20px; font-weight: bold"> &#9745;</span>
                                @else <span style="font-size: 20px; font-weight: bold"> &#9744; </span>
                                @endif
                            </span>
                            استرجاع
                        </p>
                        <p>
                            <span>
                                @if ($shipment->exchange) <span
                                    style="color:green ; font-size: 20px; font-weight: bold"> &#9745; </span>
                                @else <span style="font-size: 20px; font-weight: bold"> &#9744; </span>
                                @endif
                            </span>
                            استبدال
                        </p>
                        <p>
                            <span>
                                @if ($shipment->refrigerated) <span
                                    style="color:green; font-size: 20px; font-weight: bold"> &#9745; </span>
                                @else <span style="font-size: 20px; font-weight: bold"> &#9744; </span>
                                @endif
                            </span>
                            نقل مبرد
                        </p>

                    </td>



                </tr>
            </table>

            <div style="width: 100%; text-align: center; margin-top: 20px">
                @php echo '<img style=" position:absolute"
                    src="data:image/png;base64,' . DNS1D::getBarcodePNG($shipment->number, 'C39',2.5,50,array(5,5,5), ) . '"
                    alt="barcode" />';@endphp
            </div>

            @if($shipment->number_of_pieces > 1) <p style="width: 100%; text-align:center; font-size: 12px">{{ $i +
                1}}/{{$shipment->number_of_pieces}}</p> @endif
        </div>

        @if($shipment->number_of_pieces > ($i+1))
        <pagebreak></pagebreak>
        @endif
        @endfor


</body>

</html>