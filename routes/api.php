<?php

use Illuminate\Support\Facades\Route;
use Aliaswpeu\SferaApi\Http\Controllers\SubiektGTController;

Route::post('/kontrahent', [SubiektGTController::class, 'store']);