@extends('layouts.navigation-admin')

@section('contents')
    <div class="container">
        <!-- Settings Title -->
        <div class="card shadow-sm mb-4">
            <div class="card-body d-flex justify-content-between align-items-center">
                <h3 class="fw-bold primary-color-text">Settings</h3>
            </div>
        </div>

        <!-- Purchase Order Form -->
        @if($packs->count() < 3)
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Purchase Order</h5>
                    <form action="{{ route('admin_analyze.setting.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="namePack" class="form-label">Name Pack</label>
                            <select class="form-select" name="namePack" id="namePack" required>
                                <option value="" disabled selected>Pilih Pack</option>
                                <option value="trial">Trial</option>
                                <option value="bundle">Bundle</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="text" class="form-control" name="price" id="price" placeholder="Rp.">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-custom2">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        @else
            <div class="alert alert-warning">
                Data pack sudah mencapai batas maksimal 3. Tidak dapat menambahkan data baru.
            </div>
        @endif

        <!-- Pack Table -->
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Pack</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr class="text-center">
                            <th>Name Pack</th>
                            <th>Price</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($packs as $pack)
                            <tr>
                                <td>{{ $pack->name_pack }}</td>
                                <td>{{ $pack->price }}</td>
                                <td>{{ $pack->description }}</td>
                                <td class="text-center">
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editPackModal" 
                                            data-id="{{ $pack->id }}"
                                            data-name="{{ $pack->name_pack }}"
                                            data-price="{{ $pack->price }}"
                                            data-description="{{ $pack->description }}">
                                        Edit
                                    </button>
                                    <form action="{{ route('admin_analyze.setting.destroy', $pack->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editPackModal" tabindex="-1" aria-labelledby="editPackModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin_analyze.setting.update', 0) }}" method="POST" id="editForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPackModalLabel">Edit Pack</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editNamePack" class="form-label">Name Pack</label>
                            <select class="form-select" name="namePack" id="editNamePack" required>
                                <option value="trial">Trial</option>
                                <option value="bundle">Bundle</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="editPrice" class="form-label">Price</label>
                            <input type="text" class="form-control" name="price" id="editPrice" placeholder="Rp.">
                        </div>

                        <div class="mb-3">
                            <label for="editDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="editDescription" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-custom2">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const editPackModal = document.getElementById('editPackModal');
        editPackModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            const price = button.getAttribute('data-price');
            const description = button.getAttribute('data-description');

            // Ubah form action untuk update
            const formAction = '/admin_analyze/settings/' + id;
            document.getElementById('editForm').action = formAction;

            // Isi form dengan data
            document.getElementById('editNamePack').value = name;
            document.getElementById('editPrice').value = price;
            document.getElementById('editDescription').value = description;
        });
    </script>

    <!-- SweetAlert2 Notifications -->
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Sukses',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 2000
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ session('error') }}',
                showConfirmButton: false,
                timer: 2000
            });
        </script>
    @endif
@endsection
