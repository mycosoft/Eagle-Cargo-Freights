<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    /**
     * Display a listing of expense categories.
     */
    public function index()
    {
        $categories = ExpenseCategory::withCount('expenses')
            ->orderBy('name')
            ->paginate(20);

        return view('expense-categories.index', compact('categories'));
    }

    /**
     * Store a newly created expense category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
        ]);

        ExpenseCategory::create($validated);

        return redirect()->back()
            ->with('success', 'Expense category created successfully.');
    }

    /**
     * Update the specified expense category.
     */
    public function update(Request $request, ExpenseCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name,'.$category->id,
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
            'active' => 'boolean',
        ]);

        $category->update($validated);

        return redirect()->back()
            ->with('success', 'Expense category updated successfully.');
    }

    /**
     * Remove the specified expense category.
     */
    public function destroy(ExpenseCategory $category)
    {
        if ($category->expenses()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete category with associated expenses.');
        }

        $category->delete();

        return redirect()->back()
            ->with('success', 'Expense category deleted successfully.');
    }
}
