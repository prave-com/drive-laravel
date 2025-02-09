<!-- resources/views/storage-requests/create.blade.php -->

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
    </style>

    <div class="container my-5">

        <ul class="nav nav-pills custom-tabs mb-3" id="pills-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="pills-formulir-tab" data-bs-toggle="pill" data-bs-target="#pills-formulir"
                    type="button" role="tab" aria-selected="true">Formulir</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('storage-requests.index') }}" role="tab"
                    aria-selected="false">Riwayat</a>
            </li>
        </ul>

        <form action="{{ route('storage-requests.store') }}" method="POST">
            @csrf

            <h5 class="form-title">Formulir Pengajuan Kapasitas.</h5>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="form-group">
                <label for="request_quota" class="form-label text-dark">Pilih Kapasitas:</label>
                <div class="btn-group d-block" role="group" aria-label="Quota options">
                    <input type="radio" class="btn-check" name="request_quota" id="quota5" value="5"
                        autocomplete="off">
                    <label class="btn btn-outline-dark" for="quota5">5 GB</label>

                    <input type="radio" class="btn-check" name="request_quota" id="quota10" value="10"
                        autocomplete="off">
                    <label class="btn btn-outline-dark" for="quota10">10 GB</label>
                </div>
                @error('request_quota')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group" id="custom_quota_group">
                <label for="custom_quota">Ukuran Kustom (GB):</label>
                <input type="number" name="custom_quota" id="custom_quota" class="form-control" min="1"
                    placeholder="0">
                @error('custom_quota')
                    <div class="text-danger">{{ $message }}</div>
                @enderror

            </div>

            <div class="form-group">
                <label for="reason" class="form-label">Alasan:</label>
                <textarea name="reason" id="reason" class="textarea form-control" rows="4"
                    placeholder="Tolong berikan alasan..." maxlength="200" required></textarea>
                <div class="char-count" id="charCount">0 / 200</div>
                @error('reason')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <p class="text-muted">*Permintaan akan diproses paling lambat 3x24 jam.</p>
            <button type="submit" class="btn btn-submit">Kirim</button>
        </form>
    </div>
@endsection
