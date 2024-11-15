<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>استلام الشحنات</title>

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
                <p class="title">استلام الشحنات</p>
                <p style="width: fit-content;">{{now()->format('Y-m-d')}}</p>
            </div>


			<table cellpadding="0" cellspacing="0" style="width: 100%">
                <thead>
                    <tr>
                        <th>اسم المتجر</th>
                        <th>المدينة</th>
                        <th>الحي</th>
                        <th>رقم الجوال</th>
                        <th> عدد الشحنات</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($stores as $store)
                        <tr>
                            <td class="text-center">
                                <p>{{$store['name']}}</p>
                            </td>
                            <td>{{$store['city']}}</td>
                            <td>{{$store['neighborhood']}}</td>
                            <td>{{$store['phone']}}</td>
                            <td>{{$store['shipments']}}</td>
                        </tr>
                    @endforeach

                </tbody>
			</table>

		</div>
	</body>
</html>
