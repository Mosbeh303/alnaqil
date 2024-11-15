

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>فواتير  </title>

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

                <p style="display: inline-block; width:fit-content" class="title">
                    @if ($store)  قائمة فواتير المتجر {{$store}}
                    @else  قائمة الفواتير
                    @endif
                </p>
                @if($from && $to) <p>تاريخ: <span>من {{$from}}</span> <span>الى {{$to}}</span></p> @endif
            </div>

            <table class="w-full border border-collapse border-slate-700">
                <thead>
                    <tr>
                        <th class="label">رقم المتجر</th>
                        <th class="label">اسم المتجر</th>
                        <th class="label">رقم الفاتورة</th>
                        <th class="label">تاريخ الفاتورة</th>
                        <th class="label">الرسوم قبل الضريبة</th>
                        <th class="label">الضريبة</th>
                        <th class="label">الرسوم شامل الضريبة</th>
                        <th class="label">الرقم الضريبي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td>{{$item['storeNumber']}}</td>
                            <td>{{$item['storeName']}}</td>
                            <td>{{$item['number']}}</td>
                            <td>{{$item['date']}}</td>
                            <td>{{$item['fees_without_tax']}} ريال</td>
                            <td>{{$item['tax']}} ريال</td>
                            <td>{{$item['fees']}} ريال</td>
                            <td>{{$item['tax_number']}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

		</div>
	</body>
</html>

