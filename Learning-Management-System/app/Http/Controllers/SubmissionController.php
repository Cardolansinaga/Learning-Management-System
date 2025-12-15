<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    public function index(Request $request, $assignmentId)
    {
        $assignment = Assignment::findOrFail($assignmentId);
        
        // Check if user is the teacher of the course
        if ($request->user()->id !== $assignment->course->teacher_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $submissions = Submission::with('student')->where('assignment_id', $assignmentId)->get();
        return response()->json($submissions);
    }

    public function store(Request $request, $assignmentId)
    {
        $assignment = Assignment::findOrFail($assignmentId);

        if ($request->user()->role !== 'mahasiswa') {
            return response()->json(['message' => 'Only students can submit assignments'], 403);
        }

        // Check if already submitted
        $existingSubmission = Submission::where('assignment_id', $assignmentId)
            ->where('student_id', $request->user()->id)
            ->first();

        if ($existingSubmission) {
            return response()->json(['message' => 'You have already submitted this assignment'], 400);
        }

        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('submissions', 'public');

            $submission = Submission::create([
                'assignment_id' => $assignmentId,
                'student_id' => $request->user()->id,
                'file_path' => $path,
            ]);

            return response()->json($submission, 201);
        }

        return response()->json(['message' => 'File upload failed'], 400);
    }

    public function update(Request $request, $id)
    {
        $submission = Submission::findOrFail($id);
        $assignment = $submission->assignment;

        if ($request->user()->id !== $assignment->course->teacher_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'grade' => 'required|integer|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        $submission->update([
            'grade' => $request->grade,
            'feedback' => $request->feedback,
        ]);

        return response()->json($submission);
    }
}
