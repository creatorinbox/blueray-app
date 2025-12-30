<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $expenses = DB::table('expenses')
            ->leftJoin('expense_categories', 'expenses.category_id', '=', 'expense_categories.id')
            ->leftJoin('expense_sub_categories', 'expenses.sub_category_id', '=', 'expense_sub_categories.id')
            ->leftJoin('customers', 'expenses.customer_id', '=', 'customers.id')
            ->leftJoin('users', 'expenses.created_by', '=', 'users.id')
            ->select(
                'expenses.*',
                'expense_categories.category_name',
                'expense_sub_categories.sub_category_name',
                'customers.customer_name',
                'users.name as created_by_name'
            )
            ->where('expenses.company_id', auth()->user()->company_id)
            ->orderBy('expenses.expense_date', 'desc')
            ->get();

        $stats = [
            'total_expenses' => $expenses->count(),
            'total_amount' => $expenses->sum('total_amount'),
            'this_month' => $expenses->where('expense_date', '>=', now()->startOfMonth())->sum('total_amount'),
            'this_week' => $expenses->where('expense_date', '>=', now()->startOfWeek())->sum('total_amount'),
        ];

        return view('expenses.index', compact('expenses', 'stats'));
    }

    public function create()
    {
        $categories = DB::table('expense_categories')
            ->where('status', 1)
            ->get();

        $subCategories = DB::table('expense_sub_categories')
            ->where('status', 1)
            ->get();

        $customers = DB::table('customers')
            ->where('company_id', auth()->user()->company_id)
            ->get();

        return view('expenses.create', compact('categories', 'subCategories', 'customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_date' => 'required|date',
            'category_id' => 'required|exists:expense_categories,id',
            'sub_category_id' => 'nullable|exists:expense_sub_categories,id',
            'customer_id' => 'nullable|exists:customers,id',
            'expense_for' => 'required|string|max:255',
            'expense_amount' => 'required|numeric|min:0',
            'vat_type' => 'required|in:withoutvat,vat',
            'total_amount' => 'required|numeric|min:0',
            'vehicle_no' => 'nullable|string|max:255',
            'reference_no' => 'nullable|string|max:255',
            'note' => 'nullable|string',
        ]);

        try {
            $validated['company_id'] = auth()->user()->company_id;
            $validated['created_by'] = auth()->id();

            DB::table('expenses')->insert([
                ...$validated,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('expenses.index')->with('success', 'Expense created successfully');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error creating expense: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $expense = DB::table('expenses')
            ->leftJoin('expense_categories', 'expenses.category_id', '=', 'expense_categories.id')
            ->leftJoin('expense_sub_categories', 'expenses.sub_category_id', '=', 'expense_sub_categories.id')
            ->leftJoin('customers', 'expenses.customer_id', '=', 'customers.id')
            ->leftJoin('users', 'expenses.created_by', '=', 'users.id')
            ->select(
                'expenses.*',
                'expense_categories.category_name',
                'expense_sub_categories.sub_category_name',
                'customers.customer_name',
                'users.name as created_by_name'
            )
            ->where('expenses.id', $id)
            ->where('expenses.company_id', auth()->user()->company_id)
            ->first();

        if (!$expense) {
            return redirect()->route('expenses.index')->with('error', 'Expense not found');
        }

        return view('expenses.show', compact('expense'));
    }

    public function edit($id)
    {
        $expense = DB::table('expenses')
            ->where('id', $id)
            ->where('company_id', auth()->user()->company_id)
            ->first();

        if (!$expense) {
            return redirect()->route('expenses.index')->with('error', 'Expense not found');
        }

        $categories = DB::table('expense_categories')
            ->where('status', 1)
            ->get();

        $customers = DB::table('customers')
            ->where('company_id', auth()->user()->company_id)
            ->get();

        return view('expenses.edit', compact('expense', 'categories', 'customers'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'expense_date' => 'required|date',
            'category_id' => 'required|exists:expense_categories,id',
            'sub_category_id' => 'nullable|exists:expense_sub_categories,id',
            'customer_id' => 'nullable|exists:customers,id',
            'expense_for' => 'required|string|max:255',
            'expense_amount' => 'required|numeric|min:0',
            'vat_type' => 'required|in:withoutvat,vat',
            'total_amount' => 'required|numeric|min:0',
            'vehicle_no' => 'nullable|string|max:255',
            'reference_no' => 'nullable|string|max:255',
            'note' => 'nullable|string',
        ]);

        $expense = DB::table('expenses')
            ->where('id', $id)
            ->where('company_id', auth()->user()->company_id)
            ->first();

        if (!$expense) {
            return redirect()->route('expenses.index')->with('error', 'Expense not found');
        }

        try {
            DB::table('expenses')
                ->where('id', $id)
                ->update([
                    ...$validated,
                    'updated_at' => now(),
                ]);

            return redirect()->route('expenses.index')->with('success', 'Expense updated successfully');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error updating expense: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $expense = DB::table('expenses')
            ->where('id', $id)
            ->where('company_id', auth()->user()->company_id)
            ->first();

        if (!$expense) {
            return redirect()->route('expenses.index')->with('error', 'Expense not found');
        }

        try {
            DB::table('expenses')->where('id', $id)->delete();
            return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully');
        } catch (\Exception $e) {
            return redirect()->route('expenses.index')->with('error', 'Error deleting expense: ' . $e->getMessage());
        }
    }

    public function getSubCategories($categoryId)
    {
        $subCategories = DB::table('expense_sub_categories')
            ->where('category_id', $categoryId)
            ->where('status', 1)
            ->get();

        return response()->json($subCategories);
    }
}
