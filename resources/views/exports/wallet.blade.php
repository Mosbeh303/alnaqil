<table>
    <thead>
    <tr>
        <th >#</th>
        <th >الإسم  </th>
        <th >الدائن </th>
        <th>المدين </th>
        <th >الرصيد</th>
        <th >البنك</th>
        <th >التاريخ</th>
        <th >المسؤول</th>
        <th>الموظف</th>
        <th >ملاحظات</th>
    </tr>
    </thead>
    <tbody>
        @foreach ($vouchers as $voucher)
            <tr>
                <td>{{$voucher['number']}}</td>
                <td>{{$voucher['store']}}</td>
                <td>
                    @if($voucher['amount'] < 0)
                        {{abs($voucher['amount'])}}
                    @endif
                </td>
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
