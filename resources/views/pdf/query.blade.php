<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>كشف حساب الشحنات</title>

		<style>
			.invoice-box {
				max-width: 850px;
				margin: auto;
                border-radius: 5px ;
				font-size: 14px;
				line-height: 10px;
				direction: rtl;
				font-family: 'XBRiyaz', sans-serif;
				color: #333;
			}

            table {
                border: solid 1px #DDEEEE;
                border-collapse: collapse;
                border-spacing: 0;
                font: normal 13px Arial, sans-serif;
            }

            table thead th {
                background-color: #eee;
                border: solid 1px #999;
                padding: 10px;
                color: #444;
                line-height: 20px
            }

            table tbody td {
                border: solid 1px #999;
                color: #333;
                padding: 10px;
                text-shadow: 1px 1px 1px #fff;
                line-height: 20px
            }

            .title {
                font-weight: bold;
                font-size: 15px
            }



		</style>
	</head>

	<body>
		<div class="invoice-box rtl">
            <p class="title"> الاستعلام عن الشحنات</p>
			<table cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th>رقم الشحنة</th>
                        <th>الانشاء</th>
                        <th>التوصيل</th>
                        <th>نوع الشحنة</th>
                        <th> الدفع عند الاستلام</th>
                        <th>رقم الجوال</th>
                        <th>اسم المرسل</th>
                        <th>  المدينة (الحي)</th>
                        <th> الحالة</th>
                        <th> ملاحظات</th>
                        <th> مكان التخزين</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($shipments as $shipment)
                        <tr>
                            <td>{{$shipment['number']}}</td>
                            <td>{{$shipment['created_at']}}</td>
                            <td>{{$shipment['shipping_date']}}</td>
                            <td>
                                @if ($shipment['return_back']) مسترجع
                                @elseif ($shipment['refrigerated']) نقل مبرد
                                @elseif ($shipment['exchange']) استبدال
                                @endif عادي
                             </td>
                            <td>
                               @if ($shipment['cod'] == 0) دفع مسبق
                               @else {{$shipment['cod']}}
                               @endif
                            </td>
                            <td>{{$shipment['receiverPhone']}}</td>
                            <td>{{$shipment['storeName']}}</td>
                            <td>{{$shipment['city']}} ({{$shipment['neighborhood']}})</td>
                            <td>
                                @if ($shipment['status'] == "100") تم التوصيل
                                @elseif ($shipment['status'] == "-100") مرتجعة
                                @elseif ($shipment['status'] == "10") تم الانشاء
                                @elseif ($shipment['status'] == "20") تم الاستلام
                                @elseif ($shipment['status'] == "35") جاري التجهيز
                                @elseif ($shipment['status'] == "-1") ملغية
                                @endif
                            </td>
                            <td>{{$shipment['notice']}}</td>
                            <td>{{$shipment['warehouseLocation']}}</td>
                        </tr>
                    @endforeach

                </tbody>
			</table>

		</div>
	</body>
</html>
