-- Digital Education Monitoring System Database Schema

CREATE DATABASE IF NOT EXISTS education_monitoring;
USE education_monitoring;

-- Admin Table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role VARCHAR(20) DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Schools Table
CREATE TABLE IF NOT EXISTS schools (
    id INT AUTO_INCREMENT PRIMARY KEY,
    school_name VARCHAR(150) NOT NULL,
    school_code VARCHAR(20) NOT NULL UNIQUE,
    district VARCHAR(100) NOT NULL,
    block_or_taluk VARCHAR(100),
    address TEXT,
    contact_number VARCHAR(15),
    head_name VARCHAR(100),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Students Table
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL UNIQUE,
    full_name VARCHAR(150) NOT NULL,
    gender ENUM('Male', 'Female', 'Other'),
    dob DATE,
    class_name VARCHAR(20) NOT NULL,
    section VARCHAR(10),
    school_id INT,
    father_name VARCHAR(100),
    mother_name VARCHAR(100),
    guardian_contact VARCHAR(15),
    address TEXT,
    admission_date DATE,
    photo VARCHAR(255),
    status ENUM('active', 'inactive', 'transferred') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE SET NULL
);

-- Teachers Table
CREATE TABLE IF NOT EXISTS teachers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    teacher_id VARCHAR(20) NOT NULL UNIQUE,
    full_name VARCHAR(150) NOT NULL,
    subject_name VARCHAR(100),
    qualification VARCHAR(100),
    school_id INT,
    phone VARCHAR(15),
    email VARCHAR(100),
    address TEXT,
    joining_date DATE,
    designation VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE SET NULL
);

-- Attendance Table
CREATE TABLE IF NOT EXISTS attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    school_id INT NOT NULL,
    class_name VARCHAR(20) NOT NULL,
    section VARCHAR(10),
    attendance_date DATE NOT NULL,
    status ENUM('Present', 'Absent', 'Late') NOT NULL,
    timestamp_marked TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    marked_by_admin_id INT,
    remarks TEXT,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
    FOREIGN KEY (marked_by_admin_id) REFERENCES admins(id)
);

-- Marks Table
CREATE TABLE IF NOT EXISTS marks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject_name VARCHAR(100) NOT NULL,
    exam_type VARCHAR(50) NOT NULL,
    marks_obtained DECIMAL(5,2) NOT NULL,
    max_marks DECIMAL(5,2) NOT NULL,
    remarks TEXT,
    entered_on DATE,
    entered_by_admin_id INT,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (entered_by_admin_id) REFERENCES admins(id)
);

-- Seed Data (Default Admin: admin/admin123)
-- The password_hash is generated using password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO admins (username, password_hash, full_name, role) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin');
