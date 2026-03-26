@extends('layouts.app')

@section('title', 'Inventory Farmasi')

@section('content')
<div class="container">
    <h3 class="mb-4">Inventory Obat</h3>

    <div class="card">
        <div class="card-header">
            Daftar Obat
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Nama Obat</th>
                        <th>Stok</th>
                        <th>Harga</th>
                        <th>Expired</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inventory as $item)
                    <tr>
                        <td>{{ $item->medication_name }}</td>
                        <td>{{ $item->stock_quantity }}</td>
                        <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td>{{ $item->expiration_date->format('d-m-Y') }}</td>
                        <td>
                            @if($item->stock_quantity <= $item->reorder_level)
                                <span class="badge bg-danger">Stok Rendah</span>
                            @else
                                <span class="badge bg-success">Aman</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection