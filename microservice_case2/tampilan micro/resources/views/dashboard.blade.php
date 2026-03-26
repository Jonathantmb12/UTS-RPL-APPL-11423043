@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="h3 d-flex align-items-center gap-2">
                <i class="bi bi-speedometer2"></i>
                Dashboard
            </h1>
            <p class="text-muted mb-0">Welcome back, {{ Auth::user()->name }}!</p>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Total Patients</p>
                            <h5 class="mb-0">8</h5>
                        </div>
                        <div style="background-color: #667eea; color: white; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                    <small class="text-success mt-2 d-block">↑ 2 this month</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Total Doctors</p>
                            <h5 class="mb-0">4</h5>
                        </div>
                        <div style="background-color: #28a745; color: white; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                            <i class="bi bi-person-badge"></i>
                        </div>
                    </div>
                    <small class="text-muted mt-2 d-block">All active</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Total Appointments</p>
                            <h5 class="mb-0">10</h5>
                        </div>
                        <div style="background-color: #ffc107; color: white; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                    </div>
                    <small class="text-muted mt-2 d-block">5 scheduled</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Total Prescriptions</p>
                            <h5 class="mb-0">15</h5>
                        </div>
                        <div style="background-color: #17a2b8; color: white; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                            <i class="bi bi-capsule"></i>
                        </div>
                    </div>
                    <small class="text-muted mt-2 d-block">12 active</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Overview -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom px-4 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-graph-up me-2"></i>Appointment Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <p class="text-muted small mb-1">Scheduled</p>
                            <h5 class="mb-0 text-warning">5</h5>
                        </div>
                        <div class="col-md-4">
                            <p class="text-muted small mb-1">Completed</p>
                            <h5 class="mb-0 text-success">4</h5>
                        </div>
                        <div class="col-md-4">
                            <p class="text-muted small mb-1">Cancelled</p>
                            <h5 class="mb-0 text-danger">1</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom px-4 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-graph-up me-2"></i>Prescription Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <p class="text-muted small mb-1">Active</p>
                            <h5 class="mb-0 text-success">12</h5>
                        </div>
                        <div class="col-md-4">
                            <p class="text-muted small mb-1">Expired</p>
                            <h5 class="mb-0 text-danger">2</h5>
                        </div>
                        <div class="col-md-4">
                            <p class="text-muted small mb-1">Cancelled</p>
                            <h5 class="mb-0 text-secondary">1</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom px-4 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock-history me-2"></i>Recent Activities
                    </h5>
                </div>
                <div class="card-body">
                    <div class="activity-list">
                        <div class="activity-item d-flex gap-3 pb-3 border-bottom">
                            <div class="activity-icon bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; min-width: 40px;">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-1"><strong>Appointment Completed</strong></p>
                                <p class="text-muted small mb-0">Ahmad Gunawan - Dr. Bambang Irawan • 2 hours ago</p>
                            </div>
                        </div>
                        <div class="activity-item d-flex gap-3 pb-3 border-bottom">
                            <div class="activity-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; min-width: 40px;">
                                <i class="bi bi-plus-circle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-1"><strong>New Prescription Created</strong></p>
                                <p class="text-muted small mb-0">Nur Azizah - Ibuprofen 400mg • 4 hours ago</p>
                            </div>
                        </div>
                        <div class="activity-item d-flex gap-3 pb-3 border-bottom">
                            <div class="activity-icon bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; min-width: 40px;">
                                <i class="bi bi-person-plus"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-1"><strong>New Patient Registered</strong></p>
                                <p class="text-muted small mb-0">Siti Nurhaliza • 1 day ago</p>
                            </div>
                        </div>
                        <div class="activity-item d-flex gap-3">
                            <div class="activity-icon bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; min-width: 40px;">
                                <i class="bi bi-exclamation-circle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-1"><strong>Low Stock Alert</strong></p>
                                <p class="text-muted small mb-0">Amoxicillin below reorder level • 2 days ago</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom px-4 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calendar-event me-2"></i>Upcoming Appointments
                    </h5>
                </div>
                <div class="card-body">
                    <div class="upcoming-list">
                        <div class="upcoming-item d-flex justify-content-between align-items-start pb-3 border-bottom mb-3">
                            <div>
                                <p class="mb-1"><strong>Budi Santoso</strong></p>
                                <p class="text-muted small mb-0">Dr. Bambang Irawan (Cardiology)</p>
                                <p class="text-muted small mb-0"><i class="bi bi-calendar"></i> Mar 25, 2025 - 10:00 AM</p>
                            </div>
                            <span class="badge bg-warning">Scheduled</span>
                        </div>
                        <div class="upcoming-item d-flex justify-content-between align-items-start pb-3 border-bottom mb-3">
                            <div>
                                <p class="mb-1"><strong>Ahmad Gunawan</strong></p>
                                <p class="text-muted small mb-0">Dr. Sri Indah Lestari (Pediatrics)</p>
                                <p class="text-muted small mb-0"><i class="bi bi-calendar"></i> Mar 26, 2025 - 02:00 PM</p>
                            </div>
                            <span class="badge bg-info">Confirmed</span>
                        </div>
                        <div class="upcoming-item d-flex justify-content-between align-items-start">
                            <div>
                                <p class="mb-1"><strong>Nur Azizah</strong></p>
                                <p class="text-muted small mb-0">Dr. Bambang Irawan (Cardiology)</p>
                                <p class="text-muted small mb-0"><i class="bi bi-calendar"></i> Mar 28, 2025 - 11:30 AM</p>
                            </div>
                            <span class="badge bg-warning">Scheduled</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
