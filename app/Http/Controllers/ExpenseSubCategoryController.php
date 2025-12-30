<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseSubCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $subCategories = DB::table('expense_sub_categories')
            ->leftJoin('expense_categories', 'expense_sub_categories.category_id', '=', 'expense_categories.id')
            ->select(
                'expense_sub_categories.*',
                'expense_categories.category_name'
            )
            ->orderBy('expense_categories.category_name')
            ->orderBy('expense_sub_categories.sub_category_name')
            ->get();

        return view('expense-sub-categories.index', compact('subCategories'));
    }

    public function create()
    {
        $categories = DB::table('expense_categories')
            ->where('status', 1)
            ->orderBy('category_name')
            ->get();

        return view('expense-sub-categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:expense_categories,id',
            'sub_category_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Check if subcategory already exists for this category
        $exists = DB::table('expense_sub_categories')
            ->where('category_id', $validated['category_id'])
            ->where('sub_category_name', $validated['sub_category_name'])
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'This subcategory already exists for the selected category.');
        }

        try {
            DB::table('expense_sub_categories')->insert([
                'category_id' => $validated['category_id'],
                'sub_category_name' => $validated['sub_category_name'],
                'description' => $validated['description'],
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('expense-sub-categories.index')->with('success', 'Subcategory created successfully');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error creating subcategory: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $subCategory = DB::table('expense_sub_categories')->where('id', $id)->first();

        if (!$subCategory) {
            return redirect()->route('expense-sub-categories.index')->with('error', 'Subcategory not found');
        }

        $categories = DB::table('expense_categories')
            ->where('status', 1)
            ->orderBy('category_name')
            ->get();

        return view('expense-sub-categories.edit', compact('subCategory', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:expense_categories,id',
            'sub_category_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $subCategory = DB::table('expense_sub_categories')->where('id', $id)->first();

        if (!$subCategory) {
            return redirect()->route('expense-sub-categories.index')->with('error', 'Subcategory not found');
        }

        // Check if subcategory already exists for this category (excluding current record)
        $exists = DB::table('expense_sub_categories')
            ->where('category_id', $validated['category_id'])
            ->where('sub_category_name', $validated['sub_category_name'])
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'This subcategory already exists for the selected category.');
        }

        try {
            DB::table('expense_sub_categories')
                ->where('id', $id)
                ->update([
                    'category_id' => $validated['category_id'],
                    'sub_category_name' => $validated['sub_category_name'],
                    'description' => $validated['description'],
                    'updated_at' => now(),
                ]);

            return redirect()->route('expense-sub-categories.index')->with('success', 'Subcategory updated successfully');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error updating subcategory: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $subCategory = DB::table('expense_sub_categories')->where('id', $id)->first();

        if (!$subCategory) {
            return redirect()->route('expense-sub-categories.index')->with('error', 'Subcategory not found');
        }

        // Check if subcategory is being used
        $expenseCount = DB::table('expenses')->where('sub_category_id', $id)->count();

        if ($expenseCount > 0) {
            return redirect()->route('expense-sub-categories.index')
                ->with('error', 'Cannot delete subcategory. It is being used by expenses.');
        }

        try {
            DB::table('expense_sub_categories')->where('id', $id)->delete();
            return redirect()->route('expense-sub-categories.index')->with('success', 'Subcategory deleted successfully');
        } catch (\Exception $e) {
            return redirect()->route('expense-sub-categories.index')->with('error', 'Error deleting subcategory: ' . $e->getMessage());
        }
    }
}
