<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Department;

class FixUserDepartmentCommand extends Command
{
    protected $signature = 'fix:user-departments';
    protected $description = 'Fix user department assignments based on accountable person relationships';

    public function handle()
    {
        $this->info("Fixing user department assignments...");
        
        // Get all departments
        $departments = Department::all();
        
        foreach ($departments as $department) {
            if ($department->accountableper) {
                $user = User::find($department->accountableper);
                if ($user) {
                    $oldDeptId = $user->department_id;
                    $user->department_id = $department->departmentID;
                    $user->save();
                    
                    $this->info("Updated {$user->name} (ID: {$user->id}) department_id from {$oldDeptId} to {$department->departmentID} ({$department->officename})");
                } else {
                    $this->error("User with ID {$department->accountableper} not found for department {$department->officename}");
                }
            }
        }
        
        $this->info("User department assignments fixed!");
        return 0;
    }
}
