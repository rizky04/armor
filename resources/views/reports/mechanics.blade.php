@extends('layouts.main')

@section('content')
<h4>Laporan Mekanik</h4>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Mekanik</th>
            <th>Jumlah Service</th>
            <th>Service IDs</th>
        </tr>
    </thead>
    <tbody>
        @foreach($mechanics as $m)
        <tr>
            <td>{{ $m->mekanik }}</td>
            <td>{{ $m->jumlah_service }}</td>
            <td>{{ $m->service_ids }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
