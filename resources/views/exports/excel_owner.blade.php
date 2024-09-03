
<h4>Export To Excel Owner</h4>
<p>{{$tgl}}</p>
<table>
    <thead>
    <tr>
        <th>No</th>
        <th>Kode</th>
        <th>Name</th>
        <th>Branch</th>
        <th>Email</th>
        <th>Telpon</th>
        <th>Alamat</th>
        <th>Komunitas</th> 
        <th>Status</th> 
        <th>Created by</th> 
        <th>Updated by</th> 
    </tr>
    </thead>
    <tbody>
        @foreach ($data as $key => $item) 
            <tr>
                <td>{{++$key}}</td>
                <td>{{$item['kode']}}</td>
                <td>{{$item['name']}}</td>
                <td>{{$item['branch']}}</td>
                <td>{{$item['email']}}</td>
                <td>{{$item['telpon']}}</td>
                <td>{{$item['alamat']}}</td>
                <td>{{$item['komunitas']}}</td>
                <td>{{$item['status']}}</td>
                <td>{{$item['created_by']}}</td>
                <td>{{$item['updated_by']}}</td>
            </tr>
        @endforeach
    </tbody>
</table>