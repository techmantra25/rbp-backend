
@extends('admin.layouts.app')

@section('page', 'Download All Report')
@section('content')

<style>
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        justify-content: center;
        align-items: center;
    }

    .loading-spinner {
        border: 4px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top: 4px solid #ffffff;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<section class="store-sec ">
   
  <div class="row">
      <div class="col-xl-12 order-2 order-xl-1">
          <div class="card search-card">
              <div class="card-body">
                 
                  
                  <div class="table-responsive">
                      <table class="table table-sm admin-table no-sticky" id="example5">
        <thead>
        <tr>
            <th>File</th>
            <th>Date</th>
            <th>Action</th>
            
        </tr>
        </thead>
        <tbody>
            @foreach ($files as $file)
			   @php
			   
			   $timestamp = Illuminate\Support\Facades\Storage::lastModified($file);

                // Convert timestamp to a human-readable date and time format
                $dateTime = date('j M Y g:i A', $timestamp);
			   @endphp
            <tr>
                <td>
                   {{ $file ?? ''}}
                </td>
                <td>{{$dateTime ?? ''}}</td>
                <td>
                    <a href="{{ route('admin.download.file', ['file_path' => $file]) }}">Download</a>
                   
                </td>
                
          
                
            </tr>
           
            @endforeach
        </tbody>
    </table>
                  </div>

                  
              </div>
          </div>
      </div>
  </div>
</section>


<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
</div>


@endsection

@section('script')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>


@endsection
