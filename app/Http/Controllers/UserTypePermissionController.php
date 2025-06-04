<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\UserTypePermission;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;

class UserTypePermissionController extends Controller
{

    public function deleteRole($userType)
    {
        UserTypePermission::where('user_type', $userType)->delete();
        return response()->json(['message' => 'Role deleted']);
    }
    

    public function editRole(Request $request)
{
    $request->validate([
        'user_type' => 'required|string',
        'permissions' => 'required|array'
    ]);

    // Delete old permissions
    UserTypePermission::where('user_type', $request->user_type)->delete();

    // Re-insert updated permissions
    foreach ($request->permissions as $permName) {
        $permission = Permission::firstOrCreate(['name' => $permName]);

        UserTypePermission::create([
            'user_type' => $request->user_type,
            'permission_id' => $permission->id
        ]);
    }

    return response()->json(['message' => 'Permissions updated successfully']);
}


    public function getAllRoles()
    {
        Log::info('getAllRoles() endpoint was hit');
    
        // Group permissions under each user_type (role)
        $roles = UserTypePermission::with('permission')
            ->get()
            ->groupBy('user_type')
            ->map(function ($group) {
                return $group->map(function ($item) {
                    return $item->permission->name ?? null;
                })->filter()->values();
            });
    
        Log::info('Roles with permissions:', $roles->toArray());
    
        return response()->json($roles);
    }
    


    public function store(Request $request)
{
    $request->validate([
        'user_type' => 'required|string',
        'permissions' => 'required|array'
    ]);

    foreach ($request->permissions as $permName) {
        $permission = Permission::firstOrCreate(['name' => $permName]);

        UserTypePermission::create([
            'user_type' => $request->user_type,
            'permission_id' => $permission->id
        ]);
    }

    return response()->json(['message' => 'Permissions saved successfully']);
}


    

    public function getPermissionsForUserType($userType)
    {
        $permissions = UserTypePermission::where('user_type', $userType)
            ->with('permission')
            ->get()
            ->pluck('permission.name');

        return response()->json($permissions);
    }
}
