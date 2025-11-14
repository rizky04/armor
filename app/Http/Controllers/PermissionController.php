<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        return view('permissions.index');
    }

    // ambil data semua permission
    // public function list()
    // {
    //     $permissions = Permission::paginate(100);
    //     return response()->json($permissions);
    // }
    public function list(Request $request)
{
    $query = Permission::query();

    if ($request->has('search') && $request->search != '') {
        $query->where('name', 'like', '%' . $request->search . '%')
              ->orWhere('guard_name', 'like', '%' . $request->search . '%');
    }

    $permissions = $query->orderBy('id', 'desc')->paginate(10);

    return response()->json($permissions);
}


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255|unique:permissions,name',
            'guard_name' => 'required|string|max:50',
        ]);

        $permission = Permission::create($validated);

        return response()->json(['success' => true, 'data' => $permission]);
    }

    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255|unique:permissions,name,' . $permission->id,
            'guard_name' => 'required|string|max:50',
        ]);

        $permission->update($validated);

        return response()->json(['success' => true, 'data' => $permission]);
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return response()->json(['success' => true]);
    }
}
