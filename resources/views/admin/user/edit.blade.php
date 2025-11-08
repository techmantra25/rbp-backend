
@extends('admin.layouts.app')
@section('page', 'Edit User')

@section('content')
@php
$distributorTeam=\App\Models\Team::select('id','nsm_id','zsm_id','rsm_id','sm_id','asm_id','ase_id','distributor_id','state_id','area_id')->where('distributor_id',$data->id)->where('store_id',NULL)->paginate(50);

@endphp
<style>
    .select2-container{
        display:block;
    }
    .select2-dropdown {
        z-index: 1056;
    }
    .icon-btn {
    padding: 3px 3px;
    line-height: 1;
    font-size: 12px;
    }
</style>
<section class="inner-sec1">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.users.update', $data->id) }}" enctype="multipart/form-data">@csrf @method('PUT')
                        <div class="row mb-2">
                            <div class="col-12">
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="mobile">Type <span class="text-danger">*</span></label>
                                    <div class="form-floating mb-3">
                                        <select class="form-select select2" id="user_type" name="type">
                                            <option value="" selected disabled>Select</option>
                                            <option value="1" {{ ($data->type == 1) ? 'selected' : '' }}>NSM</option>
                                            <option value="2" {{ ($data->type == 2) ? 'selected' : '' }}>ZSM</option>
                                            <option value="3" {{ ($data->type == 3) ? 'selected' : '' }}>RSM</option>
                                            <option value="4" {{ ($data->type == 4) ? 'selected' : '' }}>SM</option>
                                            <option value="5" {{ ($data->type == 5) ? 'selected' : '' }}>ASM</option>
                                            <option value="6" {{ ($data->type == 6) ? 'selected' : '' }}>ASE</option>
                                            <option value="7" {{ ($data->type == 7) ? 'selected' : '' }}>Distributor</option>
                                        </select>
                                       
                                    </div>
                                    @error('mobile') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="designation">Designation <span class="text-danger">*</span></label>
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="designation" name="designation" placeholder="name@example.com" value="{{ old('designation') ? old('designation') : $data->designation }}">
                                        
                                    </div>
                                    @error('designation') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="employee_id">Employee ID<span class="text-danger">*</span></label>
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="employee_id" name="employee_id" placeholder="name@example.com" value="{{ old('employee_id') ? old('employee_id') : $data->employee_id }}">
                                        
                                    </div>
                                    @error('employee_id') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-12">
                                <p class="small text-muted mb-2">Name details</p>
                            </div>
                           
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="name" name="name" placeholder="name@example.com" value="{{ old('name') ? old('name') : $data->name }}">
                                        <label for="name">Full name <span class="text-danger">*</span></label>
                                    </div>
                                    @error('name') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="fname" name="fname" placeholder="name@example.com" value="{{ old('fname') ? old('fname') : $data->fname }}">
                                        <label for="fname">First name <span class="text-danger">*</span></label>
                                    </div>
                                    @error('fname') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="lname" name="lname" placeholder="name@example.com" value="{{ old('lname') ? old('lname') : $data->lname }}">
                                        <label for="lname">Last name <span class="text-danger">*</span></label>
                                    </div>
                                    @error('lname') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-12">
                                <p class="small text-muted mb-2">Contact details</p>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control" id="mobile" name="mobile" placeholder="name@example.com" value="{{ old('mobile') ? old('mobile') : $data->mobile }}">
                                        <label for="mobile">Mobile number <span class="text-danger">*</span></label>
                                    </div>
                                    @error('mobile') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control" id="mobile" name="whatsapp_no" placeholder="name@example.com" value="{{ old('whatsapp_no') ? old('whatsapp_no') : $data->whatsapp_no }}">
                                        <label for="mobile">Whatsapp number </label>
                                    </div>
                                    @error('mobile') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" value="{{ old('email') ? old('email') : $data->email }}">
                                        <label for="email">Official Email ID</label>
                                    </div>
                                    @error('email') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                             
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control" id="date_of_joining" name="date_of_joining" placeholder="name@example.com" value="">
                                        <label for="date_of_joining">Date of Joining</label>
                                    </div>
                                    @error('date_of_joining') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="password" name="password" placeholder="name@example.com" value="">
                                        <label for="password">Password</label>
                                    </div>
                                    @error('password') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                        </div>
                    
                        <div class="row mb-2">
                            <div class="col-12">
                                <p class="small text-muted mb-2">Location details</p>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="state">State <span class="text-danger">*</span></label>
                                    <div class="form-floating mb-3">
                                        <select class="form-select select2" id="state" name="state" aria-label="Floating label select example">
                                            <option value="" selected disabled>Select</option>
                                            @foreach ($data->stateDetails as $index => $item)
                                                <option value="{{ $item->name }}" {{ (strtolower($data->state) == strtolower($item->name)) ? 'selected' : '' }}>{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                      
                                    </div>
                                    @error('state') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="area">City/ Area </label><span>({{$data->city}})</span>
                                    <div class="form-floating mb-3">
                                        <select class="form-select select2" id="area" name="area" aria-label="Floating label select example" disabled>
                                            <option value="">Select State first</option>
                                        </select>
                                       
                                    </div>
                                    @error('area') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="area">HQ </label><span></span>
                                    <div class="form-floating mb-3">
                                        <select class="form-select select2" id="state" name="headquater" aria-label="Floating label select example">
                                            <option value="" selected disabled>Select</option>
                                            @foreach ($hq as $index => $item)
                                                <option value="{{ $item->name }}" {{ (strtolower($data->headquater) == strtolower($item->name)) ? 'selected' : '' }}>{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                       
                                    </div>
                                    @error('area') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                        </div>
						
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-danger">Update changes</button>
                            </div>
                        </div>
                    </form>
                    @if($data->type==7)
                    <div class="card shadow-sm" id="singleProductVariation">
                        <div class="card-header">
                            <div class="row justify-content-between">
                                <div class="col-6">
                                    <h3>Team Details</h3>
                                    <p class="small text-muted m-0">Add | edit | delete team hierarchy from here</p>
                                    
                                </div>
                                <div class="col-2 text-end">
                                    <a href="#newRangeModal" data-bs-toggle="modal" type="button" class="btn btn-sm btn-success new-color">Add New Record</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">

                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>#SR</th>
                                            <th>State</th>
                                            <th>Area</th>
                                            <th>NSM</th>
                                            <th>ZSM</th>
                                            <th>RSM</th>
                                            <th>SM</th>
                                            <th>ASM</th>
                                            <th>ASE</th>
                                            <th>Distributor</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($distributorTeam as $index => $row)
                                            <tr>
                                                <td>{{$index + $distributorTeam->firstItem()}}</td>
                                                <td>{{$row->states->name ??''}}</td>
                                                <td>{{$row->areas->name ??''}}</td>
                                                <td>{{$row->nsm->name ?? ''}}</td>
                                                <td>{{$row->zsm->name ?? ''}}</td>
                                                <td>{{$row->rsm->name ?? ''}}</td>
                                                <td>{{$row->sm->name ?? ''}}</td>
                                                <td>{{$row->asm->name ?? ''}}</td>
                                                <td>{{$row->ase->name ??''}}</td>
                                                <td>{{$row->distributors->name}}</td>
                                                <td>
            
                                                    <a href="#exampleModal_{{$row->id}}" data-bs-toggle="modal" type="button" class="btn btn-success new-color icon-btn"><iconify-icon icon="tabler:edit"></iconify-icon></a>
                                                
                                                    <div class="modal fade rrrr" id="exampleModal_{{$row->id}}"  aria-labelledby="newRangeModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="newRangeModalLabel">Update</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form action="{{route('admin.users.team.update',$row->id)}}" method="POST">@csrf
                                                                        <input type="hidden" name="distributor_id" value="{{$data->id}}">
                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <div class="form-group">
                                                                                    <label for="vp">NSM *</label>
                                                                                    <div class="form-floating mb-3">
                                                                                        <select class="form-select select2" id="nsm" name="nsm_id" aria-label="Floating label select example">
                                                                                            <option value="" selected disabled>Select</option>
                                                                                            @foreach ($data->allNSM as $item)
                                                                                                <option value="{{$item->id}}" {{ ($row->nsm_id == $item->id) ? 'selected' : '' }}>{{$item->name}}</option>
                                                                                            @endforeach
                                                                                        </select>
                                                                                    
                                                                                    </div>
                                                                                    @error('nsm_id') <p class="small text-danger">{{$message}}</p> @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="form-group">
                                                                                    <label for="vp">ZSM *</label>
                                                                                    <div class="form-floating mb-3">
                                                                                        <select class="form-select select2" id="zsm" name="zsm_id" aria-label="Floating label select example">
                                                                                            <option value="" selected disabled>Select</option>
                                                                                            @foreach ($data->allZSM as $item)
                                                                                                <option value="{{$item->id}}" {{ ($row->zsm_id) == strtolower($item->id) ? 'selected' : '' }}>{{$item->name}}</option>
                                                                                            @endforeach
                                                                                        </select>
                                                                                        
                                                                                    </div>
                                                                                    @error('zsm_id') <p class="small text-danger">{{$message}}</p> @enderror
                                                                                </div>
                                                                            </div>
                                                                            
                                                                            <div class="col-md-6">
                                                                                <div class="form-group">
                                                                                    <label for="rsm">RSM *</label>
                                                                                    <div class="form-floating mb-3">
                                                                                        <select class="form-select select2" id="rsm" name="rsm_id" aria-label="Floating label select example">
                                                                                            <option value="" selected disabled>Select</option>
                                                                                            @foreach ($data->allRSM as $item)
                                                                                                <option value="{{$item->id}}" {{ ($row->rsm_id) == strtolower($item->id) ? 'selected' : '' }}>{{$item->name}}</option>
                                                                                            @endforeach
                                                                                        </select>
                                                                                    
                                                                                    </div>
                                                                                    @error('rsm_id') <p class="small text-danger">{{$message}}</p> @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="form-group">
                                                                                    <label for="rsm">SM *</label>
                                                                                    <div class="form-floating mb-3 ">
                                                                                        <select class="form-select select2" id="sm" name="sm_id" aria-label="Floating label select example">
                                                                                            <option value="" selected disabled>Select</option>
                                                                                            @foreach ($data->allSM as $item)
                                                                                                <option value="{{$item->id}}" {{ ($row->sm_id) == strtolower($item->id) ? 'selected' : '' }}>{{$item->name}}</option>
                                                                                            @endforeach
                                                                                        </select>
                                                                                    
                                                                                    </div>
                                                                                    @error('sm_id') <p class="small text-danger">{{$message}}</p> @enderror
                                                                                </div>
                                                                            </div>
                                                                            
                                                                            
                                                                            <div class="col-md-6">
                                                                                <div class="form-group">
                                                                                    <label for="asm">ASM *</label>
                                                                                    <div class="form-floating mb-3">
                                                                                        <select class="form-select select2" id="asm" name="asm_id" aria-label="Floating label select example">
                                                                                            <option value="" selected disabled>Select</option>
                                                                                            @foreach ($data->allASM as $item)
                                                                                                <option value="{{$item->id}}" {{ ($row->asm_id) == strtolower($item->id) ? 'selected' : '' }}>{{$item->name}}</option>
                                                                                            @endforeach
                                                                                        </select>
                                                                                    
                                                                                    </div>
                                                                                    @error('asm_id') <p class="small text-danger">{{$message}}</p> @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="form-group">
                                                                                    <label for="asm">ASE *</label>
                                                                                    <div class="form-floating mb-3">
                                                                                        <select class="form-select select2" id="ase" name="ase_id" aria-label="Floating label select example">
                                                                                            <option value="" selected disabled>Select</option>
                                                                                            @foreach ($data->allASE as $item)
                                                                                                <option value="{{$item->id}}" {{ ($row->ase_id) == strtolower($item->id) ? 'selected' : '' }}>{{$item->name}}</option>
                                                                                            @endforeach
                                                                                        </select>
                                                                                        
                                                                                    </div>
                                                                                    @error('ase_id') <p class="small text-danger">{{$message}}</p> @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="form-group">
                                                                                    <label for="state">State *</label>
                                                                                    <div class="form-floating mb-3">
                                                                                        <select class="form-select select2" id="stateId" name="stateId" aria-label="Floating label select example">
                                                                                            <option value="" selected disabled>Select</option>
                                                                                            @foreach ($data->stateDetails as $index => $item)
                                                                                                <option value="{{ $item->name }}" {{ ($row->state_id) == strtolower($item->id) ? 'selected' : '' }}>{{ $item->name }}</option>
                                                                                            @endforeach
                                                                                        </select>
                                                                                    
                                                                                    </div>
                                                                                    @error('stateId') <p class="small text-danger">{{$message}}</p> @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="form-group">
                                                                                    <label for="area">City/ Area *</label><span>({{$row->areas->name ??''}})</span>
                                                                                    <div class="form-floating mb-3">
                                                                                        <select class="form-select select2" id="areaId" name="areaId" aria-label="Floating label select example" disabled>
                                                                                            <option value="">Select State first</option>
                                                                                        </select>
                                                                                    
                                                                                    </div>
                                                                                    @error('areaId') <p class="small text-danger">{{$message}}</p> @enderror
                                                                                </div>
                                                                            </div>
                                                    
                                                                            <div class="col-12 mt-3">
                                                                                    <button type="submit" class="btn btn-sm btn-danger">Update</button>
                                                                            </div>
                                                                        </div>
                                                                        
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <a href="{{ route('admin.users.team.delete',$row->id) }}" onclick="return confirm('Are you sure ?')" type="button" class="btn btn-danger icon-btn"><iconify-icon icon="material-symbols:delete"></iconify-icon></a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="100%" class="small text-muted text-center">No records found</td>
                                            </tr>
                                            {{-- edit team ---}}
                                
                                        @endforelse
                                    </tbody>
                                </table>
                            <div class="d-flex justify-content-end">
                                {{$distributorTeam->appends($_GET)->links()}}
                            </div>
                        </div>
                     </div>
                     @endif
                </div>
            </div>
        </div>
    </div>
    {{-- add team ---}}
<div class="modal fade distributor-edit" id="newRangeModal"  aria-labelledby="newRangeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newRangeModalLabel">Add new ASE for Distributor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{route('admin.users.team.add')}}" method="POST">@csrf
                    <input type="hidden" name="distributor_id" value="{{$data->id}}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="vp">NSM *</label>
                                <div class="form-floating mb-3">
                                    <select class="form-select select2" id="nsm_data" name="nsm_id" aria-label="Floating label select example">
                                        <option value="" selected disabled>Select</option>
                                        @foreach ($data->allNSM as $item)
                                            <option value="{{$item->id}}">{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                
                                </div>
                                @error('nsm_id') <p class="small text-danger">{{$message}}</p> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="vp">ZSM *</label>
                                <div class="form-floating mb-3">
                                    <select class="form-select select2" id="zsm_data" name="zsm_id" aria-label="Floating label select example">
                                        <option value="" selected disabled>Select</option>
                                        @foreach ($data->allZSM as $item)
                                            <option value="{{$item->id}}">{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                    
                                </div>
                                @error('zsm_id') <p class="small text-danger">{{$message}}</p> @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="rsm">RSM *</label>
                                <div class="form-floating mb-3">
                                    <select class="form-select select2" id="rsm_data" name="rsm_id" aria-label="Floating label select example">
                                        <option value="" selected disabled>Select</option>
                                        @foreach ($data->allRSM as $item)
                                            <option value="{{$item->id}}" >{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                  
                                </div>
                                @error('rsm_id') <p class="small text-danger">{{$message}}</p> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="rsm">SM *</label>
                                <div class="form-floating mb-3 ">
                                    <select class="form-select select2" id="sm_data" name="sm_id" aria-label="Floating label select example">
                                        <option value="" selected disabled>Select</option>
                                        @foreach ($data->allSM as $item)
                                            <option value="{{$item->id}}">{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                   
                                </div>
                                @error('sm_id') <p class="small text-danger">{{$message}}</p> @enderror
                            </div>
                        </div>
                        
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="asm">ASM *</label>
                                <div class="form-floating mb-3">
                                    <select class="form-select select2" id="asm_data" name="asm_id" aria-label="Floating label select example">
                                        <option value="" selected disabled>Select</option>
                                        @foreach ($data->allASM as $item)
                                            <option value="{{$item->id}}" >{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                   
                                </div>
                                @error('asm_id') <p class="small text-danger">{{$message}}</p> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="asm">ASE *</label>
                                <div class="form-floating mb-3">
                                    <select class="form-select select2" id="ase_data" name="ase_id" aria-label="Floating label select example">
                                        <option value="" selected disabled>Select</option>
                                        @foreach ($data->allASE as $item)
                                            <option value="{{$item->id}}">{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                    
                                </div>
                                @error('ase_id') <p class="small text-danger">{{$message}}</p> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="state">State *</label>
                                <div class="form-floating mb-3">
                                    <select class="form-select select2" id="state_data" name="stateId" aria-label="Floating label select example">
                                        <option value="" selected disabled>Select</option>
                                        @foreach ($data->stateDetails as $index => $item)
                                            <option value="{{ $item->name }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                  
                                </div>
                                @error('stateId') <p class="small text-danger">{{$message}}</p> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="area">City/ Area *</label><span>({{$data->city}})</span>
                                <div class="form-floating mb-3">
                                    <select class="form-select select2" id="area_data" name="areaId" aria-label="Floating label select example" disabled>
                                        <option value="">Select State first</option>
                                    </select>
                                   
                                </div>
                                @error('areaId') <p class="small text-danger">{{$message}}</p> @enderror
                            </div>
                        </div>

                        <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-sm btn-danger">Save</button>
                        </div>
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
    $('select[name="state"]').on('change', (event) => {
        var value = $('select[name="state"]').val();
      
        $.ajax({
            url: '{{url("/")}}/admin/users/state/'+value,
            method: 'GET',
            success: function(result) {
                var content = '';
                var slectTag = 'select[name="area"]';
                var displayCollection =  "All";

                content += '<option value="" selected>'+displayCollection+'</option>';
                $.each(result.data.area, (key, value) => {
                    content += '<option value="'+value.area+'">'+value.area+'</option>';
                });
                $(slectTag).html(content).attr('disabled', false);
            }
        });
    });
</script>
<script>

	$(document).ready(function() {
  //$("select").select2({
    //dropdownParent: $('.rrrr' '.distributor-edit')
	//dropdownParent: $('.distributor-edit')
  //});
$('.modal select').each(function() {  
   var $p = $(this).parent(); 
   $(this).select2({  
     dropdownParent: $p  
   });  
});
});
	

    $('select[name="stateId"]').on('change', (event) => {
        var value = $('select[name="stateId"]').val();
      
        $.ajax({
            url: '{{url("/")}}/admin/users/state/'+value,
            method: 'GET',
            success: function(result) {
                var content = '';
                var slectTag = 'select[name="areaId"]';
                var displayCollection =  "All";

                content += '<option value="" selected>'+displayCollection+'</option>';
                $.each(result.data.area, (key, value) => {
                    content += '<option value="'+value.area+'">'+value.area+'</option>';
                });
                $(slectTag).html(content).attr('disabled', false);
            }
        });
    });
</script>
@endsection
