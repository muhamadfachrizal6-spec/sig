@extends('layouts.navigation-admin')

@section('contents')
    <div class="container m-2">
        <div class="mb-4 card shadow-sm border-light p-3">
            <h3>Edit Data Emiten</h3>
        </div>

        @error('name')
            <div class="text-danger">{{ $message }}</div>
        @enderror

        <!-- Card untuk Edit Company -->
        <div class="card shadow-sm border-light p-3">
            <p class="text-muted">Edit the details of the company</p>

            <form action="{{ route('admin_analyze.emiten.update', $company->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label"><b>Name Emiten</b></label>
                        <input type="text" name="name" class="form-control" id="name"
                            value="{{ $company->name }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="ticker" class="form-label"><b>Ticker Code</b></label>
                        <input type="text" name="ticker" class="form-control" id="ticker"
                            value="{{ $company->ticker }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="category" class="form-label"><b>Category</b></label>
                        <select name="category" class="form-control" id="category">
                            <option value="IDXENERGY" {{ $company->category == 'IDXENERGY' ? 'selected' : '' }}>IDXENERGY</option>
                            <option value="IDXNONCYC" {{ $company->category == 'IDXNONCYC' ? 'selected' : '' }}>IDXNONCYC</option>
                            <option value="IDXFINANCE" {{ $company->category == 'IDXFINANCE' ? 'selected' : '' }}>IDXFINANCE</option>
                            <option value="IDXINFRA" {{ $company->category == 'IDXINFRA' ? 'selected' : '' }}>IDXINFRA</option>
                            <option value="IDXBASIC" {{ $company->category == 'IDXBASIC' ? 'selected' : '' }}>IDXBASIC</option>
                            <option value="IDXTRANS" {{ $company->category == 'IDXTRANS' ? 'selected' : '' }}>IDXTRANS</option>
                            <option value="IDXINDUST" {{ $company->category == 'IDXINDUST' ? 'selected' : '' }}>IDXINDUST</option>
                            <option value="IDXTECHNO" {{ $company->category == 'IDXTECHNO' ? 'selected' : '' }}>IDXTECHNO</option>
                            <option value="IDXHEALTH" {{ $company->category == 'IDXHEALTH' ? 'selected' : '' }}>IDXHEALTH</option>
                            <option value="IDXINVEST" {{ $company->category == 'IDXINVEST' ? 'selected' : '' }}>IDXINVEST</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="address" class="form-label"><b>Address</b></label>
                        <input type="text" name="address" class="form-control" id="address"
                            value="{{ $company->address }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="logo" class="form-label"><b>Logo</b></label>
                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                    </div>
                    <div class="col-md-6">
                        <label for="description" class="form-label"><b>Description</b></label>
                        <textarea name="description" class="form-control" id="description" rows="3" required>{{ $company->description }}</textarea>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="current_logo" class="form-label"><b>Current Logo</b></label><br>
                    @if ($company->logo)
                        <img src="{{ asset($company->logo) }}" alt="Logo {{ $company->name }}" width="100">
                    @else
                        <p>No logo available</p>
                    @endif
                </div>

                <hr class="mt-3 mb-3 w-100">

                <button type="submit" class="btn btn-custom2">Update</button>
            </form>
        </div>
    </div>

    <!-- SweetAlert2 Success/Error Handling -->
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                confirmButtonText: 'OK'
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                confirmButtonText: 'OK'
            });
        </script>
    @endif
@endsection
