<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>محافظ المتاجر</title>

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
                <p style="display: inline-block; width:fit-content" class="title"> محافظ المتاجر</p>
                <p style="display:inline-block; width:fit-content">{{now()}}</p>
            </div>

			<table cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th>رقم المتجر</th>
                        <th>اسم المتجر</th>
                        <th>دفع آجل / مسبق</th>
                        <th>رسوم التوصيل (المحفظة)</th>
                        <th>المدفوع للعميل (المحفظة)</th>
                        <th>رصيد معلق   </th>
                        <th>إجمالي رصيد المحفظة   </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stores as $store)
                        <tr>
                            <td>{{$store['id']}}</td>
                            <td>{{$store['name']}}</td>
                            <td>
                                @if ($store['postpaid'] == 1) <span>{{ trans('postpaid') }}</span>
                                @else <span> {{trans('prepaid') }}</span>
                                @endif
                            </td>
                            <td>{{$store['wallet']['fees'] . ' ' .  trans('riyal')}} </td>
                            <td>{{$store['wallet']['paid'] . ' ' . trans('riyal')}} </td>
                            <td>{{$store['wallet']['outstandingBalance'] . ' ' . trans('riyal')}} </td>
                            <td>{{$store['wallet']['balance'] . ' ' . trans('riyal')}} </td>
                        </tr>
                    @endforeach

                </tbody>
			</table>

		</div>
	</body>
</html>
