@extends('layouts.main')

@section('content')
    <div class="page-header">
        <div class="page-title">
            <h4>Laporan Service</h4>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Total Service</th>
                        <th>Menunggu</th>
                        <th>Proses</th>
                        <th>Selesai</th>
                        <th>Diambil</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reports as $r)
                        <tr>
                            <td>{{ $r->tanggal }}</td>
                            <td>{{ $r->total }}</td>
                            <td>{{ $r->menunggu }}</td>
                            <td>{{ $r->proses }}</td>
                            <td>{{ $r->selesai }}</td>
                            <td>{{ $r->diambil }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
