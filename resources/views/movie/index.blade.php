<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Movie List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/waitMe.css') }}" />
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <h1 class="mb-4">Movie List</h1>
                <a href="#!" class="btn btn-primary mb-3">Add Movie</a>
                <table class="table table-striped table-hover data-table" id="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Title</th>
                            <th scope="col">Year</th>
                            <th scope="col">Rating</th>
                            <th scope="col">URL</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{asset('assets/js/waitMe.js')}}"></script>
    <script src="{{asset('assets/js/toastr.min.js')}}"></script>
    
    <script>
        // full_page();
        function full_page() {
                $('.container').waitMe({
                    effect: 'bounce',
                    text: '',
                    bg: 'rgba(255,255,255,0.7)',
                    color: '#000',
                    maxSize: '',
                    waitTime: -1,
                    textPos: 'vertical',
                    fontSize: '',
                    source: '',
                    onClose: function() {}
                });
            }

        $(function () {
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('movie') }}",
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'title', name: 'title' },
                    { data: 'year', name: 'year' },
                    { data: 'rating', name: 'rating' },
                    { data: 'url', name: 'url' },
                   
                ]
            });
        });
        // toastr.success('asas');
        $(document).on('click','.mb-3',function(e){
            e.preventDefault();

                $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
             });
             full_page();
            $.ajax({
            type: "POST",
            url: "{{route('scrape')}}",
            // data: formData,
            success: function(response) {
                console.log(response);
                $('.container').waitMe('hide');
                var table = $('.data-table').DataTable();
                table.ajax.reload();
                
                toastr.success('Success');
             

            },
            error: function(error) {
                console.log(error);
                toastr.error(error);
                $('.container').waitMe('hide');
            }
        });
        });

    </script>
</body>
</html>