<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('team-invitations:expire')->daily();
Schedule::command('memberships:cancel-unpaid')->daily();
Schedule::command('memberships:send-renewal-reminders')->daily();
Schedule::command('payments:send-due-reminders')->daily();
Schedule::command('registrations:cancel-expired')->hourly();
