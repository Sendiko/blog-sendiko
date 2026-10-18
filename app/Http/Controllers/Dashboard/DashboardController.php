<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $posts = $request->user()->posts();

        return view('dashboard', [
            'totalPosts' => (clone $posts)->count(),
            'publishedPosts' => (clone $posts)->where('status', 'published')->count(),
            'draftPosts' => (clone $posts)->where('status', 'draft')->count(),
            'totalViews' => (clone $posts)->sum('views'),
            'recentPosts' => (clone $posts)->latest('updated_at')->limit(5)->get(),
        ]);
    }
}
