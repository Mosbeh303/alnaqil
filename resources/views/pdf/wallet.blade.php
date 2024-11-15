<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>{{$wallet->name}}</title>

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

        table td,
        table th {
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

        table td.label,
        table th.label {
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
    <div class="invoice-box rtl">
        <div style=" min-width: 100%">
            <div class="logo" style="width: fit-content; float: right">
                <img src="https://alnaqil.sa/images/logo1.png" style="width: 100%; max-width: 130px" />
            </div>

            <div style="text-align:center">
                <h2 class="font-weight: normal">{{$wallet->name}}</h2>
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
                <table cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="label">التاريخ</td>
                        <td>من {{$from}} إلى {{$to}}</td>
                    </tr>

                    <tr>
                        <td class="label">الرقم الضريبي ( VAT )</td>
                        <td class="py-1 pr-2 pl-12 border border-slate-600">311251521800003</td>
                    </tr>
                </table>
            </div>
        </header>

        <div class="clear:both"></div>
        <table style="width: 100%; margin-top: 20px">
            <thead>
                <tr class="font-bold ">
                    <th class="label ">#</th>
                    <th class="label ">الإسم </th>
                    <th class="label ">الدائن </th>
                    <th class="label ">المدين </th>
                    <th class="label ">الرصيد</th>
                    <th class="label ">البنك</th>
                    <th class="label ">التاريخ</th>
                    <th class="label ">المسؤول</th>
                    <th class="label ">الموظف</th>
                    <th class="label ">ملاحظات</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($vouchers as $voucher)
                <tr>
                    <td>{{$voucher['number']}}</td>
                    <td>{{$voucher['store']}}</td>
                    <td>
                        @if($voucher['amount'] < 0) {{abs($voucher['amount'])}} @endif </td>
                    <td>
                        @if($voucher['amount'] > 0)
                        {{abs($voucher['amount'])}}
                        @endif
                    </td>
                    <td>{{$voucher['sum']}}</td>
                    <td>{{$voucher['to_bank']}}</td>
                    <td>{{$voucher['created_at']}}</td>
                    <td>{{$voucher['responsible']}}</td>
                    <td>{{$voucher['employee']}} </td>
                    <td>{{$voucher['notice']}} </td>
                </tr>
                @endforeach

            </tbody>
        </table>

    </div>
</body>

</html>