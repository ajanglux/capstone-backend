<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PageView;

class PageViewController extends Controller
{
    public function track(Request $request)
    {
        $validated = $request->validate([
            'page' => 'required|string'
        ]);

        $page = $validated['page'];

        $pageView = PageView::firstOrCreate(
            ['page' => $page],
            ['count' => 0]
        );

        $pageView->increment('count');
        $pageView->refresh();

        return response()->json(['count' => $pageView->count]);
    }

    public function get($page)
    {
        $pageView = PageView::where('page', $page)->first();

        return response()->json(['count' => $pageView->count ?? 0]);
    }
}
