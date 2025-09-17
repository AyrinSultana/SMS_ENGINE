<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SMS Form</title>

    <!-- Bootstrap CSS (you can use a CDN or local file) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery (required for AJAX) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- Custom CSS (if any) -->
    <style>
        /* Add your custom styles here */
    </style>
</head>
<body>
    <!-- Header Section (optional) -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container">
                <!-- <a class="navbar-brand" href="#">SMS App</a> -->
            </div>
        </nav>
    </header>

    <!-- Main Content Section -->
    <main class="container mt-4">
        @yield('content') <!-- This is where the content from sms_form.blade.php will be injected -->
    </main>

    <!-- Footer Section (optional) -->
    <footer class="bg-light text-center py-3 mt-4">
        <p>&copy; 2023 SMS App. All rights reserved.</p>
    </footer>

    <!-- Bootstrap JS (optional, if you need Bootstrap functionality) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Scripts Section -->
    @yield('scripts') <!-- This is where the scripts from sms_form.blade.php will be injected -->
</body>
</html>