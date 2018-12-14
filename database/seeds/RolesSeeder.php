<?php

use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	if(DB::table('roles')->get()->count() == 0){
            
         $role = [
	            'name' => 'Super Administrator',
	            'slug' => 'super-administrator',
	            'permissions' => [
	                'super-administrator' => true,
	            ],
    		];

        $administratorRole = Sentinel::getRoleRepository()->createModel()->fill($role)->save();
        $administrator = [
            'email'    => 'admin@askrindo.com',
            'password' => 'admin1234!',
        ];
        
        $adminUser = Sentinel::registerAndActivate($administrator);
        $adminUser->roles()->attach($administratorRole);

        DB::table('roles')->insert([
            ['name' => 'Administrator', 'slug' => 'administrator'],
            ['name' => 'User', 'slug' => 'user'],
        ]);  
         
        }else{
             echo "\e[31mTable is not empty, therefore NOT "; 
        }
    }
}
