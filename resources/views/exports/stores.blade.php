<table>
    <thead>
        <tr>
            <th>رقم المتجر</th>
            <th>اسم المتجر</th>
            <th>اسم صاحب المتجر</th>
            <th>رقم الجوال</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($stores as $store)
        <tr>
            <td>{{$store['id']}}</td>
            <td>{{$store['name']}}</td>
            <td>{{$store['ownerName']}}</td>
            <td>{{$store['phone']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>