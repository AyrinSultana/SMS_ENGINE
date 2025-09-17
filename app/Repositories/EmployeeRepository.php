<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class EmployeeRepository
{
    public function getAuthorizers()
    {
        return DB::connection('mysql2')->table('apiusers')->select('full_name','email_address')->get();
    }
}
