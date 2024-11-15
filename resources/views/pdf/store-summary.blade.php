<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>كشف حساب المتجر</title>

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
                width: 100%;
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
            <div style="display: flex; justify-content: space-between">
                <p style="display: inline-block; width:fit-content" class="title"> مستحقات المتجر رقم {{$store}}</p>
                <p>تاريخ: <span>من {{$from}}</span> <span>الى {{$to}}</span></p>
            </div>

			<table cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th>رقم الشحنة</th>
                        <th>رقم السند</th>
                        <th>رسوم التوصيل</th>
                        <th> الدفع عند الاستلام</th>
                        <th> عمولة cod </th>
                        <th>قيمة السند</th>
                        <th>التاريخ </th>
                        <th>حالة الشحنة</th>
                        <th>البيان </th>
                        <th>المجموع</th>
                        <th>الاجمالي</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($items as $item)
                        <tr>
                            <td> @if(array_key_exists('number', $item)) {{$item['number']}} @endif</td>
                            <td> @if(array_key_exists('id', $item)) {{$item['id']}} @endif</td>
                            <td>@if(array_key_exists('fees', $item)) {{$item['fees'] - $item['codFee']}} @endif</td>
                            <td>
                                @if(array_key_exists('cod', $item))
                                    @if ($item['cod'] == 0) دفع مسبق
                                    @else {{$item['cod']}}
                                    @endif
                                @endif
                            </td>
                            <td> @if(array_key_exists('codFee', $item)) {{$item['codFee']}} @endif</td>
                            <td> @if(array_key_exists('amount', $item)) {{$item['amount']}} @endif</td>
                            <td>{{$item['date']}} </td>
                            <td>
                                @if(array_key_exists('status', $item))
                                    @if ($item['status'] == "100") تم التوصيل
                                    @else مرتجعة
                                    @endif
                                @endif
                            </td>
                            <td> @if(array_key_exists('amount', $item)) {{$item['notice']}} @endif</td>
                            <td>@if(array_key_exists('cod', $item)) {{$item['fees'] - $item['cod']}} @endif</td>
                            <td>
                                @php
                                    if(array_key_exists('id', $item))
                                        $sum = number_format((float)$sum, 2, '.', '') + $item['amount'];
                                    else
                                        $sum =  number_format((float)$sum, 2, '.', '') + ($item['fees'] - $item['cod'] );
                                @endphp

                                {{ number_format((float)$sum, 2, '.', '')}}
                            </td>
                        </tr>
                    @endforeach

                </tbody>
			</table>

		</div>
	</body>
</html>
