@extends('layouts.app')

@section('title', 'Pharmacy Orders')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 d-flex align-items-center gap-2">
                <i class="bi bi-bag-check"></i>
                Prescription Orders
            </h1>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Total Orders</p>
                            <h5 class="mb-0">15</h5>
                        </div>
                        <div class="stat-icon" style="background-color: #667eea; color: white; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                            <i class="bi bi-bag"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Pending</p>
                            <h5 class="mb-0">5</h5>
                        </div>
                        <div class="stat-icon" style="background-color: #ffc107; color: white; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                            <i class="bi bi-hourglass"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Completed</p>
                            <h5 class="mb-0">9</h5>
                        </div>
                        <div class="stat-icon" style="background-color: #28a745; color: white; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Cancelled</p>
                            <h5 class="mb-0">1</h5>
                        </div>
                        <div class="stat-icon" style="background-color: #dc3545; color: white; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                            <i class="bi bi-x-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by patient name or medication...">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">-- All Status --</option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" name="date" class="form-control">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="card-title mb-0">
                <i class="bi bi-list-check me-2"></i>Recent Orders
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Order Date</th>
                            <th>Patient</th>
                            <th>Medication</th>
                            <th>Dosage</th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $orders = [
                            ['id' => 1, 'date' => 'Mar 20, 2025', 'patient' => 'Budi Santoso', 'medication' => 'Ibuprofen', 'dosage' => '400mg', 'qty' => 30, 'status' => 'pending'],
                            ['id' => 2, 'date' => 'Mar 19, 2025', 'patient' => 'Siti Nurhaliza', 'medication' => 'Amoxicillin', 'dosage' => '500mg', 'qty' => 20, 'status' => 'completed'],
                            ['id' => 3, 'date' => 'Mar 19, 2025', 'patient' => 'Ahmad Gunawan', 'medication' => 'Omeprazole', 'dosage' => '20mg', 'qty' => 14, 'status' => 'completed'],
                            ['id' => 4, 'date' => 'Mar 18, 2025', 'patient' => 'Nur Azizah', 'medication' => 'Metformin', 'dosage' => '850mg', 'qty' => 30, 'status' => 'pending'],
                            ['id' => 5, 'date' => 'Mar 18, 2025', 'patient' => 'Bambang Irawan', 'medication' => 'Atorvastatin', 'dosage' => '10mg', 'qty' => 30, 'status' => 'completed'],
                        ];
                        @endphp
                        @foreach($orders as $order)
                        <tr>
                            <td>{{ $order['date'] }}</td>
                            <td>
                                <strong>{{ $order['patient'] }}</strong>
                            </td>
                            <td>{{ $order['medication'] }}</td>
                            <td>{{ $order['dosage'] }}</td>
                            <td>{{ $order['qty'] }} units</td>
                            <td>
                                @php
                                    $statusClass = match($order['status']) {
                                        'pending' => 'bg-warning',
                                        'completed' => 'bg-success',
                                        'cancelled' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ ucfirst($order['status']) }}</span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group" role="group">
                                    <a href="#" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($order['status'] == 'pending')
                                        <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="tooltip" title="Mark Complete">
                                            <i class="bi bi-check"></i>
                                        </button>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <nav aria-label="Page navigation" class="mt-3">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">Next</a></li>
                </ul>
            </nav>
        </div>
    </div>
</div>
@endsection
