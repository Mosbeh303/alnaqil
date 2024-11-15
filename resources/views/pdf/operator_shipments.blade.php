<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>الشحنات المسجلة باسم المندوب  {{ $operatorName }}</title>

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
                font-size: 15px;
                width: fit-content
            }

            .text-center{
                text-align: center
            }



		</style>
	</head>

	<body>
		<div class="invoice-box rtl">
            <div style="display: flex; justify-content:space-between">
                @if ($userAccount !== 'operator')
                    <p class="title">الشحنات المسجلة باسم المندوب  {{ $operatorName }}</p>
                @endif

                <p style="width: fit-content;">{{now()->format('Y-m-d')}}</p>
            </div>


			<table cellpadding="0" cellspacing="0" style="width: 100%">
                <thead>
                    <tr>
                        <th>رقم الشحنة</th>
                        <th>اسم المستلم</th>
                        <th>رقم الجوال</th>
                        <th>المدينة، الحي</th>
                        <th>الشارع</th>
                        <th> الدفع عند الاستلام</th>
                        <th>عدد الطرود</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($shipments as $shipment)
                        <tr>
                            <td class="text-center">
                                @php echo '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($shipment['number'], 'C39',1,43,array(5,5,5), ) . '" alt="barcode"   />';@endphp
                                <p>{{$shipment['number']}}</p>
                            </td>
                            <td>{{$shipment['receiverName']}}</td>
                            <td>{{$shipment['receiverPhone']}}</td>
                            <td>{{$shipment['city']}}، {{$shipment['neighborhood']}}</td>
                            <td>{{$shipment['street']}}</td>
                            <td>
                               @if ($shipment['cod'] == 0) دفع مسبق
                               @else {{$shipment['cod']}}
                               @endif
                            </td>
                            <td>{{$shipment['number_of_pieces']}}</td>

                        </tr>
                    @endforeach

                </tbody>
			</table>

		</div>
	</body>
</html>
