<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function login(Request $request)
    {
        //We are getting the post using request and we are validating that the name email and password
        // in the form is required and added in. addionally for the email we are specifying it is email
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // this is eloquent orm by lravel :: where. it get the first email from the admin email column where it matches
        // the email entered by getting it from the request x
        $admin = Admin::where('email', $request->email)->first();

        // condition to check if the admin was !found based on email bascaially null and or if the has password doesnt match
        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json(['message' => "Invalid email or password."], 401);
        }

        $token = $admin->createToken('adminToken', ['admin'])->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'admin' => $admin,
            'type' => Auth::guard('admin')->user(),
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Admin Logged out'
        ]);
    }


    public function register(Request $request)
    {
        $ad = Admin::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'contact_number' => $request->contact_number,
        ]);

        $role = Role::where('name', $request->role)->first();
        $admin = Admin::find($ad->id);
        $admin->roles()->attach($role);
        return response()->json([
            'message' => 'Admin registration successful',
            'admin' => $admin,
            'status' => 'success'
        ]);
    }

    public function allRoles()
    {
        $roles = Role::all();
        return response()->json(['roles' => $roles]);
    }

    public function allPermissions()
    {
        $permissions = Permission::all();
        return response()->json(['permissions' => $permissions]);
    }

    public function allAdmins()
    {
        $admins = Admin::all();
        $admins->load('roles');
        return response()->json(['admins' => $admins]);
    }

    public function createRole(Request $request)
    {
        $Availaablepermissions = Permission::all()->keyBy('name');
        $role = Role::create([
            'name' => $request->name
        ]);
        $permissions = $request->permissions;
        foreach ($permissions as $permission) {
            $role->permissions()->attach($Availaablepermissions[$permission]->id);
        }
        return response()->json([
            'message' => 'Role created successfully',
            'role' => $role
        ]);
    }

    public function assignPermission(Request $request)
    {
        $Availaablepermissions = Permission::all()->keyBy('name');
        $role =  Role::where('name', $request->role)->first();

        $permissionIds = [];
        $newPermissions = $request->permissions;
        foreach ($newPermissions as $permission) {
            $permissionIds[] = $Availaablepermissions[$permission]->id;
        }
        $role->permissions()->sync($permissionIds);
        $role->save();
        return response()->json([
            'message' => 'successfully assigned permissions',
            'role' => $role,
            'permissions' => $role->permissions
        ], 200);
    }

    public function createPermission(Request $request)
    {
        $permission = Permission::create([
            'name' => $request->name
        ]);
        return response()->json([
            'message' => 'Permission created successfully',
            'permission' => $permission
        ], 200);
    }

    public function deleteRole(Request $request, $id)
    {
        $role = Role::find($id);
        if ($role) {
            $role->delete();
            return response()->json([
                'message' => 'Role deleted successfully'
            ], 200);
        } else {
            return response()->json([
                'message' => 'Role not found'
            ], 404);
        }
    }
}
