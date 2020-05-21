<?php

use App\Product;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('seller_id');
            $table->string('name');
            $table->string('description', 1000);
            $table->unsignedInteger('quantity');
            $table->enum('status', [Product::PRODUCTO_DISPONIBLE, Product::PRODUCTO_NO_DISPONIBLE])->default(Product::PRODUCTO_NO_DISPONIBLE);
            $table->string('image');

            $table->foreign('seller_id')->references('id')->on('users')->onDelete('CASCADE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
