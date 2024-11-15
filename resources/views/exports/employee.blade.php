<table style="width: 100%; margin-top: 20px">
    <thead>
        <tr class="font-bold ">


            <th class="label">#</th>
            <th class="label">السند</th>
            <th class="label">المبلغ</th>
            <th class="label">التاريخ</th>
            <th class="label">المندوب</th>
            <th class="label">المسؤول</th>
            <th class="label">ملاحظات</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($vouchers as $voucher)
            <tr>
                <td>{{ $voucher['number'] }} </td>
                <td>{{ $voucher['type'] }} </td>
                <td> {{abs($voucher['amount'])}}</td>
                <td>{{ $voucher['created_at'] }} </td>
                <td>{{ $voucher['operator'] }} </td>
                <td>{{ $voucher['responsible'] }} </td>
                <td>{{ $voucher['notice'] }} </td>
            </tr>

        @endforeach

    </tbody>
</table>
