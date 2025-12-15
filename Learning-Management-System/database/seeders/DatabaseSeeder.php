<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Material;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create specific Dosen
        $dosen = User::factory()->create([
            'name' => 'Pak Dosen',
            'email' => 'dosen@example.com',
            'password' => bcrypt('password'),
            'role' => 'dosen',
        ]);

        // 2. Create specific Mahasiswa
        $mahasiswa = User::factory()->create([
            'name' => 'Si Mahasiswa',
            'email' => 'mahasiswa@example.com',
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
        ]);

        // 3. Create random users
        User::factory(2)->create(['role' => 'dosen']);
        $students = User::factory(2)->create(['role' => 'mahasiswa']);

        // 4. Create Courses for the specific Dosen
        $courses = Course::factory(3)->create([
            'teacher_id' => $dosen->id,
        ]);

        foreach ($courses as $course) {
            // 5. Create Materials for each course
            Material::factory(5)->create([
                'course_id' => $course->id,
            ]);

            // 6. Create Assignments for each course
            $assignments = Assignment::factory(3)->create([
                'course_id' => $course->id,
            ]);

            // 7. Create Submissions for each assignment from the specific Mahasiswa and random students
            foreach ($assignments as $assignment) {
                // Submission from specific Mahasiswa
                Submission::factory()->create([
                    'assignment_id' => $assignment->id,
                    'student_id' => $mahasiswa->id,
                ]);
            }
        }
    }
}
