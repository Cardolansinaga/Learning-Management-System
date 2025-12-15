<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    public function index($courseId)
    {
        $materials = Material::where('course_id', $courseId)->get();
        return response()->json($materials);
    }

    public function store(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);

        if ($request->user()->id !== $course->teacher_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:10240', // 10MB max
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('materials', 'public');

            $material = Material::create([
                'course_id' => $courseId,
                'title' => $request->title,
                'file_path' => $path,
                'description' => $request->description,
            ]);

            return response()->json($material, 201);
        }

        return response()->json(['message' => 'File upload failed'], 400);
    }

    public function show($id)
    {
        $material = Material::findOrFail($id);
        return response()->json($material);
    }

    public function destroy(Request $request, $id)
    {
        $material = Material::findOrFail($id);
        $course = $material->course;

        if ($request->user()->id !== $course->teacher_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        Storage::disk('public')->delete($material->file_path);
        $material->delete();

        return response()->json(['message' => 'Material deleted successfully']);
    }
}
