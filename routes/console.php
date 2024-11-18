<?php

use App\Console\Commands\ProductRecap;
use Illuminate\Support\Facades\Schedule;

Schedule::command(ProductRecap::class)->dailyAt('23:59');
