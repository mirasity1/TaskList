<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function index()
    {
        // return status by order /order
        return Status::orderBy('order', 'desc')->get();
    }

    public function store(Request $request)
    {
        $status = Status::create($request->all());
        return response()->json($status, 201);
    }

    public function show(Status $status)
    {
        return $status;
    }
    // update statuses (mainly order)
    public function update(Request $request, $id) // Altere para receber $id
    {
        $status = Status::find($id); // Busque o status pelo ID
    
        if (!$status) {
            return response()->json(['error' => 'Status not found'], 404);
        }
    
        $request->validate([
            'order' => 'required|integer',
        ]);
    
        $status->update($request->only(['order']));
    
        return response()->json($status, 200);
    }

    public function destroy(Status $status)
    {
        $status->delete();
        return response()->json(null, 204);
    }
}
