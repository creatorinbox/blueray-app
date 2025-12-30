<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $stats = [
            'total_quotations' => \App\Models\Quotation::count(),
            'pending_approvals' => \App\Models\Quotation::where('approval_status', 'Submitted')->count(),
            'total_customers' => \App\Models\Customer::where('is_active', true)->count(),
            'total_items' => \App\Models\Item::where('is_active', true)->count(),
        ];
        
        $recentQuotations = \App\Models\Quotation::with('customer')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        return view('dashboard', compact('stats', 'recentQuotations'));
    }
}
