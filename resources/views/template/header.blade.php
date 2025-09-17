<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>SMS Engine</title>
        <link rel="icon" type="image/png" href="{{ URL::to('/adminLTE/dist/img/sms.png') }}"/>
        <!-- ***-->
        <link rel="stylesheet" href="{{asset('adminLTE/plugins/fontawesome-free/css/all.min.css')}}">
        <!-- Theme style *** -->
        <link rel="stylesheet" href="{{asset('adminLTE/dist/css/adminlte.min.css')}}">
        <!-- DataTables  ***-->
        <link rel="stylesheet" href="{{asset('adminLTE/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
        <link rel="stylesheet" href="{{asset('adminLTE/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
        <link rel="stylesheet" href="{{asset('adminLTE/plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
        <link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}">

        {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous"> --}}
        {{-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css"> --}}
        <style type="text/css">
        a{
            text-decoration: none;
        }
        .sidebar-dark-primary {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        }
        .nav-sidebar .nav-item .nav-link {
            padding: 12px 15px;
            margin: 6px 10px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .nav-sidebar .nav-item .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }
        .nav-sidebar .nav-item .nav-link p {
            font-weight: 500;
        }
        .sidebar-heading {
            color: #c3d5f5;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 10px 25px;
            margin-top: 15px;
            margin-bottom: 5px;
        }
        .subgroup {
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            color: rgb(3, 1, 8);
            cursor: pointer;
            transition: background-color 0.3s; /* Added transition for smooth effect */
        }

        .subgroup:hover {
            background-color: #b6fa7a; /* Change the color on hover */
        }
        .nav-sidebar .nav-link p {
            margin-left: 5px;
        }
        .sidebar-dark-primary {
            background: linear-gradient(180deg, #2c3e50 0%, #1a2530 100%);
        }
        .nav-pills .nav-link {
            border-radius: .5rem;
            margin-bottom: 5px;
            transition: all 0.3s ease;
        }
        .nav-pills .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }
        .nav-pills .nav-link.active {
            background-color: #007bff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .sidebar-heading {
            color: #6c757d;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 10px 15px;
            margin-top: 10px;
        }
        .user-panel {
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        </style>

    </head>
    <body class="hold-transition sidebar-mini layout-fixed sidebar-collapse">
    <div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>

        </li>
            <span ><h5 class="mt-2 ml-2">SMS Engine(On Demand)</h5></span>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
        <!-- Navbar Search -->
        <li class="nav-item mb-2">
            <a class="nav-link" data-widget="navbar-search" href="#" role="button">
            <img src="{{ URL::to('/adminLTE/dist/img/222.png') }}" class="img-fluid mx-auto d-block" width="100" >
            </a>

        </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="" class="brand-link">

        <img src="{{ URL::to('/adminLTE/dist/img/sms.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">SMS Engine</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">

            <img src="{{ URL::to('/adminLTE/dist/img/avatar5.png') }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
            <a href="#" class="d-block">{{ session('name') }}</a>
            </div>
        </div>

        <!-- SidebarSearch Form -->

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            
            <!-- Dashboard -->
            <li class="nav-item">
                <a href="{{ route('template.index') }}" class="nav-link">
                    <i class="nav-icon fas fa-tachometer-alt text-info"></i>
                    <p>Dashboard</p>
                </a>
            </li>
            
            <!-- Template Management -->
            <div class="sidebar-heading">Template Management</div>
            <li class="nav-item">
                <a href="{{ route('template.index') }}" class="nav-link">
                    <i class="nav-icon fas fa-list-alt text-success"></i>
                    <p>View Templates</p>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('template.create') }}" class="nav-link">
                    <i class="nav-icon fas fa-plus-circle text-success"></i>
                    <p>Create Template</p>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('auth.list_template') }}" class="nav-link">
                    <i class="nav-icon fas fa-check-circle text-warning"></i>
                    <p>Approve Templates</p>
                </a>
            </li>
            
            <!-- SMS Management -->
            <div class="sidebar-heading">SMS Management</div>
            <li class="nav-item">
                <a href="{{ route('template.sms_form') }}" class="nav-link">
                    <i class="nav-icon fas fa-paper-plane text-primary"></i>
                    <p>Send SMS</p>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('waiting_list') }}" class="nav-link">
                    <i class="nav-icon fas fa-clock text-warning"></i>
                    <p>Pending SMS</p>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('auth.list') }}" class="nav-link">
                    <i class="nav-icon fas fa-check-double text-success"></i>
                    <p>Approve SMS</p>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('sms.history') }}" class="nav-link">
                    <i class="nav-icon fas fa-history text-info"></i>
                    <p>SMS History</p>
                </a>
            </li>
            
            <!-- System -->
            <div class="sidebar-heading">System</div>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-sign-out-alt text-danger"></i>
                    <p>Logout</p>
                </a>
            </li>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
        </aside>
        </body>
    </html>