<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $categories = DB::table('expense_categories')
            ->orderBy('category_name')
            ->get();

        return view('expense-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('expense-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_name' => 'required|string|max:255|unique:expense_categories,category_name',
            'description' => 'nullable|string',
        ]);

        try {
            DB::table('expense_categories')->insert([
                'category_name' => $validated['category_name'],
                'description' => $validated['description'],
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('expense-categories.index')->with('success', 'Category created successfully');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error creating category: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $category = DB::table('expense_categories')->where('id', $id)->first();

        if (!$category) {
            return redirect()->route('expense-categories.index')->with('error', 'Category not found');
        }

        return view('expense-categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'category_name' => 'required|string|max:255|unique:expense_categories,category_name,' . $id,
            'description' => 'nullable|string',
        ]);

        $category = DB::table('expense_categories')->where('id', $id)->first();

        if (!$category) {
            return redirect()->route('expense-categories.index')->with('error', 'Category not found');
        }

        try {
            DB::table('expense_categories')
                ->where('id', $id)
                ->update([
                    'category_name' => $validated['category_name'],
                    'description' => $validated['description'],
                    'updated_at' => now(),
                ]);

            return redirect()->route('expense-categories.index')->with('success', 'Category updated successfully');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error updating category: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $category = DB::table('expense_categories')->where('id', $id)->first();

        if (!$category) {
            return redirect()->route('expense-categories.index')->with('error', 'Category not found');
        }

        // Check if category is being used
        $expenseCount = DB::table('expenses')->where('category_id', $id)->count();
        $subCategoryCount = DB::table('expense_sub_categories')->where('category_id', $id)->count();

        if ($expenseCount > 0 || $subCategoryCount > 0) {
            return redirect()->route('expense-categories.index')
                ->with('error', 'Cannot delete category. It is being used by expenses or subcategories.');
        }

        try {
            DB::table('expense_categories')->where('id', $id)->delete();
            return redirect()->route('expense-categories.index')->with('success', 'Category deleted successfully');
        } catch (\Exception $e) {
            return redirect()->route('expense-categories.index')->with('error', 'Error deleting category: ' . $e->getMessage());
        }
    }
}
