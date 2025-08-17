<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Department;

class CheckUsersCommand extends Command
{
    protected $signature = 'check:users';
    protected $description = 'Check user department assignments';

    public function handle()
    {
        $this->info("Users and their departments:");
        $this->info("ID | Name | Role | Department ID");
        $this->info("---|------|------|-------------");
        
        User::all(['id', 'name', 'role', 'department_id'])->each(function($user) {
            $deptName = $user->department_id ? Department::find($user->department_id)?->officename : 'None';
            $this->line("{$user->id} | {$user->name} | {$user->role} | {$user->department_id} ({$deptName})");
        });
        
        $this->info("\nDepartments and their accountable persons:");
        $this->info("ID | Department | Accountable Person ID | Accountable Person Name");
        $this->info("---|------------|---------------------|----------------------");
        
        Department::all(['departmentID', 'officename', 'accountableper'])->each(function($dept) {
            $accountablePerson = $dept->accountableper ? User::find($dept->accountableper)?->name : 'None';
            $this->line("{$dept->departmentID} | {$dept->officename} | {$dept->accountableper} | {$accountablePerson}");
        });
        
        return 0;
    }
}
