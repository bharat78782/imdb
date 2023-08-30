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
                <a href="{{route('scrape')}}" class="btn btn-success mb-3 add">Add Movie</a>
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
                       @if(count($data)>0)
                        @foreach($data as $key => $item) 
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$item->title}}</td>
                            <td>{{$item->year}}</td>
                            <td>{{$item->rating}}</td>
                            <td><a href="{{$item->year}}" target="_blank">{{$item->url}}</a></td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>

                <nav aria-label="Page navigation example" style="float:right">
                <ul class="pagination">
                {!! $data->links() !!}
                </ul>
                </nav>
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

       
        // toastr.success('asas');
        $(document).on('click','.add',function(e){
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
            data: {type:"0"},
            success: function(response) {
                console.log(response);
                $('.container').waitMe('hide');
               
                toastr.success('Success');
                setInterval(function () {location.reload()}, 1000);
             

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