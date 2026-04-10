<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\UserCredentialsMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): View
    {
        $mode = $request->query('mode', 'active');
        $search = trim((string) $request->query('search', ''));

        $query = User::query();

        if ($mode === 'deleted') {
            $query->where('is_deleted', true);
        } elseif ($mode === 'all') {
            // no filter
        } else {
            $mode = 'active';
            $query->where('is_deleted', false);
        }

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        return view('backend.users.index', compact('users', 'mode', 'search'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        return view('backend.users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => 'required|string|in:Admin,SF001,SF002,SF003,Stock,PPC',
            'status' => 'required|boolean',
            'notify_via_email' => 'nullable|boolean',
        ]);

        // Store plain password before hashing (for email notification)
        $plainPassword = $validated['password'];
        $validated['password'] = Hash::make($validated['password']);
        $validated['is_deleted'] = false;

        $user = User::create($validated);

        // Send email notification if toggle is checked.
        // Temporarily disabled: credential emails are not being sent as of now.
        if ($request->has('notify_via_email') && $request->notify_via_email) {
            // try {
            //     Mail::to($user->email)->send(new UserCredentialsMail($user, $plainPassword));
            //     return redirect()->route('admin.users.index')
            //         ->with('success', 'User created successfully and credentials sent via email.');
            // } catch (\Exception $e) {
            //     return redirect()->route('admin.users.index')
            //         ->with('success', 'User created successfully, but failed to send email: ' . $e->getMessage());
            // }

            return redirect()->route('admin.users.index')
                ->with('success', 'User created successfully. Credential email sending is temporarily disabled.');
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): View
    {
        return view('backend.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        return view('backend.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string|in:Admin,SF001,SF002,SF003,Stock,PPC',
            'status' => 'required|boolean',
        ]);

        // Only update password if provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['required', 'confirmed', Password::defaults()],
            ]);
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Prevent deleting your own account
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        // Soft delete by setting is_deleted flag
        $user->update(['is_deleted' => true]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Toggle user status (active/inactive) via AJAX.
     */
    public function toggleStatus(User $user)
    {
        try {
            // Prevent deactivating your own account
            if ($user->id === Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot deactivate your own account.'
                ], 403);
            }

            $user->status = !$user->status;
            $user->save();

            return response()->json([
                'success' => true,
                'status' => $user->status,
                'message' => $user->status ? 'User activated successfully!' : 'User deactivated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the status.'
            ], 500);
        }
    }

    /**
     * Display user-wise login/logout activity report.
     */
    public function loginActivity(Request $request): View
    {
        $selectedUserId = $request->query('user_id');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $query = DB::table('user_login_activities as activities')
            ->select(
                'activities.id',
                'activities.user_id',
                'activities.login_at',
                'activities.logout_at',
                'activities.ip_address',
                'activities.user_agent',
                'users.name as user_name',
                'users.email as user_email',
                'users.role as user_role'
            )
            ->leftJoin('users', 'activities.user_id', '=', 'users.id')
            ->orderByDesc('activities.login_at');

        if (!empty($selectedUserId)) {
            $query->where('activities.user_id', $selectedUserId);
        }

        if (!empty($dateFrom)) {
            $query->whereDate('activities.login_at', '>=', $dateFrom);
        }

        if (!empty($dateTo)) {
            $query->whereDate('activities.login_at', '<=', $dateTo);
        }

        $activities = $query->paginate(20)->withQueryString();

        $users = User::query()
            ->select('id', 'name', 'email', 'role')
            ->where('is_deleted', false)
            ->orderBy('name')
            ->get();

        return view('backend.users.login-activity', compact('activities', 'users', 'selectedUserId', 'dateFrom', 'dateTo'));
    }
}
