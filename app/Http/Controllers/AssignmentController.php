<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class AssignmentController extends Controller
{
    public function index(Request $request)
    {
        try {
            $assignments = $request->user()->assignments()
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'assignments' => $assignments
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve assignments',
                'error' => 'An unexpected error occurred while fetching assignments',
                'error_code' => 'FETCH_ASSIGNMENTS_ERROR'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            if (!$request->user()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No authenticated user found',
                    'error_code' => 'NOT_AUTHENTICATED'
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'subject' => 'required|string|max:100',
                'status' => 'sometimes|in:Pending,Submitted,Approved',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                    'error_code' => 'VALIDATION_ERROR'
                ], 422);
            }

            $assignment = $request->user()->assignments()->create([
                'title' => $request->title,
                'description' => $request->description,
                'subject' => $request->subject,
                'status' => $request->status ?? 'Pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Assignment created successfully',
                'data' => [
                    'assignment' => $assignment
                ]
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create assignment',
                'error' => 'An unexpected error occurred while creating assignment',
                'error_code' => 'CREATE_ASSIGNMENT_ERROR'
            ], 500);
        }
    }

    public function show(Request $request, $id)
    {
        try {
            if (!is_numeric($id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid assignment ID',
                    'error_code' => 'INVALID_ID'
                ], 400);
            }

            $assignment = $request->user()->assignments()->find($id);

            if (!$assignment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Assignment not found',
                    'error_code' => 'ASSIGNMENT_NOT_FOUND'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'assignment' => $assignment
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve assignment',
                'error' => 'An unexpected error occurred while fetching assignment',
                'error_code' => 'FETCH_ASSIGNMENT_ERROR'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            if (!is_numeric($id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid assignment ID',
                    'error_code' => 'INVALID_ID'
                ], 400);
            }

            $assignment = $request->user()->assignments()->find($id);

            if (!$assignment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Assignment not found',
                    'error_code' => 'ASSIGNMENT_NOT_FOUND'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|required|string',
                'subject' => 'sometimes|required|string|max:100',
                'status' => 'sometimes|required|in:Pending,Submitted,Approved',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                    'error_code' => 'VALIDATION_ERROR'
                ], 422);
            }

            $assignment->update($request->only([
                'title',
                'description',
                'subject',
                'status'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Assignment updated successfully',
                'data' => [
                    'assignment' => $assignment
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update assignment',
                'error' => 'An unexpected error occurred while updating assignment',
                'error_code' => 'UPDATE_ASSIGNMENT_ERROR'
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            if (!is_numeric($id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid assignment ID',
                    'error_code' => 'INVALID_ID'
                ], 400);
            }

            $assignment = $request->user()->assignments()->find($id);

            if (!$assignment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Assignment not found',
                    'error_code' => 'ASSIGNMENT_NOT_FOUND'
                ], 404);
            }

            $assignment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Assignment deleted successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete assignment',
                'error' => 'An unexpected error occurred while deleting assignment',
                'error_code' => 'DELETE_ASSIGNMENT_ERROR'
            ], 500);
        }
    }
}
