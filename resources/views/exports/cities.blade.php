<table>
    <thead>
    <tr>
        <th >الإسم بالعربية </th>
        <th >الإسم بالأنجليزية </th>
        <th >سعر التوصيل </th>
        <th>كود طرود</th>

    </tr>
    </thead>
    <tbody>
        @foreach ($cities as $city)
            <tr>
                <td>{{$city['name']}}</td>
                <td>{{$city['name_en']}}</td>
                <td>tier{{$city['fee_range']}}</td>
                <td>{{$city['code']}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
