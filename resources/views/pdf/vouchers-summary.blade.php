<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>كشف سندات القبض والصرف </title>

    <style>
        .invoice-box {
            max-width: 850px;
            margin: auto;
            border-radius: 5px;
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
            <p style="display: inline-block; width:fit-content" class="title">كشف سندات القبض والصرف  </p>
            <p>تاريخ: <span>من {{ $from }}</span> <span>الى {{ $to }}</span></p>
        </div>

        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>اسم المتجر</th>
                    <th>
                        رقم السند
                    </th>
                    <th>
                        نوع السند
                    </th>
                    <th>
                        قيمة السند
                    </th>
                    <th>
                        تاريخ السند
                    </th>
                    <th>
                        المسؤول
                    </th>
                    <th>
                        الموظف
                    </th>
                    <th>
                        البنك
                    </th>
                </tr>
            </thead>
            <tbody>

                @foreach ($items as $item)
                    <tr>
                        <td>
                            {{ $item['store'] }}
                        </td>
                        <td>
                            {{ $item['number'] }}
                        </td>
                        <td>
                            @if($item['type'] ===  'RECEIPT')قبض
                            @else صرف
                            @endif
                        </td>
                        <td>
                            {{ $item['amount'] }}
                        </td>
                        <td>
                            {{ $item['date'] }}
                        </td>
                        <td>
                            {{ $item['responsible'] }}
                        </td>
                        <td>
                            {{ $item['employee'] }}
                        </td>
                        <td>
                            {{ $item['bank'] }}
                        </td>

                    </tr>
                @endforeach

            </tbody>
        </table>

    </div>
</body>

</html>
