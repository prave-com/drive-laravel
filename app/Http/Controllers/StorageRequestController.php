<?php

namespace App\Http\Controllers;

use App\Enums\StorageRequestStatus;
use App\Enums\UserRole;
use App\Http\Requests\IndexStorageRequestRequest;
use App\Http\Requests\StoreStorageRequestRequest;
use App\Http\Requests\UpdateStorageRequest;
use App\Models\StorageRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StorageRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexStorageRequestRequest $request)
    {
        $user = Auth::user();
        $search = $request->get('search'); // Capture the search query

        // Initialize the query for fetching storage requests
        $query = StorageRequest::with('owner');

        // If the user is admin or superadmin, allow searching by username and created_at
        if ($user->role === UserRole::SUPERADMIN || $user->role === UserRole::ADMIN) {
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('owner', function ($query) use ($search) {
                        $query->where('name', 'like', '%'.$search.'%');
                    })
                        ->orWhereDate('created_at', 'like', "%{$search}%"); // Search by date
                });
            }
        } else {
            // If the user is a regular user, only show their own requests
            $query->where('user_id', $user->id);
        }

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Paginate the results
        $storageRequests = $query->paginate(10);

        // Count the number of requests for each status
        $totalRequests = StorageRequest::count();
        $pendingRequests = StorageRequest::where('status', StorageRequestStatus::PENDING->value)->count();
        $approvedRequests = StorageRequest::where('status', StorageRequestStatus::APPROVED->value)->count();
        $rejectedRequests = StorageRequest::where('status', StorageRequestStatus::REJECTED->value)->count();

        // Pass the counts and search to the view
        return view('storage_requests.index', compact(
            'storageRequests',
            'totalRequests',
            'pendingRequests',
            'approvedRequests',
            'rejectedRequests',
            'search' // Pass the search query to the view
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('storage_requests.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStorageRequestRequest $request)
    {
        $user = Auth::user();
        $requestQuota = $request->request_quota == 5 || $request->request_quota == 10 ? $request->request_quota : $request->custom_quota;

        $storageRequest = StorageRequest::create([
            'request_quota' => $requestQuota,
            'reason' => $request->reason,
            'user_id' => $user->id,
        ]);

        activity()
            ->causedBy($user)
            ->performedOn($storageRequest)
            ->event('created')
            ->log("Create storage request {$requestQuota} GB");

        return redirect()->route('storage-requests.index')->with('success', 'Storage request submitted successfully!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStorageRequest $request, StorageRequest $storageRequest)
    {
        $user = Auth::user();
        $storageRequest->update([
            'status' => $request->status,
        ]);

        if ($request->status === StorageRequestStatus::APPROVED->value) {
            $storage = $storageRequest->owner->storage;

            // Convert request quota from GB to bytes (1 GB = 1073741824 bytes)
            $additionalQuota = $storageRequest->request_quota * 1073741824;

            // Increase the total_quota
            $storage->increment('total_quota', $additionalQuota);

            activity()
                ->causedBy($user)
                ->performedOn($storageRequest)
                ->event('updated')
                ->log("Accept storage request dari {$storageRequest->owner->email} dan menambahkan {$additionalQuota} bytes kuota");
        } else {
            activity()
                ->causedBy($user)
                ->performedOn($storageRequest)
                ->event('updated')
                ->log("Reject storage request dari {$storageRequest->owner->email}");
        }

        return redirect()->route('storage-requests.index')->with('success', 'Storage request status updated successfully!');
    }
}
