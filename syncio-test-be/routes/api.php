<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PayloadComparisonController;

Route::post('/payload/compare', [PayloadComparisonController::class, 'compare']);