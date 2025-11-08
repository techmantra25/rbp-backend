@extends('admin.layouts.app')
@section('page', 'Catalogue')
@section('content')
<section class="inner-sec1">
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.catalogues.store') }}" enctype="multipart/form-data">
                    @csrf
                        <h4 class="page__subtitle">Add New Catalogue</h4>
                        <div class="form-group mb-3">
                            <label class="label-control">Name <span class="text-danger">*</span> </label>
                            <input type="text" name="name" placeholder="" class="form-control" value="{{old('name')}}">
                            @error('name') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label class="label-control">Start Date </label>
                            <input type="date" name="start_date" class="form-control">{{old('start_date')}}</textarea>
                            @error('start_date') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label class="label-control">End Date </label>
                            <input type="date" name="end_date" class="form-control">{{old('end_date')}}</textarea>
                            @error('end_date') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="col-12 col-md-6 col-xl-12">
                            <div class="row">
                                <div class="col-md-6 card">
                                    <div class="card-header p-0 mb-3">Image <span class="text-danger">*</span></div>
                                    <div class="card-body p-0">
                                        <div class="w-100 product__thumb">
                                            <label for="icon"><img id="iconOutput" src="{{ asset('admin/images/placeholder-image.jpg') }}" /></label>
                                        </div>
                                        <input type="file" name="image" id="icon" accept="image/*" onchange="loadIcon(event)" class="d-none">
                                        <script>
                                            let loadIcon = function(event) {
                                                let iconOutput = document.getElementById('iconOutput');
                                                iconOutput.src = URL.createObjectURL(event.target.files[0]);
                                                iconOutput.onload = function() {
                                                    URL.revokeObjectURL(iconOutput.src) // free memory
                                                }
                                            };
                                        </script>
                                    </div>
                                    @error('image') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                                <div class="col-md-6 card">
                                    <div class="card-header p-0 mb-3">Pdf <span class="text-danger">*</span></div>
                                    <div class="card-body p-0">
                                        <div class="w-100 product__thumb">
                                        </div>
                                        <div class="col-sm-9">
                                            <input class="form-control" type="file" name="pdf" id="pdf">
                                       </div>
                                    </div>
                                    @error('pdf') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-sm btn-danger">Add New Catalogue</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('script')
<script>
    function htmlToCSV() {
        var data = [];
        var rows = document.querySelectorAll("#example5 tbody tr");
        @php
            if (!request()->input('page')) {
                $page = '1';
            } else {
                $page = request()->input('page');
            }
        @endphp

        var page = "{{ $page }}";

        data.push("SRNO,Image,Pdf,Title,Date,Status");

        for (var i = 0; i < rows.length; i++) {
            var row = [],
                cols = rows[i].querySelectorAll("td");

            for (var j = 0; j < cols.length; j++) {
                var text = cols[j].innerText.split(' ');
                var new_text = text.join('-');
                if (j == 3||j==4)
                    var comtext = new_text.replace(/\n/g, "-");
                else
                    var comtext = new_text.replace(/\n/g, ";");
                row.push(comtext);

            }
            data.push(row.join(","));
        }

        downloadCSVFile(data.join("\n"), 'Catalogue.csv');
    }

    function downloadCSVFile(csv, filename) {
        var csv_file, download_link;

        csv_file = new Blob([csv], {
            type: "text/csv"
        });

        download_link = document.createElement("a");

        download_link.download = filename;

        download_link.href = window.URL.createObjectURL(csv_file);

        download_link.style.display = "none";

        document.body.appendChild(download_link);

        download_link.click();
    }


</script>
 @if (request()->input('export_all') == true)
                <script>
                    htmlToCSV();
                    window.location.href = "{{ route('admin.catalogue.index') }}";
                </script>
            @endif
@endsection
