<html>
<body>
    

<div class="content-wrapper">
        <div class="content-header">
        <div class="container-fluid">
        <div class="row mb-2">
        <div class="container mt-4  table-responsive">
          
    <div class="container p-5 my-4  ">
    <h3>Select Branch Name</h3><br>
    

   
    </div>

    </div>
    </div>
    </div><!-- /.row -->
    </div><!-- /.container-fluid -->
    </div>

      <footer class="main-footer">
        <div class="float-right d-none d-sm-block">
        <b>Version</b> 1.0.0
        </div>
        <strong>Copyright &copy; 2024 <a href="#">IFIC Bank PLC</a>.</strong>All rights reserved.
    </footer>

    </div>
    <script>
    $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="{{asset('adminLTE/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{asset('adminLTE/plugins/jquery/jquery.min.js') }}"></script>
    {{--  --}}

    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}

    <!-- AdminLTE App -->
    <script src="{{asset('adminLTE/dist/js/adminlte.min.js') }}"></script>
    <script src="{{asset('adminLTE/plugins/summernote/summernote-bs4.min.js') }}"></script>

    <!-- Datatables App -->
    <script src="{{asset('adminLTE/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{asset('adminLTE/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{asset('adminLTE/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{asset('adminLTE/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    {{-- <script src="{{asset('js/jquery-3.5.1.slim.min.js')}}"></script>
    <script src="{{asset('js/popper.min.js')}}"></script> --}}
    <script src="{{asset('js/bootstrap.bundle.min.js')}}"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script> --}}
    
    <script>
    $(function () {
        $("#example1").DataTable({
        "responsive": false, "lengthChange": true, "autoWidth": true,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        $('#example2').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        });
    });

    </script>

    </body>
    </html>