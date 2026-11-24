<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ApiInquiryStatusRequest;
use App\Http\Resources\Api\ContactInquiryResource;
use App\Models\ContactInquiry;
use App\Security\SecurityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ApiInquiryController extends Controller
{
    /**
     * List customer inquiries with pagination and filters.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if (Gate::forUser($user)->denies('viewAny', ContactInquiry::class)) {
            SecurityLogger::log('permission_denied', 'User lacked inquiries.view permission', 'warning', $user, $request);

            return response()->json([
                'status' => 'error',
                'code' => 'FORBIDDEN',
                'message' => 'You lack permission to access customer inquiries.',
            ], 403);
        }

        $query = ContactInquiry::latest();

        if ($request->has('status')) {
            $query->where('status', (string) $request->input('status'));
        }

        if ($request->has('search')) {
            $term = '%' . $request->input('search') . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhere('email', 'like', $term)
                  ->orWhere('company', 'like', $term);
            });
        }

        $perPage = min(max((int) $request->query('per_page', 15), 1), 50);
        $inquiries = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => ContactInquiryResource::collection($inquiries),
            'meta' => [
                'current_page' => $inquiries->currentPage(),
                'last_page' => $inquiries->lastPage(),
                'per_page' => $inquiries->perPage(),
                'total' => $inquiries->total(),
            ],
        ]);
    }

    /**
     * Retrieve single customer inquiry details.
     */
    public function show(Request $request, ContactInquiry $inquiry): JsonResponse
    {
        $user = $request->user();

        if (Gate::forUser($user)->denies('view', $inquiry)) {
            SecurityLogger::log('permission_denied', 'User lacked permission to view inquiry ID', 'warning', $user, $request, [
                'inquiry_id' => $inquiry->id,
            ]);

            return response()->json([
                'status' => 'error',
                'code' => 'FORBIDDEN',
                'message' => 'You do not have permission to view this customer inquiry.',
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => new ContactInquiryResource($inquiry),
        ]);
    }

    /**
     * Update inquiry status and admin notes.
     */
    public function updateStatus(ApiInquiryStatusRequest $request, ContactInquiry $inquiry): JsonResponse
    {
        $user = $request->user();

        if (Gate::forUser($user)->denies('update', $inquiry)) {
            SecurityLogger::log('permission_denied', 'User lacked inquiries.manage permission', 'warning', $user, $request, [
                'inquiry_id' => $inquiry->id,
            ]);

            return response()->json([
                'status' => 'error',
                'code' => 'FORBIDDEN',
                'message' => 'You do not have permission to update inquiry status.',
            ], 403);
        }

        $oldStatus = $inquiry->status;
        $inquiry->status = $request->input('status');
        if ($request->has('admin_notes')) {
            $inquiry->admin_notes = $request->input('admin_notes');
        }
        $inquiry->save();

        SecurityLogger::log('inquiry_status_updated', "Inquiry #{$inquiry->id} status changed from '{$oldStatus}' to '{$inquiry->status}' via API", 'info', $user, $request, [
            'inquiry_id' => $inquiry->id,
            'new_status' => $inquiry->status,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Inquiry status updated successfully.',
            'data' => new ContactInquiryResource($inquiry),
        ]);
    }
}

