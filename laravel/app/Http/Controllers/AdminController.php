<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Test;
use App\Models\Question;
use App\Models\Category;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'users_count' => User::count(),
            'tests_count' => Test::count(),
            'questions_count' => Question::count(),
            'categories_count' => Category::count(),
            'active_tests' => Test::where('status', 'in_progress')->count(),
            'completed_tests' => Test::where('status', 'completed')->count(),
        ];

        $recentUsers = User::latest()->take(5)->get();
        $recentTests = Test::with('user')->latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentTests'));
    }

    public function users(Request $request)
    {
        $query = User::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%");
            });
        }

        if ($role = $request->get('role')) {
            $query->where('role', $role);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.users', compact('users'));
    }

    public function updateUserRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:demo,user,admin'
        ]);

        $user->update(['role' => $request->role]);

        return redirect()->back()->with('success', __('app.role_updated_successfully'));
    }

    public function tests(Request $request)
    {
        $query = Test::with('user');

        if ($search = $request->get('search')) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('email', 'LIKE', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $tests = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.tests', compact('tests'));
    }
}