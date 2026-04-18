<?php
require_once "db.php";

// Increase execution time for bulk data
set_time_limit(300);

try {
    // 1. Clear existing student/attendance/marks data before bulk seeding
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $pdo->exec("TRUNCATE TABLE attendance;");
    $pdo->exec("TRUNCATE TABLE marks;");
    $pdo->exec("TRUNCATE TABLE students;");
    $pdo->exec("TRUNCATE TABLE teachers;");
    $pdo->exec("TRUNCATE TABLE schools;");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

    echo "Tables cleared for fresh bulk seeding.<br>";

    // 2. Schools Data
    $schools = [
        ['Govt. Model High School', 'SCH001', 'Dharwad', 'Dharwad', 'College Road, Dharwad', '0836-244556', 'Shri R.B. Patil'],
        ['Govt. Kannada Medium School', 'SCH002', 'Dharwad', 'Hubballi', 'Vidyanagar, Hubballi', '0836-233445', 'Smt. S.M. Kulkarni'],
        ['Dr. Ambedkar Govt. Primary School', 'SCH003', 'Belagavi', 'Belagavi', 'Main St, Belagavi', '0831-222334', 'Shri M.A. Khan']
    ];

    $school_stmt = $pdo->prepare("INSERT INTO schools (school_name, school_code, district, block_or_taluk, address, contact_number, head_name) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach($schools as $s) $school_stmt->execute($s);
    
    $school_ids = $pdo->query("SELECT id FROM schools")->fetchAll(PDO::FETCH_COLUMN);
    echo count($school_ids) . " Schools created.<br>";

    // 3. Name Lists for Random Generation
    $first_names_m = ['Rahul', 'Anil', 'Vijay', 'Suresh', 'Arun', 'Rajesh', 'Manoj', 'Santosh', 'Ganesh', 'Mahesh', 'Prashant', 'Sandeep', 'Amit', 'Vikram', 'Rohan', 'Abhishek', 'Kiran', 'Deepak', 'Sanjay', 'Suraj'];
    $first_names_f = ['Priya', 'Sneha', 'Kavita', 'Deepa', 'Pooja', 'Sunita', 'Laxmi', 'Shilpa', 'Jyoti', 'Savita', 'Divya', 'Anjali', 'Rashmi', 'Vidya', 'Priyanka', 'Megha', 'Shruti', 'Anusha', 'Krutika', 'Preeti'];
    $last_names = ['Kumar', 'Patil', 'Deshpande', 'Hegde', 'Kulkarni', 'More', 'Joshi', 'Hiremath', 'Pawar', 'Shinde', 'Bhat', 'Naik', 'Gowda', 'Bagewadi', 'Kamble', 'Reddy', 'Puranik', 'Angadi', 'Yadav', 'Mathapati'];

    $classes = ['1st Std', '2nd Std', '3rd Std', '4th Std', '5th Std', '6th Std', '7th Std', '8th Std', '9th Std', '10th Std'];
    
    $student_stmt = $pdo->prepare("INSERT INTO students (student_id, full_name, gender, dob, class_name, section, school_id, father_name, mother_name, guardian_contact, address, admission_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE())");

    $pdo->beginTransaction();
    $total_students = 0;
    $stu_counter = 1001;

    foreach($school_ids as $school_index => $s_id){
        foreach($classes as $c_index => $class_name){
            // Base year calculation for DOB (e.g., 2026 - 6 - class_index)
            $base_year = date('Y') - (6 + $c_index);
            
            for($i = 1; $i <= 50; $i++){
                $is_male = (rand(0, 1) == 0);
                $f_name = $is_male ? $first_names_m[array_rand($first_names_m)] : $first_names_f[array_rand($first_names_f)];
                $l_name = $last_names[array_rand($last_names)];
                $full_name = "$f_name $l_name";
                $gender = $is_male ? 'Male' : 'Female';
                
                $dob = $base_year . "-" . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . "-" . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);
                $stu_id_string = "STU" . ($school_index+1) . str_pad($stu_counter++, 4, '0', STR_PAD_LEFT);
                $section = (rand(0, 1) == 0) ? 'A' : 'B';
                
                $father = $first_names_m[array_rand($first_names_m)] . " " . $l_name;
                $mother = $first_names_f[array_rand($first_names_f)] . " " . $l_name;
                $contact = "9" . rand(100000000, 999999999);
                $address = "Village " . rand(1, 50) . ", District " . ($school_index == 2 ? "Belagavi" : "Dharwad");

                $student_stmt->execute([$stu_id_string, $full_name, $gender, $dob, $class_name, $section, $s_id, $father, $mother, $contact, $address]);
                $total_students++;
            }
        }
    }
    $pdo->commit();
    echo "Inserted <strong>$total_students</strong> students successfully (50 per class per school).<br>";

    // 4. Reset Teachers
    $teacher_stmt = $pdo->prepare("INSERT INTO teachers (teacher_id, full_name, subject_name, qualification, school_id, phone, email, address, joining_date, designation) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $teachers_data = [
        ['TCH001', 'Mahesh Hegde', 'Mathematics', 'M.Sc, B.Ed', $school_ids[0], '9443322110', 'mahesh@edu.in', 'Dharwad', '2020-06-01', 'Senior Assistant Teacher'],
        ['TCH002', 'Savita Hiremath', 'Science', 'M.Sc, B.Ed', $school_ids[1], '9332211009', 'savita@edu.in', 'Hubballi', '2021-01-15', 'Principal'],
        ['TCH003', 'Ashok Kumar', 'Social Studies', 'M.A, B.Ed', $school_ids[2], '9221100998', 'ashok@edu.in', 'Belagavi', '2019-08-10', 'Headmaster']
    ];
    foreach($teachers_data as $t) $teacher_stmt->execute($t);
    echo "Teachers reset.<br>";

    echo "<br><strong>Seeding Complete!</strong> System is now loaded with 1500 students.";

} catch(PDOException $e) {
    if($pdo->inTransaction()) $pdo->rollBack();
    die("Bulk Seeding failed: " . $e->getMessage());
}
?>
