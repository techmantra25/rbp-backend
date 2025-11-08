@extends('admin.layouts.app')
@section('page', $distributor->name.' Range')

@section('content')
<section>
    <div class="card card-body">
        <div class="search__filter mb-0">
            <div class="row">
                <div class="search-filter-right">
                    <div class="search-filter-right-el">
                        <a href="#newRangeModal" data-bs-toggle="modal" class="btn btn-outline-danger btn-sm store-filter-btn">Add new Range</a>
                
                    </div>    
                </div>
            </div>
        </div>
    </div>

    <table class="table" id="example5">
        <thead>
            <tr>
                <th>#SR</th>
                <th>Range</th>
                <th>Sales Person</th>
				<th>Action</th>
            </tr>
        </thead>
        <tbody>
			@forelse ($data as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    @php
                        $collectionName = \App\Models\Collection::findOrFail($item->collection_id);
                    @endphp
                    {{ $collectionName->name }}
                </td>
                <td>
                    {{ $item->users->name }}
                </td>
				<td>
                    <a href="{{ route('admin.users.collection.delete', $item->id) }}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
				</td>
            </tr>
            @empty
            <tr><td colspan="100%" class="small text-muted text-center">No data found</td></tr>
            @endforelse
        </tbody>
    </table>
</section>

<div class="modal fade" id="newRangeModal" tabindex="-1" aria-labelledby="newRangeModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="newRangeModalLabel">Add new Range</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form action="{{ route('admin.users.collection.create', $id) }}" method="post">@csrf
                    <div class="row">
                        <div class="col-12">
                            <label for="collection_id" class="small text-muted">Select Range</label>
                            <select name="collection_id" id="collection_id" class="form-control form-control-sm">
                                <option value="" selected disabled>Select</option>
                                @foreach($collections as $collection)
                                    <option value="{{ $collection->id }}">{{ $collection->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="user_id" class="small text-muted">Select Sales Person</label>
                            <select name="user_id" id="user_id" class="form-control form-control-sm ">
                                <option value="" selected disabled>Select</option>
                                @foreach($aseList as $ase)
                                    <option value="{{ $ase->id }}" data-name="{{ $ase->ase->name }}">{{ $ase->ase->name }} ({{ $ase->areas->name }}, {{ $ase->states->name }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 mt-3">
                            <input type="hidden" name="user_name" value="">
                            <input type="hidden" name="distributor_id" value="{{ $id }}">
                            <div class="search-filter-right-el">
                                 <button type="submit" class="btn btn-outline-danger btn-sm store-filter-btn">Add Range</button>
                            </div>
                        </div>
                    </div>
                </form>
			</div>
		</div>
	</div>
</div>
@endsection

@section('script')
    <script>
        $('select[name="user_id"]').on('change', function() {
            const userName = $(this).find(':selected').data('name');
            $('input[name="user_name"]').val(userName);
        });
   </script>
@endsection
