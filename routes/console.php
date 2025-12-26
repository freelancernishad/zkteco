<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('zk:sync')->everyFiveSeconds()->withoutOverlapping();
