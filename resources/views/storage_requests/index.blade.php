<!-- resources/views/storage-requests/index.blade.php -->

@extends('layouts.app')

@section('content')
    <style>
        /* Tabs Styling */
        .custom-tabs .nav-link {
            font-weight: bold;
            border-radius: 10px 10px 0 0;
            padding: 8px 16px;
            color: #333;
        }

        .custom-tabs .nav-link.active {
            background-color: #333;
            color: white;
        }

        /* Form Content Styling */
        .form-tab-content {
            padding: 20px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-title {
            font-weight: bold;
            font-size: 1.1rem;
            color: #333;
            margin-bottom: 16px;
        }

        /* Input Fields */
        .form-control,
        .textarea {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            font-size: 0.9rem;
            width: 100%;
        }

        .unit {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            color: #777;
        }

        .char-count {
            text-align: right;
            font-size: 0.8rem;
            color: #888;
            margin-top: 4px;
        }

        /* Submit Button */
        .btn-submit {
            background-color: #333;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 1rem;
            cursor: pointer;
        }

        .btn-submit:hover {
            background-color: #555;
        }

        /* FILTER PERMINTAAN */
        .filter-tab {
            text-decoration: none;
            padding-bottom: 5px;
        }

        .filter-tab.active {
            border-bottom: 3px solid #28a745;
            color: #28a745;
        }

        .filter-tab:hover {
            color: #28a745;
        }
    </style>

    <div class="container my-5">
        @can('view-logs')
            <div class="d-flex justify-content-between align-items-center mb-3 mt-5">
                <h3>Permintaan Penyimpanan User</h3>

                <!-- Search Form -->
                <form method="GET" action="{{ route('storage-requests.index') }}" class="d-flex">
                    <div class="input-group" style="border: 2px solid #7f7f7f; border-radius: 20px; overflow: hidden;">
                        <!-- Button on the left -->
                        <button class="btn border-none bg-transparent" type="submit"
                            style="border: none; border-radius: 20px 0 0 20px;">
                            <i class='bx bx-search-alt file-icon text-secondary fw-bold'></i>
                        </button>
                        <!-- Input on the right with custom focus styles -->
                        <input type="text" class="form-control border-none" name="search" placeholder="Cari"
                            value="{{ $search ?? '' }}"
                            style="max-width: 300px; border: none; outline: none; box-shadow: none;">

                        <!-- Close button to clear the search -->
                        @if (!empty($search))
                            <a href="{{ route('storage-requests.index') }}" class="btn btn-link text-secondary align-middle"
                                style="border-radius: 20px 0 0 20px;">
                                <i class='bx bx-x'></i> <!-- Close icon to clear the search -->
                            </a>
                        @endif
                    </div>
                </form>

            </div>
            @error('status')
                <div class="alert alert-danger mt-2">{{ $message }}</div>
            @enderror
        @endcan

        @can('view-bookmarks')
            <ul class="nav nav-pills custom-tabs mb-3" id="pills-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('storage-requests.create') }}" role="tab"
                        aria-selected="false">Formulir</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" id="pills-riwayat-tab" data-bs-toggle="pill" data-bs-target="#pills-riwayat"
                        type="button" role="tab" aria-selected="true">Riwayat</a>
                </li>
            </ul>
        @endcan

        <div class="tab-content form-tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-riwayat" role="tabpanel" aria-labelledby="pills-riwayat-tab">
                <form method="GET" class="mb-3">
                    <!-- Filter Riwayat -->
                    <div class="d-flex justify-content-start mb-4">
                        <!-- Tab Semua -->
                        <a href="{{ route('storage-requests.index') }}"
                            class="filter-tab mx-2 {{ request('status') == null ? 'fw-bold text-success active' : 'fw-bold text-dark' }}">
                            Semua ({{ $totalRequests }})
                        </a>

                        <!-- Tab Proses -->
                        <a href="{{ route('storage-requests.index', ['status' => \App\Enums\StorageRequestStatus::PENDING->value]) }}"
                            class="filter-tab mx-2 {{ request('status') == \App\Enums\StorageRequestStatus::PENDING->value ? 'fw-bold text-success active' : 'fw-bold text-dark' }}">
                            Diproses ({{ $pendingRequests }})
                        </a>

                        <!-- Tab Diterima -->
                        <a href="{{ route('storage-requests.index', ['status' => \App\Enums\StorageRequestStatus::APPROVED->value]) }}"
                            class="filter-tab mx-2 {{ request('status') == \App\Enums\StorageRequestStatus::APPROVED->value ? 'fw-bold text-success active' : 'fw-bold text-dark' }}">
                            Diterima ({{ $approvedRequests }})
                        </a>

                        <!-- Tab Ditolak -->
                        <a href="{{ route('storage-requests.index', ['status' => \App\Enums\StorageRequestStatus::REJECTED->value]) }}"
                            class="filter-tab mx-2 {{ request('status') == \App\Enums\StorageRequestStatus::REJECTED->value ? 'fw-bold text-success active' : 'fw-bold text-dark' }}">
                            Ditolak ({{ $rejectedRequests }})
                        </a>
                    </div>

                    <button type="submit" class="btn btn-secondary mt-3" style="display: none">Filter</button>
                </form>

                <table class="table table-borderless table-hover table-layout: fixed;">
                    <thead class="text-center"
                        style="border-bottom: 3px solid #ddd; position: relative; box-shadow: 0 6px 4px -4px rgba(0, 0, 0, 0.3); z-index: 1;">
                        <tr>
                            @can('view-bookmarks')
                                <th class="align-middle fw-semibold text-dark">No.</th>
                            @endcan
                            <th class="align-middle fw-semibold text-dark">Tanggal</th>
                            @if (Auth::user()->role === \App\Enums\UserRole::SUPERADMIN || Auth::user()->role === \App\Enums\UserRole::ADMIN)
                                <th class="align-middle fw-semibold text-dark">Profil</th>
                                <th class="align-middle fw-semibold text-dark">Nama</th>
                            @endif
                            <th class="align-middle fw-semibold text-dark">Kuota (GB)</th>
                            <th class="align-middle fw-semibold text-dark">Alasan</th>
                            @can('view-bookmarks')
                                <th class="align-middle fw-semibold text-dark">Status</th>
                            @endcan
                            @if (Auth::user()->role === \App\Enums\UserRole::SUPERADMIN || Auth::user()->role === \App\Enums\UserRole::ADMIN)
                                <th class="align-middle fw-semibold text-dark">Status</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($storageRequests as $index => $request)
                            <tr>
                                @can('view-bookmarks')
                                    <td class="text-center align-middle text-secondary" style="border-bottom: 1px solid #ddd;">
                                        {{ $index + 1 }}</td>
                                @endcan
                                <td class="text-center align-middle text-secondary" style="border-bottom: 1px solid #ddd;">
                                    {{ \Carbon\Carbon::parse($request->created_at)->setTimezone('Asia/Jakarta')->format('Y-m-d | H:i') }}
                                </td>
                                @if (Auth::user()->role === \App\Enums\UserRole::SUPERADMIN || Auth::user()->role === \App\Enums\UserRole::ADMIN)
                                    <td class="text-center align-middle" style="border-bottom: 1px solid #ddd;">
                                        <!-- Gambar Profil Owner -->
                                        <img src="{{ $request->owner->avatar ? route('profile.avatar.show', ['filename' => $request->owner->avatar]) : asset('/images/avatar.png') }}"
                                            class="rounded-circle d-block mx-auto" alt="{{ $request->owner->name }}"
                                            style="width: 40px; height: 40px; object-fit: cover;">
                                    </td>
                                    <td class="text-center align-middle text-secondary"
                                        style="border-bottom: 1px solid #ddd; max-width: 20px;">
                                        <div style="overflow: hidden; white-space: nowrap; text-overflow: ellipsis;">
                                            <strong>{{ $request->owner->name }}</strong>
                                        </div>
                                    </td>
                                @endif
                                <td class="text-center align-middle text-secondary" style="border-bottom: 1px solid #ddd;">
                                    <strong>{{ $request->request_quota }}</strong>
                                </td>
                                <td class="text-left align-middle text-secondary"
                                    style="max-width: 150px; position: relative; border-bottom: 1px solid #ddd;">
                                    <!-- Alasan dengan potongan teks -->
                                    <div class="reason-container"
                                        style="overflow: hidden; white-space: nowrap; text-overflow: ellipsis;">
                                        {{ $request->reason }}
                                    </div>

                                    <!-- Ikon panah hanya jika teks dipotong -->
                                    <span class="toggle-arrow mr-2"
                                        style="position: absolute; right: -13px; top: 50%; transform: translateY(-50%); cursor: pointer; display: none;"
                                        onclick="toggleReason(this)">
                                        <i class="fa-solid fa-sort-down"></i> <!-- Bootstrap Icon -->
                                    </span>

                                    <!-- Alasan penuh yang disembunyikan awalnya -->
                                    <div class="reason-full d-none mt-2">
                                        {{ $request->reason }}
                                    </div>
                                </td>
                                <script>
                                    document.addEventListener("DOMContentLoaded", function() {
                                        const containers = document.querySelectorAll('.reason-container');

                                        containers.forEach(container => {
                                            // Dapatkan elemen toggle-arrow
                                            const arrow = container.parentElement.querySelector('.toggle-arrow');
                                            const fullText = container.innerText;

                                            // Deteksi jika teks dipotong
                                            if (container.scrollWidth > container.clientWidth) {
                                                arrow.style.display = 'inline'; // Tampilkan panah
                                            }

                                            // Toggle alasan penuh
                                            window.toggleReason = function(element) {
                                                const fullReason = element.parentElement.querySelector('.reason-full');
                                                const container = element.parentElement.querySelector('.reason-container');
                                                const icon = element.querySelector('i');

                                                if (fullReason.classList.contains('d-none')) {
                                                    // Tampilkan alasan penuh
                                                    fullReason.classList.remove('d-none');
                                                    container.style.display = 'none';
                                                    icon.classList.remove('fa-sort-down');
                                                    icon.classList.add('fa-sort-up');
                                                } else {
                                                    // Sembunyikan alasan penuh
                                                    fullReason.classList.add('d-none');
                                                    container.style.display = 'block';
                                                    icon.classList.remove('fa-sort-up');
                                                    icon.classList.add('fa-sort-down');
                                                }
                                            };
                                        });
                                    });
                                </script>

                                @can('view-bookmarks')
                                    <td class="text-center align-middle" style="border-bottom: 1px solid #ddd;">
                                        @if ($request->status === \App\Enums\StorageRequestStatus::PENDING)
                                            <span class="badge bg-warning text-dark">Proses</span>
                                        @elseif ($request->status === \App\Enums\StorageRequestStatus::APPROVED)
                                            <span class="badge bg-success">Diterima</span>
                                        @elseif ($request->status === \App\Enums\StorageRequestStatus::REJECTED)
                                            <span class="badge bg-danger">Ditolak</span>
                                        @endif
                                    </td>
                                @endcan
                                @if (Auth::user()->role === \App\Enums\UserRole::SUPERADMIN || Auth::user()->role === \App\Enums\UserRole::ADMIN)
                                    <td class="text-center align-middle ml-2" style="border-bottom: 1px solid #ddd;">
                                        @if ($request->status === \App\Enums\StorageRequestStatus::PENDING)
                                            <form method="POST"
                                                action="{{ route('storage-requests.update', $request) }}">
                                                @csrf
                                                @method('PUT')
                                                <select name="status"
                                                    class="form-select form-select-sm border-primary shadow-sm"
                                                    style="border-width: 1px; width: auto; display: inline-block;"
                                                    onchange="this.form.submit()">
                                                    <option value="" disabled selected>Status</option>
                                                    <option
                                                        value="{{ \App\Enums\StorageRequestStatus::APPROVED->value }}">
                                                        Diterima
                                                    </option>
                                                    <option
                                                        value="{{ \App\Enums\StorageRequestStatus::REJECTED->value }}">
                                                        Ditolak
                                                    </option>
                                                </select>
                                            </form>
                                        @else
                                            @if ($request->status === \App\Enums\StorageRequestStatus::APPROVED)
                                                <span class="badge bg-success">Diterima</span>
                                            @elseif ($request->status === \App\Enums\StorageRequestStatus::REJECTED)
                                                <span class="badge bg-danger">Ditolak</span>
                                            @endif
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $storageRequests->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection
