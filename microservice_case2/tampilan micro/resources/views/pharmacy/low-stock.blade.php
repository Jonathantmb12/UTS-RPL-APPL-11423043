@extends('layouts.app')

@section('title', 'Low Stock Alerts')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle"></i>
                Low Stock Alerts
            </h1>
            <p class="text-muted mb-0">Medications below reorder level</p>
        </div>
    </div>

    <!-- Alert Summary -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Total Low Stock Items</p>
                            <h5 class="mb-0">8</h5>
                        </div>
                        <div style="background-color: #dc3545; color: white; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                            <i class="bi bi-box-seam"></i>
                        </div>
                    </div>
                    <small class="text-muted mt-2 d-block">Requires immediate action</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Critical Stock</p>
                            <h5 class="mb-0">3</h5>
                        </div>
                        <div style="background-color: #ff6b6b; color: white; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                            <i class="bi bi-exclamation-circle"></i>
                        </div>
                    </div>
                    <small class="text-muted mt-2 d-block">Below 5 units</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Pending Orders</p>
                            <h5 class="mb-0">5</h5>
                        </div>
                        <div style="background-color: #ffc107; color: white; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                            <i class="bi bi-hourglass"></i>
                        </div>
                    </div>
                    <small class="text-muted mt-2 d-block">On the way</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Search by medication name...">
                </div>
                <div class="col-md-4">
                    <select name="severity" class="form-select">
                        <option value="">-- All Severity --</option>
                        <option value="critical">Critical (< 5 units)</option>
                        <option value="warning">Warning (5-10 units)</option>
                        <option value="low">Low Stock</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Low Stock Items Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="card-title mb-0">
                <i class="bi bi-list-check me-2"></i>Items Below Reorder Level
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Medication</th>
                            <th>Generic Name</th>
                            <th>Current Stock</th>
                            <th>Reorder Level</th>
                            <th>Expiration</th>
                            <th>Severity</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $lowStockItems = [
                            ['id' => 1, 'name' => 'Amoxicillin', 'generic' => 'Amoxicillin', 'stock' => 3, 'reorder' => 20, 'expiry' => 'Jun 15, 2025', 'severity' => 'critical'],
                            ['id' => 2, 'name' => 'Metformin', 'generic' => 'Metformin Hydrochloride', 'stock' => 8, 'reorder' => 25, 'expiry' => 'Aug 20, 2025', 'severity' => 'warning'],
                            ['id' => 3, 'name' => 'Lisinopril', 'generic' => 'Lisinopril', 'stock' => 2, 'reorder' => 15, 'expiry' => 'May 10, 2025', 'severity' => 'critical'],
                            ['id' => 4, 'name' => 'Omeprazole', 'generic' => 'Omeprazole', 'stock' => 12, 'reorder' => 20, 'expiry' => 'Jul 30, 2025', 'severity' => 'warning'],
                            ['id' => 5, 'name' => 'Ibuprofen', 'generic' => 'Ibuprofen', 'stock' => 4, 'reorder' => 50, 'expiry' => 'Dec 15, 2025', 'severity' => 'critical'],
                            ['id' => 6, 'name' => 'Atorvastatin', 'generic' => 'Atorvastatin Calcium', 'stock' => 18, 'reorder' => 30, 'expiry' => 'Sep 25, 2025', 'severity' => 'warning'],
                            ['id' => 7, 'name' => 'Clopidogrel', 'generic' => 'Clopidogrel Bisulfate', 'stock' => 5, 'reorder' => 20, 'expiry' => 'Nov 30, 2025', 'severity' => 'warning'],
                            ['id' => 8, 'name' => 'Azithromycin', 'generic' => 'Azithromycin', 'stock' => 1, 'reorder' => 10, 'expiry' => 'Apr 05, 2025', 'severity' => 'critical'],
                        ];
                        @endphp
                        @foreach($lowStockItems as $item)
                        <tr>
                            <td>
                                <strong>{{ $item['name'] }}</strong>
                            </td>
                            <td>
                                <small class="text-muted">{{ $item['generic'] }}</small>
                            </td>
                            <td>
                                <span class="badge bg-danger">{{ $item['stock'] }} units</span>
                            </td>
                            <td>{{ $item['reorder'] }} units</td>
                            <td>{{ $item['expiry'] }}</td>
                            <td>
                                @php
                                    $severityClass = match($item['severity']) {
                                        'critical' => 'bg-danger',
                                        'warning' => 'bg-warning',
                                        default => 'bg-secondary'
                                    };
                                    $severityIcon = match($item['severity']) {
                                        'critical' => 'exclamation-circle',
                                        'warning' => 'exclamation-triangle',
                                        default => 'info-circle'
                                    };
                                @endphp
                                <span class="badge {{ $severityClass }}">
                                    <i class="bi bi-{{ $severityIcon }} me-1"></i>{{ ucfirst($item['severity']) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#orderModal{{ $item['id'] }}" title="Place Order">
                                        <i class="bi bi-cart-plus"></i>
                                    </button>
                                    <a href="#" class="btn btn-sm btn-outline-info" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Order Recommendations -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom px-4 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lightbulb me-2"></i>Recommendations
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <strong>Urgent Order Needed:</strong>
                            Amoxicillin (currently 3 units) - placed order by today
                        </li>
                        <li class="list-group-item">
                            <strong>Critical Inventory:</strong>
                            Azithromycin (1 unit) and Lisinopril (2 units) - immediate restock required
                        </li>
                        <li class="list-group-item">
                            <strong>Monitor Expiry:</strong>
                            Several items expiring within next 2 months - consider rotation
                        </li>
                        <li class="list-group-item">
                            <strong>Supplier Contact:</strong>
                            <a href="mailto:supplier@pharmacy.com">supplier@pharmacy.com</a> - typically 2-3 days delivery
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Modals -->
@foreach($lowStockItems as $item)
<div class="modal fade" id="orderModal{{ $item['id'] }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Place Order for {{ $item['name'] }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form>
                <div class="modal-body">
                    <p class="mb-3"><strong>Current Stock:</strong> {{ $item['stock'] }} units</p>
                    <p class="mb-3"><strong>Reorder Level:</strong> {{ $item['reorder'] }} units</p>
                    <div class="mb-3">
                        <label class="form-label">Order Quantity</label>
                        <input type="number" class="form-control" value="{{ $item['reorder'] * 2 }}" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estimated Delivery</label>
                        <input type="date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Supplier</label>
                        <select class="form-select">
                            <option>Primary Supplier</option>
                            <option>Secondary Supplier</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Place Order</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection
