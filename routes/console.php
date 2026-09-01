<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('demo:reset', ['--force'])->dailyAt('03:00');
