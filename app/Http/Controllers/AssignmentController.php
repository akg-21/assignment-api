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
                'message' => $assignments->count() > 0 ? "Assignments retrieved successfully" : "No assignments found",
                'data' =>  $assignments->map(function ($assignment) {
                    return [
                        'id' => $assignment->id,
                        'title' => $assignment->title,
                        'description' => $assignment->description,
                        'subject' => $assignment->subject,
                        'status' => $assignment->status,
                        'user_id' => $assignment->user_id
                    ];
                })
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
                    'id' => $assignment->id,
                    'title' => $assignment->title,
                    'description' => $assignment->description,
                    'subject' => $assignment->subject,
                    'status' => $assignment->status,
                    'user_id' => $assignment->user_id
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
                    'message' => 'Assignment not found or access denied',
                    'error_code' => 'ASSIGNMENT_NOT_FOUND'
                ], 404);
            }

            // Additional check: Ensure the assignment belongs to the authenticated user
            if ($assignment->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied',
                    'error_code' => 'ACCESS_DENIED'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $assignment->id,
                    'title' => $assignment->title,
                    'description' => $assignment->description,
                    'subject' => $assignment->subject,
                    'status' => $assignment->status,
                    'user_id' => $assignment->user_id
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
                    'message' => 'Assignment not found or access denied',
                    'error_code' => 'ASSIGNMENT_NOT_FOUND'
                ], 404);
            }

            // Additional check: Ensure the assignment belongs to the authenticated user
            if ($assignment->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied',
                    'error_code' => 'ACCESS_DENIED'
                ], 403);
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
                    'id' => $assignment->id,
                    'title' => $assignment->title,
                    'description' => $assignment->description,
                    'subject' => $assignment->subject,
                    'status' => $assignment->status,
                    'user_id' => $assignment->user_id
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
                    'message' => 'Assignment not found or access denied',
                    'error_code' => 'ASSIGNMENT_NOT_FOUND'
                ], 404);
            }

            // Additional check: Ensure the assignment belongs to the authenticated user
            if ($assignment->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied',
                    'error_code' => 'ACCESS_DENIED'
                ], 403);
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
