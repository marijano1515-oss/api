<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private function index(Request $request)
    {

    }

    private function store(Request $request,)
    {
        if (!auth()->user() || auth()->user()->is_admin != 1)  {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
        ]);
        $category = CategoryController::create([
            'name' => $request->name
        ]);
        return response()->json([
            'category' => $category,
            'message' => 'Category created'
        ]);
    }

    private function update(Request $request)
    {
        if (!auth()->user() || auth()->user()->is_admin != 1)  {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
        ]);
        $category = CategoryController::update([
            'name' =>$request->name
        ]);
        return response()->json([
            'category' => $category,
            'message' => 'Category updated'
        ]);
    }

    private function show(Request $request)
    {

    }

    private function delete(Request $request)
    {

    }
}
