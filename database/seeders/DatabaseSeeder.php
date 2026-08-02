<?php

namespace Database\Seeders;

use App\Models\Cohort;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Lecturer;
use App\Models\Programme;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    private const DEFAULT_PASSWORD = 'Tarumt@2026';

    private const STUDENT_COUNTS = [
        'DFT1(S1)G1' => 30,
        'DFT2(S1)G1' => 28,
        'DSF1(S1)G1' => 24,
        'DSF2(S1)G1' => 22,
        'RSD1(S1)G1' => 18,
        'RSD2(S1)G1' => 16,
        'RSD2(S1)G2' => 16,
        'RSD2(S1)G3' => 15,
        'RSD3(S1)G1' => 14,
        'RSD3(S1)G2' => 14,
        'RSD3(S1)G3' => 13,
        'RAF2(S3)G2' => 12,
        'RAF2(S3)G4' => 10,
        'RBU1(S1)G1' => 20,
    ];

    public function run(): void
    {
        $this->seedReferenceData();
        $this->seedUsers();
    }

    private function seedReferenceData(): void
    {
        $focs = Faculty::create(['faculty_code' => 'FOCS', 'faculty_name' => 'Faculty of Computer Science and Information Technology']);
        $fafb = Faculty::create(['faculty_code' => 'FAFB', 'faculty_name' => 'Faculty of Accountancy, Finance and Business']);

        $dcit = Department::create(['dept_code' => 'DCIT', 'dept_name' => 'Department of Computing and Information Technology', 'faculty_id' => $focs->id]);
        $dssh = Department::create(['dept_code' => 'DSSH', 'dept_name' => 'Department of Social Sciences and Humanities', 'faculty_id' => $focs->id]);
        $dacb = Department::create(['dept_code' => 'DACB', 'dept_name' => 'Department of Accounting and Business', 'faculty_id' => $fafb->id]);

        $dft = Programme::create(['programme_code' => 'DFT', 'programme_name' => 'Diploma in Information Technology', 'faculty_id' => $focs->id]);
        $dsf = Programme::create(['programme_code' => 'DSF', 'programme_name' => 'Diploma in Software Engineering', 'faculty_id' => $focs->id]);
        $rsd = Programme::create(['programme_code' => 'RSD', 'programme_name' => 'Bachelor in IT (Hons) Software Systems Development', 'faculty_id' => $focs->id]);
        $raf = Programme::create(['programme_code' => 'RAF', 'programme_name' => 'Bachelor in Accountancy (Hons)', 'faculty_id' => $fafb->id]);
        $rbu = Programme::create(['programme_code' => 'RBU', 'programme_name' => 'Bachelor in Business Administration (Hons)', 'faculty_id' => $fafb->id]);

        $cohortsData = [
            ['programme' => $dft, 'year' => 1, 'sem' => 1, 'group' => 1, 'academic_year' => '2025/26', 'intake' => 'June 2025'],
            ['programme' => $dft, 'year' => 2, 'sem' => 1, 'group' => 1, 'academic_year' => '2025/26', 'intake' => 'June 2024'],
            ['programme' => $dsf, 'year' => 1, 'sem' => 1, 'group' => 1, 'academic_year' => '2025/26', 'intake' => 'June 2025'],
            ['programme' => $dsf, 'year' => 2, 'sem' => 1, 'group' => 1, 'academic_year' => '2025/26', 'intake' => 'June 2024'],
            ['programme' => $rsd, 'year' => 1, 'sem' => 1, 'group' => 1, 'academic_year' => '2025/26', 'intake' => 'June 2025'],
            ['programme' => $rsd, 'year' => 2, 'sem' => 1, 'group' => 1, 'academic_year' => '2025/26', 'intake' => 'June 2024'],
            ['programme' => $rsd, 'year' => 2, 'sem' => 1, 'group' => 2, 'academic_year' => '2025/26', 'intake' => 'June 2024'],
            ['programme' => $rsd, 'year' => 2, 'sem' => 1, 'group' => 3, 'academic_year' => '2025/26', 'intake' => 'June 2024'],
            ['programme' => $rsd, 'year' => 3, 'sem' => 1, 'group' => 1, 'academic_year' => '2025/26', 'intake' => 'June 2023'],
            ['programme' => $rsd, 'year' => 3, 'sem' => 1, 'group' => 2, 'academic_year' => '2025/26', 'intake' => 'June 2023'],
            ['programme' => $rsd, 'year' => 3, 'sem' => 1, 'group' => 3, 'academic_year' => '2025/26', 'intake' => 'June 2023'],
            ['programme' => $raf, 'year' => 2, 'sem' => 3, 'group' => 2, 'academic_year' => '2025/26', 'intake' => 'June 2024'],
            ['programme' => $raf, 'year' => 2, 'sem' => 3, 'group' => 4, 'academic_year' => '2025/26', 'intake' => 'June 2024'],
            ['programme' => $rbu, 'year' => 1, 'sem' => 1, 'group' => 1, 'academic_year' => '2025/26', 'intake' => 'June 2025'],
        ];

        foreach ($cohortsData as $c) {
            Cohort::create([
                'programme_id' => $c['programme']->id,
                'current_year' => $c['year'],
                'semester' => $c['sem'],
                'tutorial_group' => $c['group'],
                'academic_year' => $c['academic_year'],
                'intake' => $c['intake'],
            ]);
        }
    }

    private function seedUsers(): void
    {
        $this->seedLecturers();
        $this->seedStudents();
    }

    private function seedLecturers(): void
    {
        $dcit = Department::where('dept_code', 'DCIT')->first();
        $dssh = Department::where('dept_code', 'DSSH')->first();
        $dacb = Department::where('dept_code', 'DACB')->first();

        $lecturersData = [
            ['staff_id' => '5425', 'name' => 'Pn. Surayaini Binti Basri', 'email' => 'surayaini@tarc.edu.my', 'is_pl' => true, 'dept' => $dcit],
            ['staff_id' => '5516', 'name' => 'En. Mohd Nur Rahmat Bin Mohd Taat', 'email' => 'mohdnurrahmat@tarc.edu.my', 'is_pl' => true, 'dept' => $dcit],
            ['staff_id' => '4288', 'name' => 'Dr. Christopher Lazarus', 'email' => 'christopherl@tarc.edu.my', 'is_pl' => false, 'dept' => $dcit],
            ['staff_id' => '3221', 'name' => 'Pn. Lee Yee Fong', 'email' => 'leeyeefong@tarc.edu.my', 'is_pl' => false, 'dept' => $dcit],
            ['staff_id' => '3825', 'name' => 'Pn. Teng Nga Sing', 'email' => 'tengns@tarc.edu.my', 'is_pl' => false, 'dept' => $dcit],
            ['staff_id' => '4127', 'name' => 'Pn. Patricia G Kissol', 'email' => 'patriciagk@tarc.edu.my', 'is_pl' => false, 'dept' => $dcit],
            ['staff_id' => '2873', 'name' => 'Cik Ellis Chieng', 'email' => 'chienge@tarc.edu.my', 'is_pl' => false, 'dept' => $dcit],
            ['staff_id' => '5514', 'name' => 'Ts. Norshikin Binti Zainal Abidin', 'email' => 'norshikin@tarc.edu.my', 'is_pl' => false, 'dept' => $dcit],
            ['staff_id' => '5599', 'name' => 'En. Jefther Edward', 'email' => 'jeftheredward@tarc.edu.my', 'is_pl' => false, 'dept' => $dcit],
            ['staff_id' => '5652', 'name' => 'En. Daniel Royd Michael', 'email' => 'danielroyd@tarc.edu.my', 'is_pl' => false, 'dept' => $dcit],
            ['staff_id' => '5770', 'name' => 'En. Lim Jia Zheng', 'email' => 'limjz@tarc.edu.my', 'is_pl' => false, 'dept' => $dcit],
            ['staff_id' => '3799', 'name' => 'En. Muada Bin Ojih', 'email' => 'muadao@tarc.edu.my', 'is_pl' => false, 'dept' => $dssh],
            ['staff_id' => '4363', 'name' => 'Pn. Tan Sharon', 'email' => 'tans@tarc.edu.my', 'is_pl' => false, 'dept' => $dacb],
            ['staff_id' => '5254', 'name' => 'Dr. Chang Foo Chung', 'email' => 'changfc@tarc.edu.my', 'is_pl' => false, 'dept' => $dacb],
        ];

        foreach ($lecturersData as $l) {
            $user = User::create([
                'name' => $l['name'],
                'email' => $l['email'],
                'password' => Hash::make(self::DEFAULT_PASSWORD),
                'role' => 'lecturer',
            ]);

            Lecturer::create([
                'user_id' => $user->id,
                'staff_id' => $l['staff_id'],
                'dept_id' => $l['dept']->id,
                'is_pl' => $l['is_pl'],
            ]);
        }
    }

    private function seedStudents(): void
    {
        $cohorts = Cohort::with('programme')->get();

        $counters = [];

        foreach ($cohorts as $cohort) {
            $studentCount = self::STUDENT_COUNTS[$this->cohortCode($cohort)] ?? 10;
            $yy = $this->intakeYearShort($cohort->intake);
            $progCode = $cohort->programme->programme_code;
            $key = $yy . $progCode;

            if (!isset($counters[$key])) {
                $counters[$key] = 1;
            }

            for ($i = 0; $i < $studentCount; $i++) {
                $seq = $counters[$key];
                $studentId = sprintf('%s%s%04d', $yy, $progCode, $seq);
                $counters[$key]++;

                $user = User::create([
                    'name' => 'Student ' . $studentId,
                    'email' => strtolower($studentId) . '@student.tarc.edu.my',
                    'password' => Hash::make(self::DEFAULT_PASSWORD),
                    'role' => 'student',
                ]);

                Student::create([
                    'user_id' => $user->id,
                    'student_id' => $studentId,
                    'cohort_id' => $cohort->id,
                ]);
            }
        }
    }

    private function intakeYearShort(string $intake): string
    {
        preg_match('/(\d{4})/', $intake, $matches);

        return substr($matches[1], -2);
    }

    private function cohortCode(Cohort $cohort): string
    {
        return sprintf(
            '%s%d(S%d)G%d',
            $cohort->programme->programme_code,
            $cohort->current_year,
            $cohort->semester,
            $cohort->tutorial_group
        );
    }
}
