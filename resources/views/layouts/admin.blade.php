<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title')</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        
        <style>
            :root {
                --sb-sidenav-bg: #212529;
                --sb-sidenav-width: 250px;
            }
            
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            html, body {
                height: 100%;
                background-color: #f8f9fa;
            }
            
            body {
                background-color: #f8f9fa;
            }
            
            .sb-nav-fixed {
                padding-top: 56px;
                display: flex;
                flex-direction: row;
                min-height: 100vh;
            }
            
            .sb-topnav {
                background-color: #212529 !important;
                height: 56px;
                display: flex;
                align-items: center;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                z-index: 1030;
                padding: 0 20px;
            }
            
            .sb-topnav .navbar-brand {
                font-size: 18px;
                font-weight: bold;
                color: white !important;
                margin: 0;
            }
            
            #layoutSidenav {
                display: flex;
                width: 100%;
                flex: 1;
            }
            
            #layoutSidenav_nav {
                width: var(--sb-sidenav-width);
                background-color: var(--sb-sidenav-bg);
                overflow-y: auto;
                overflow-x: hidden;
                flex-shrink: 0;
                position: fixed;
                top: 56px;
                left: 0;
                bottom: 0;
                height: calc(100vh - 56px);
                z-index: 1020;
            }
            
            #layoutSidenav_content {
                flex: 1;
                display: flex;
                flex-direction: column;
                overflow-y: auto;
                margin-left: var(--sb-sidenav-width);
                width: calc(100% - var(--sb-sidenav-width));
            }
            
            main {
                flex: 1;
                padding: 20px 30px;
            }
            
            main .container-fluid {
                max-width: 100%;
                padding: 0;
            }
            
            .sb-sidenav {
                background-color: #212529;
                padding: 0;
                margin: 0;
            }
            
            .sb-sidenav-menu {
                padding: 15px 0;
                margin: 0;
            }
            
            .sb-sidenav-menu-heading {
                padding: 12px 20px;
                font-size: 10px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.05rem;
                color: #888;
                margin: 15px 0 5px 0;
            }
            
            .sb-sidenav-menu-heading:first-child {
                margin-top: 0;
                text-align: center;
                padding: 20px 15px;
                border-bottom: 1px solid #444;
            }
            
            .sb-sidenav-menu-heading:first-child div:first-div {
                font-size: 18px;
                font-weight: bold;
                color: white;
                margin-bottom: 5px;
            }
            
            .sb-sidenav-menu-heading:first-child div:last-child {
                font-size: 11px;
                color: #aaa;
                font-weight: normal;
            }
            
            .sb-sidenav .nav {
                margin: 0;
                padding: 0;
            }
            
            .sb-sidenav .nav-link {
                color: #adb5bd;
                padding: 12px 20px;
                display: flex;
                align-items: center;
                text-decoration: none;
                transition: all 0.3s;
                border-left: 3px solid transparent;
                margin: 0;
                border-radius: 0;
                font-size: 14px;
                line-height: 1.5;
            }
            
            .sb-sidenav .nav-link:hover,
            .sb-sidenav .nav-link.active {
                color: white;
                background-color: rgba(255, 255, 255, 0.1);
                border-left-color: #0d6efd;
            }
            
            .sb-sidenav button.nav-link {
                padding: 12px 20px;
                text-align: left;
                background: none;
                border: none;
                cursor: pointer;
                font-size: 14px;
                color: #adb5bd;
                display: flex;
                align-items: center;
                width: 100%;
                transition: all 0.3s;
                border-left: 3px solid transparent;
                margin: 0;
            }
            
            .sb-sidenav button.nav-link:hover {
                color: white;
                background-color: rgba(255, 255, 255, 0.1);
                border-left-color: #0d6efd;
            }
            
            .sb-nav-link-icon {
                margin-right: 12px;
                width: 16px;
                text-align: center;
                flex-shrink: 0;
            }
            
            footer {
                margin-top: auto;
                padding: 15px 20px;
                background-color: white;
                border-top: 1px solid #dee2e6;
                font-size: 12px;
                color: #6c757d;
                text-align: center;
            }
            
            .card {
                border: none;
                box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
                margin-bottom: 20px;
            }
            
            .card-header {
                background-color: #0d6efd;
                color: white;
                padding: 15px 20px;
                border: none;
                font-weight: 600;
            }
            
            .card-body {
                padding: 20px;
            }
            
            .table {
                margin: 0;
            }
            
            .table thead th {
                background-color: #f8f9fa;
                border-bottom: 2px solid #dee2e6;
                padding: 12px;
            }
            
            .table tbody td {
                padding: 12px;
                vertical-align: middle;
            }
            
            .form-label {
                font-weight: 500;
                margin-bottom: 8px;
                color: #333;
                font-size: 14px;
            }
            
            .form-control,
            .form-select {
                border: 1px solid #dee2e6;
                border-radius: 4px;
                padding: 10px 12px;
                font-size: 14px;
            }
            
            .form-control:focus,
            .form-select:focus {
                border-color: #0d6efd;
                box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
            }
            
            .btn {
                padding: 8px 16px;
                border-radius: 4px;
                font-weight: 500;
                transition: all 0.3s;
                font-size: 14px;
            }
            
            .btn-primary {
                background-color: #0d6efd;
                border-color: #0d6efd;
            }
            
            .btn-primary:hover {
                background-color: #0b5ed7;
                border-color: #0b5ed7;
            }
            
            .alert {
                border-radius: 4px;
                border: none;
                padding: 12px 16px;
                margin-bottom: 20px;
            }
            
            h1, h2, h3, h4, h5, h6 {
                margin-bottom: 15px;
            }
            
            /* Mobile Responsive */
            @media (max-width: 768px) {
                :root {
                    --sb-sidenav-width: 0;
                }
                
                .sb-nav-fixed {
                    flex-direction: column;
                }
                
                #layoutSidenav {
                    flex-direction: column;
                    margin-top: 56px;
                }
                
                #layoutSidenav_nav {
                    width: 100%;
                    transform: translateX(-100%);
                    position: fixed;
                    left: 0;
                    top: 56px;
                    height: calc(100vh - 56px);
                    transition: transform 0.3s;
                    z-index: 1019;
                }
                
                #layoutSidenav_nav.show {
                    transform: translateX(0);
                }
                
                #layoutSidenav_content {
                    width: 100%;
                }
                
                main {
                    padding: 15px 15px;
                }
                
                .container-fluid {
                    padding-left: 0 !important;
                    padding-right: 0 !important;
                }
            }
        </style>
    </head>
    <body class="sb-nav-fixed">

    @include('layouts.partials.navbar')

    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            @include('layouts.partials.sidebar')
        </div>

        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid">
                    @yield('content')
                </div>
            </main>

            @include('layouts.partials.footer')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    </body>
</html>
