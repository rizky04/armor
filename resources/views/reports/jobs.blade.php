@extends('layouts.main')

@section('content')
<h4>Laporan Pekerjaan</h4>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Kendaraan</th>
            <th>Customer</th>
            <th>Keluhan</th>
            <th>Pekerjaan</th>
            <th>Mekanik</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($jobs as $s)
            @foreach($s->jobs as $job)
            <tr>
                <td>{{ $s->service_date }}</td>
                <td>{{ $s->vehicle->plate_number }}</td>
                <td>{{ $s->vehicle->customer->name ?? '-' }}</td>
                <td>{{ $s->complaint }}</td>
                <td>{{ $job->description }}</td>
                <td>{{ $s->mechanics->pluck('name')->join(', ') }}</td>
                <td>{{ $s->status }}</td>
            </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
@endsection
