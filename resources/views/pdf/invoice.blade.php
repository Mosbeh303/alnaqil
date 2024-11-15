<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>فاتورة ضريبية</title>

    <style>
        *,
        ::before,
        ::after {
            box-sizing: border-box;
            border-width: 0;
            border-style: solid;
            border-color: #333;
        }



        body {
            max-width: 800px;
            margin: auto;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            line-height: 10px;
            text-align: right;
            font-family: 'XBRiyaz', sans-serif;
            color: #333;
        }

        header {
            display: flex;
            justify-content: space-between;
        }

        header ul li {
            margin-top: 15px
        }

        table td {
            padding: 8px 10px;
            border: 1px solid rgb(51, 65, 85);
            ;
            text-align: right;
        }

        header table td {
            padding: 8px 10px 8px 50px;
        }

        table {
            border-collapse: collapse;
            direction: rtl
        }

        table td.label {
            background-color: #D1D5DB
        }

        .logo {
            margin-top: 20px;
            margin-bottom: 20px
        }



        .font-bold {
            font-weight: bold
        }
    </style>



</head>

<body>
    <div class="container">
        <div style=" min-width: 100%">
            <div class="logo" style="width: fit-content; float: right">
                <img src="https://alnaqil.sa/images/logo1.png" style="width: 100%; max-width: 130px" />
                {{-- logo --}}
            </div>

            <div style="text-align:center">
                <h2 class="font-weight: normal">فاتورة ضريبية</h2>
            </div>
        </div>

        <div style="clear: both"></div>

        <header style="">
            <div style="width: fit-content; float: right">
                <ul style="list-style: none">
                    <li style="font-weight: bold"> شركة الناقل المحلي لنقل الطرود </li>
                    <li>سفيان بن عوف _ 3435 _ العزيزية</li>
                    <li> الرياض 14513 _ 7760</li>
                    <li>المملكة العربية السعودية</li>
                </ul>
            </div>
            <div class="top-table" style="width: fit-content; float: left">
                <table class="w-full border-collapse border border-slate-700">
                    <tr>
                        <td class="label">التاريخ</td>
                        <td>{{$shipment['date']}}</td>
                    </tr>
                    <tr>
                        <td class="label">رقم الفاتورة</td>
                        <td>SA{{$shipment['invoice']}}</td>
                    </tr>
                    <tr>
                        <td class="label">الرقم الضريبي ( VAT )</td>
                        <td class="py-1 pr-2 pl-12 border border-slate-600">311251521800003</td>
                    </tr>
                </table>
            </div>
        </header>

        <div class="clear:both"></div>

        <div style="margin-top: 20px">
            <div style="width: 49%; float: right">
                <table style="width: 100%">
                    <tr>
                        <td class="label ">اسم العميل</td>
                        <td class="py-1 pr-2 pl-12 border border-slate-600 ">{{$shipment['storeName']}}</td>
                    </tr>
                    <tr>
                        <td class="label ">الخدمة</td>
                        <td class="">نقل طرود </td>
                    </tr>
                </table>
            </div>

            <div style="width: 49%; float: left">
                <table style="width: 100%">
                    <tr>
                        <td class="label ">رقم الجوال</td>
                        <td class=" ">{{$shipment['storePhone']}}</td>
                    </tr>
                    <tr>
                        <td class="label ">الرقم الضريبي (VAT)</td>
                        <td class="">{{$shipment['storeTaxNumber']}}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div>
            <table style="margin-top: 15px; width: 100%">
                <tr>
                    <td class="label ">عنوان العميل</td>
                    <td>{{$shipment['storeCity']}}, {{$shipment['storeNeighborhood']}}</td>

                </tr>
            </table>
        </div>

        <div style="margin-top: 10px">
            <p class="mb-2 font-bold">تفاصيل الفاتورة</p>
            <table>
                <tr class="font-bold ">
                    <td class="label ">رقم الشحنة</td>
                    <td class="label ">محتويات الشحنة</td>
                    <td class="label ">سعر التوصيل</td>
                    <td class="label">معدل تطبيق الضريبة</td>
                    <td class="label"> الضريبة </td>
                    <td class="label">مبلغ الدفع عند الاستلام</td>
                    <td class="label"> الحالة </td>
                </tr>
                <tr>
                    <td>{{$shipment['number']}}</td>
                    <td>{{$shipment['details']}}</td>
                    <td>{{$shipment['fees'] - $shipment['tax']}}</td>
                    <td>15%</td>
                    <td>{{$shipment['tax']}} </td>
                    <td>

                        @if (!$shipment['cod'])
                        <span>دفع مسبق</span>
                        @elseif($shipment['status'] == -100)
                        0
                        @else
                        <span>{{$shipment['cod']}} ريال</span>
                        @endif
                    </td>
                    <td class="py-1 px-2 border border-slate-600 ">
                        @if ($shipment['status'] == 100)
                        <span>تم التوصيل</span>
                        @elseif ($shipment['status'] == -100)
                        <span>مرتجعة</span>
                        @else
                        <span>ملغية</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
        <div style="margin-top: 40px">
            <div style="width: 49%; float: right">
                <table style="width: 100%">
                    <tr class="mb-4">
                        <td class="label">اجمالي المبلغ</td>
                        <td>{{ $shipment['fees'] - $shipment['tax']}} ريال</td>
                    </tr>
                    <tr>
                        <td class="label">ضريبة القيمة المضافة 15%</td>
                        <td>{{$shipment['tax']}} ريال</td>
                    </tr>
                    <tr>
                        <td class="label">الاجمالي شاملا الضريبة المضافة</td>
                        <td>{{$shipment['fees']}} ريال</td>
                    </tr>
                </table>
            </div>
            <div style="width: 49%; float: left; text-align: center">
                <img
                    src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(110)->style('round')->generate(Request::url())) !!} ">
            </div>
        </div>


    </div>
</body>

</html>