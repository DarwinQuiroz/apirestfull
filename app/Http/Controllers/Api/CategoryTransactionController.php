<?php

namespace App\Http\Controllers\Api;

use App\Category;
use App\Http\Controllers\ApiController;

class CategoryTransactionController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Category $category)
    {
        $transactions = $category->products()
        ->whereHas('transactions')
        ->with('transactions')->get()
        ->pluck('transactions')->collapse();

        return $this->showAll($transactions);
    }
}
