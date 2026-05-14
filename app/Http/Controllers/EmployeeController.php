<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::with('user')->get();

        return EmployeeResource::collection($employees);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string'],
            'username' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string'],
            'telephone' => ['required', 'string']
        ]);

        $validated['password'] = bcrypt($validated['password']);

        $validated['name'] = $validated['username'];

        $user = User::create($validated);

        $employee = $user->employee()->create($validated);

        $employee->setRelation('user', $user);

        return new EmployeeResource($employee);

    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        $employee->load('user');

        return new EmployeeResource($employee);
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'full_name' => ['sometimes', 'string'],
            'username' => ['sometimes', 'string'],
            'email' => [
                'sometimes',
                'email',
                'unique:users,email,' . $employee->user_id
            ],
            'telephone' => ['sometimes', 'nullable', 'string']
        ]);

        // Update employee fields if present
        $employee->update([
            'full_name' => $validated['full_name'] ?? $employee->full_name,
            'telephone' => $validated['telephone'] ?? $employee->telephone,
        ]);

        // Update related user fields if present
        $employee->user()->update([
            'name' => $validated['username'] ?? $employee->user->name,
            'email' => $validated['email'] ?? $employee->user->email,
        ]);

        $employee->load('user');

        return new EmployeeResource($employee);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();

        return response()->json([
            'message' => "Delete sucessful"
       ], 200);
    }
}
