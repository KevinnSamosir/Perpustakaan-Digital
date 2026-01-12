<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class MemberController extends Controller
{
    /**
     * Display a listing of all members
     */
    public function index(Request $request)
    {
        try {
            $query = Member::with('user');

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->get('status'));
            }

            // Search by name or member number
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where('member_number', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $members = $query->paginate($perPage);

            return response()->json([
                'message' => 'Members retrieved successfully',
                'data' => $members->items(),
                'pagination' => [
                    'total' => $members->total(),
                    'per_page' => $members->perPage(),
                    'current_page' => $members->currentPage(),
                    'last_page' => $members->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve members',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created member
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
            ]);

            // Create user account
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'member',
            ]);

            // Create member profile
            $memberNumber = 'MBR' . str_pad($user->id, 5, '0', STR_PAD_LEFT);
            $member = Member::create([
                'user_id' => $user->id,
                'member_number' => $memberNumber,
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'join_date' => Carbon::now()->toDateString(),
                'status' => 'active',
            ]);

            return response()->json([
                'message' => 'Member created successfully',
                'data' => [
                    'member' => $member,
                    'user' => $user,
                ],
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create member',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified member
     */
    public function show(Member $member)
    {
        try {
            $member->load('user');
            return response()->json([
                'message' => 'Member retrieved successfully',
                'data' => $member,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Member not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified member
     */
    public function update(Request $request, Member $member)
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|email|max:255|unique:users,email,' . $member->user_id,
                'phone' => 'sometimes|nullable|string|max:20',
                'address' => 'sometimes|nullable|string',
                'status' => 'sometimes|in:active,inactive,suspended',
            ]);

            // Update user fields if provided
            if (isset($validated['name']) || isset($validated['email'])) {
                $member->user->update([
                    'name' => $validated['name'] ?? $member->user->name,
                    'email' => $validated['email'] ?? $member->user->email,
                ]);
            }

            // Update member fields
            $memberData = array_intersect_key($validated, array_flip(['phone', 'address', 'status']));
            $member->update($memberData);

            return response()->json([
                'message' => 'Member updated successfully',
                'data' => $member->load('user'),
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update member',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified member
     */
    public function destroy(Member $member)
    {
        try {
            // Check if member has active loans
            $activeLoans = $member->loans()->where('status', 'borrowed')->count();
            if ($activeLoans > 0) {
                return response()->json([
                    'message' => 'Cannot delete member with active loans',
                    'active_loans' => $activeLoans,
                ], 409);
            }

            $userId = $member->user_id;
            $member->delete();
            User::find($userId)->delete();

            return response()->json([
                'message' => 'Member deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete member',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
