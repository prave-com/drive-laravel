@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-5 mt-5">
            <h3>Log Aktivitas</h3>

            <form method="GET" action="{{ route('activity-logs.index') }}" class="d-flex">
                <div class="input-group" style="border: 2px solid #7f7f7f; border-radius: 20px; overflow: hidden;">
                    <!-- Button on the left -->
                    <button class="btn border-none bg-transparent" type="submit"
                        style="border: none; border-radius: 20px 0 0 20px;">
                        <i class='bx bx-search-alt file-icon text-secondary fw-bold'></i>
                    </button>

                    <!-- Input on the right -->
                    <input type="text" class="form-control border-none" name="search" placeholder="Cari"
                        value="{{ $search ?? '' }}"
                        style="max-width: 300px; border: none; outline: none; box-shadow: none;">

                    <!-- Close button to clear the search, only visible if there is a search query -->
                    @if (!empty($search))
                        <a href="{{ route('activity-logs.index') }}" class="btn btn-link text-secondary align-middle"
                            style="border-radius: 20px 0 0 20px;">
                            <i class='bx bx-x'></i> <!-- Close icon to clear the search -->
                        </a>
                    @endif
                </div>
            </form>

        </div>

        <!-- Activity Log Table -->
        <table class="table table-hover">
            <thead class="text-center"
                style="border-bottom: 4px solid #ddd; position: relative; box-shadow: 0 6px 4px -4px rgba(0, 0, 0, 0.3); z-index: 1;">
                <tr>
                    <th class="fw-semibold text-dark">Waktu</th>
                    <th class="fw-semibold text-dark">Pengguna</th>
                    <th class="fw-semibold text-dark">Aktivitas</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($logs as $log)
                    <tr>
                        <!-- Timeline Column -->
                        <td style="border: none; position: relative; padding: 16px 24px; text-align: right;">
                            <!-- Timeline Content -->
                            <span style="color: #7f7f7f; font-size: 14px;">
                                {{ \Carbon\Carbon::parse($log->created_at)->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s') }}
                            </span>

                            <!-- Timeline Line -->
                            <div
                                style="position: absolute; right: 15px; top: 0; bottom: 0; width: 2px; background-color: rgba(127, 148, 250, 0.3);">
                            </div>

                            <!-- Timeline Dot -->
                            <div
                                style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); width: 10px; height: 10px; background-color: #7f94fa; border-radius: 50%;">
                            </div>
                        </td>

                        <!-- User Column -->
                        <td class="align-middle"
                            style="border-right: 3px solid rgba(127, 148, 250, 0.3); border-left: none; border-bottom: none;">
                            @if ($log->causer)
                                <a href="{{ route('superadmin.users.edit', $log->causer) }}"
                                    class="text-dark text-decoration-none fw-bold text-truncate"
                                    style="max-width: 200px; display: inline-block;">
                                    {{ $log->causer->email }}
                                </a>
                            @else
                                N/A
                            @endif
                        </td>

                        <!-- Description Column -->
                        <td class="align-middle"
                            style="border-left: 1px solid rgba(127, 148, 250, 0.3); border-right: none; border-bottom: none;">
                            @php
                                $words = explode(' ', $log->description);
                                $firstWord = array_shift($words);
                                $secondWord = array_shift($words);
                                $remainingWords = implode(' ', $words);
                            @endphp
                            <strong>{{ $firstWord }}</strong> {{ $secondWord }} "<span>{{ $remainingWords }}</span>"
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        {{ $logs->appends(request()->query())->links() }}
    </div>
@endsection
