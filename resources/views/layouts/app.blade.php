<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'BluRay National ERP') - {{ config('app.name') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary-color: #2c5aa0;
            --secondary-color: #f8f9fa;
            --accent-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f6fa;
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color) !important;
        }
        
        .sidebar {
            background: linear-gradient(180deg, var(--primary-color) 0%, #1e3a5f 100%);
            min-height: calc(100vh - 56px);
            padding: 0;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            border-radius: 0;
            margin: 2px 10px;
            padding: 10px 15px;
        }
        
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: rgba(255,255,255,0.1);
            color: white;
        }
        
        .content-wrapper {
            background: white;
            margin: 20px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.08);
            padding: 25px;
        }
        
        .page-header {
            border-bottom: 2px solid #e9ecef;
            margin-bottom: 25px;
            padding-bottom: 15px;
        }
        
        .page-title {
            color: var(--primary-color);
            font-weight: 600;
            margin: 0;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #1e3a5f;
            border-color: #1e3a5f;
        }
        
        .status-badge {
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .table thead th {
            background-color: var(--secondary-color);
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .card {
            border: none;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
            border-radius: 10px;
        }
        
        .card-header {
            background: var(--secondary-color);
            border-bottom: 1px solid #dee2e6;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(44, 90, 160, 0.25);
        }
        
        .navbar {
            background: white !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-cube me-2"></i>BluRay National ERP
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i>{{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user-cog me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item" type="submit">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    @auth
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        
                        <!-- Sales Module -->
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="collapse" href="#salesMenu">
                                <i class="fas fa-shopping-cart me-2"></i>Sales <i class="fas fa-chevron-down ms-auto"></i>
                            </a>
                            <div class="collapse" id="salesMenu">
                                <ul class="nav flex-column ms-3">
                                @if(false)
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('delivery_orders.create') }}">
                                            <i class="fas fa-plus me-2"></i>New Delivery Order
                                        </a>
                                    </li>
                                      <li class="nav-item">
                                        <a class="nav-link" href="{{ route('reports.sales-payment-report') }}">
                                            <i class="fas fa-money-check-alt me-2"></i>Sales Payment Report
                                        </a>
                                    </li>
                                     @endif
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('delivery_orders.index') }}">
                                            <i class="fas fa-truck me-2"></i>Delivery Orders
                                        </a>
                                    </li>
                               
                                  
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('quotations.create') }}">
                                            <i class="fas fa-plus me-2"></i>New Quotation
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('quotations.index') }}">
                                            <i class="fas fa-list me-2"></i>Quotation List
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('sales.index') }}">
                                            <i class="fas fa-receipt me-2"></i>Sales Invoices
                                        </a>
                                    </li>
                                @if(false)
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('deliveries.create') }}">
                                            <i class="fas fa-plus me-2"></i>New Delivery Note
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('deliveries.index') }}">
                                            <i class="fas fa-truck me-2"></i>Delivery Notes
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#">
                                            <i class="fas fa-undo me-2"></i>Sales Return
                                        </a>
                                    </li>
                                @endif
                                </ul>
                            </div>
                        </li>
                        
                        <!-- Purchase Module -->
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="collapse" href="#purchaseMenu">
                                <i class="fas fa-shopping-bag me-2"></i>Purchase <i class="fas fa-chevron-down ms-auto"></i>
                            </a>
                            <div class="collapse" id="purchaseMenu">
                                <ul class="nav flex-column ms-3">
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('purchase-orders.index') }}">
                                            <i class="fas fa-file-alt me-2"></i>Purchase Orders
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('grns.index') }}">
                                            <i class="fas fa-box me-2"></i>GRN (Goods Receipt)
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#">
                                            <i class="fas fa-file-invoice-dollar me-2"></i>Purchase Invoices
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#">
                                            <i class="fas fa-exchange-alt me-2"></i>Purchase Return
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        
                        <!-- Inventory/Stock Management -->
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="collapse" href="#inventoryMenu">
                                <i class="fas fa-boxes me-2"></i>Inventory <i class="fas fa-chevron-down ms-auto"></i>
                            </a>
                            <div class="collapse" id="inventoryMenu">
                                <ul class="nav flex-column ms-3">
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('items.index') }}">
                                            <i class="fas fa-list me-2"></i>Items
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('items.create') }}">
                                            <i class="fas fa-plus me-2"></i>Add New Item
                                        </a>
                                    </li>
                                @if(false)
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('stock.report') }}">
                                            <i class="fas fa-chart-bar me-2"></i>Stock Report
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('stock.low_stock') }}">
                                            <i class="fas fa-exclamation-triangle me-2"></i>Low Stock Alert
                                        </a>
                                    </li>
                                @endif
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('inventory.damage.create') }}">
                                            <i class="fas fa-exclamation-triangle text-danger me-2"></i>Damage Stock
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('inventory.damage.history') }}">
                                            <i class="fas fa-history me-2"></i>Damage History
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('units.index') }}">
                                            <i class="fas fa-ruler-combined me-2"></i>Units
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('services.index') }}">
                                            <i class="fas fa-concierge-bell me-2"></i>Services
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('units.index') }}">
                                            <i class="fas fa-ruler-combined me-2"></i>Units
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        
                        <!-- Job Cards/Service Management -->
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="collapse" href="#jobCardMenu">
                                <i class="fas fa-wrench me-2"></i>Job Cards <i class="fas fa-chevron-down ms-auto"></i>
                            </a>
                            <div class="collapse" id="jobCardMenu">
                                <ul class="nav flex-column ms-3">
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('job-cards.index') }}">
                                            <i class="fas fa-list me-2"></i>Job Cards
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('job-cards.create') }}">
                                            <i class="fas fa-plus me-2"></i>New Job Card
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        @if(false)
                        <!-- AMC Service Management -->
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="collapse" href="#amcMenu">
                                <i class="fas fa-shield-alt me-2"></i>AMC Service <i class="fas fa-chevron-down ms-auto"></i>
                            </a>
                            <div class="collapse" id="amcMenu">
                                <ul class="nav flex-column ms-3">
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('amc-services.index') }}">
                                            <i class="fas fa-list me-2"></i>AMC List
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('amc-services.create') }}">
                                            <i class="fas fa-plus me-2"></i>New AMC
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        @endif
                        
                        @if(false)
                        <!-- Expense Management -->
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="collapse" href="#expenseMenu">
                                <i class="fas fa-money-bill-wave me-2"></i>Expense <i class="fas fa-chevron-down ms-auto"></i>
                            </a>
                            <div class="collapse" id="expenseMenu">
                                <ul class="nav flex-column ms-3">
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('expenses.index') }}">
                                            <i class="fas fa-list me-2"></i>All Expenses
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('expenses.create') }}">
                                            <i class="fas fa-plus me-2"></i>Add Expense
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('expense-categories.index') }}">
                                            <i class="fas fa-tags me-2"></i>Categories
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('expense-sub-categories.index') }}">
                                            <i class="fas fa-layer-group me-2"></i>Subcategories
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        @endif
                        
                        @if(false)
                        <!-- Reports -->
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="collapse" href="#reportsMenu">
                                <i class="fas fa-chart-line me-2"></i>Reports <i class="fas fa-chevron-down ms-auto"></i>
                            </a>
                            <div class="collapse" id="reportsMenu">
                                <ul class="nav flex-column ms-3">
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('reports.account-summary') }}">
                                            <i class="fas fa-calculator me-2"></i>Account Summary
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('reports.purchase-report') }}">
                                            <i class="fas fa-shopping-cart me-2"></i>Purchase Report
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('reports.purchase-return-report') }}">
                                            <i class="fas fa-undo me-2"></i>Purchase Return Report
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('reports.purchase-payment-report') }}">
                                            <i class="fas fa-credit-card me-2"></i>Purchase Payment Report
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('reports.sales-item-report') }}">
                                            <i class="fas fa-chart-bar me-2"></i>Sales Item Report
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('reports.item-purchase-report') }}">
                                            <i class="fas fa-boxes me-2"></i>Item Purchase Report
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('reports.sales-report') }}">
                                            <i class="fas fa-file-invoice-dollar me-2"></i>Sales Report
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('reports.sales-return-report') }}">
                                            <i class="fas fa-undo-alt me-2"></i>Sales Return Report
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('reports.sales-payment-report') }}">
                                            <i class="fas fa-money-check-alt me-2"></i>Sales Payment Report
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        @endif
                        
                        <!-- Masters -->
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="collapse" href="#mastersMenu">
                                <i class="fas fa-database me-2"></i>Masters <i class="fas fa-chevron-down ms-auto"></i>
                            </a>
                            <div class="collapse" id="mastersMenu">
                                <ul class="nav flex-column ms-3">
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('customers.index') }}">
                                            <i class="fas fa-users me-2"></i>Customers
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('customers.create') }}">
                                            <i class="fas fa-user-plus me-2"></i>Add Customer
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('suppliers.index') }}">
                                            <i class="fas fa-truck me-2"></i>Suppliers
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('suppliers.create') }}">
                                            <i class="fas fa-truck-loading me-2"></i>Add Supplier
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="content-wrapper">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    @else
    <div class="container mt-5">
        @yield('content')
    </div>
    @endauth

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // Global DataTable configuration
        $.extend(true, $.fn.dataTable.defaults, {
            responsive: true,
            processing: true,
            pageLength: 25,
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                 "<'row'<'col-sm-12 col-md-2'B>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            buttons: [
                {
                    extend: 'excel',
                    className: 'btn btn-success btn-sm me-1',
                    text: '<i class="fas fa-file-excel me-1"></i>Excel'
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-danger btn-sm me-1',
                    text: '<i class="fas fa-file-pdf me-1"></i>PDF'
                },
                {
                    extend: 'print',
                    className: 'btn btn-info btn-sm',
                    text: '<i class="fas fa-print me-1"></i>Print'
                }
            ],
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            }
        });

        // Initialize Select2
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        });

        // Global CSRF setup for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
