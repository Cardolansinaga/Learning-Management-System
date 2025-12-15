<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index($courseId)
    {
        $assignments = Assignment::where('course_id', $courseId)->get();
        return response()->json($assignments);
    }

    public function store(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);

        if ($request->user()->id !== $course->teacher_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'required|date',
        ]);

        $assignment = Assignment::create([
            'course_id' => $courseId,
            'title' => $request->title,
            'description' => $request->description,
            'deadline' => $request->deadline,
        ]);

        return response()->json($assignment, 201);
    }

    public function show($id)
    {
        $assignment = Assignment::with('submissions')->findOrFail($id);
        return response()->json($assignment);
    }

    public function destroy(Request $request, $id)
    {
        $assignment = Assignment::findOrFail($id);
        $course = $assignment->course;

        if ($request->user()->id !== $course->teacher_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $assignment->delete();

        return response()->json(['message' => 'Assignment deleted successfully']);
    }
}
