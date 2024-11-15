<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>مستحقات المتاجر</title>

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
                <p style="display: inline-block; width:fit-content" class="title"> مستحقات المتاجر</p>
                <p style="display:inline-block; width:fit-content">{{now()}}</p>
            </div>

			<table cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th>رقم المتجر</th>
                        <th>اسم المتجر</th>
                        <th>البنك</th>
                        <th>اجمالي رسوم التوصيل</th>
                        <th>اجمالي الدفع عند الإستلام</th>
                        <th>كاش   </th>
                        <th>شبكة   </th>
                        <th>سندات القبض  </th>
                        <th>سندات الصرف  </th>
                        <th> المستحقات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stores as $store)
                        <tr>
                            <td>{{$store['id']}}</td>
                            <td>{{$store['name']}}</td>
                            <td>{{$store['bank']}}</td>
                            <td>{{$store['fees']}}</td>
                            <td>{{$store['cod']}}</td>
                            <td>{{$store['cash']}}</td>
                            <td>{{$store['epayment']}}</td>
                            <td>{{$store['receipt']}}</td>
                            <td>{{$store['payment']}}</td>
                            <td>
                                @if($store['previousDues'] - ($store['cod']   - $store['receipt'] - $store['payment'] - $store['fees']) !== 0)
                                    {{ number_format((float) ($store['previousDues'] - ($store['cod']   - $store['receipt'] - $store['payment'] - $store['fees'])), 2, '.', '')  }}
                                @else 0
                                @endif
                            </td>
                        </tr>
                    @endforeach

                </tbody>
			</table>

		</div>
	</body>
</html>
