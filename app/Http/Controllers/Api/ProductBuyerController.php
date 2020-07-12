<?php

namespace App\Http\Controllers\Api;

use App\Product;
use App\Http\Controllers\ApiController;

class ProductBuyerController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Product $product)
    {
        $buyers = $product->transactions()
        ->with('buyer')->get()->pluck('buyer')
        ->unique('id')->values();

        return $this->showAll($buyers);
    }
}