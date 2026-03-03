-- ============================================================
--  AcadMate Database Setup
--  Run via:  mysql -u root -p < database.sql
--  Or paste entire file into phpMyAdmin SQL tab
-- ============================================================

CREATE DATABASE IF NOT EXISTS acadmate_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE acadmate_db;

-- ──────────────────────────────────────────────────────────────
--  1. users
-- ──────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    full_name    VARCHAR(150)  NOT NULL,
    email        VARCHAR(200)  NOT NULL UNIQUE,
    course       VARCHAR(200)  NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    auth_token   VARCHAR(64)   DEFAULT NULL,
    created_at   DATETIME      DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ──────────────────────────────────────────────────────────────
--  2. semesters
-- ──────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS semesters (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT          NOT NULL,
    name       VARCHAR(100) NOT NULL,
    start_date DATE         NOT NULL,
    end_date   DATE         NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ──────────────────────────────────────────────────────────────
--  3. subjects
-- ──────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS subjects (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    semester_id         INT          NOT NULL,
    user_id             INT          NOT NULL,
    name                VARCHAR(150) NOT NULL,
    code                VARCHAR(50)  DEFAULT '',
    credits             INT          DEFAULT 3,
    type                ENUM('Theory','Practical','Mixed') DEFAULT 'Theory',
    classes_per_week    INT          DEFAULT 3,
    min_attendance_pct  INT          DEFAULT 75,
    total_classes       INT          DEFAULT 72,
    created_at          DATETIME     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)     REFERENCES users(id)     ON DELETE CASCADE
) ENGINE=InnoDB;

-- ──────────────────────────────────────────────────────────────
--  4. class_slots  (weekly recurring timetable entries)
-- ──────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS class_slots (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT         NOT NULL,
    user_id    INT         NOT NULL,
    day        ENUM('Mon','Tue','Wed','Thu','Fri','Sat') NOT NULL,
    mode       ENUM('Theory','Practical') DEFAULT 'Theory',
    start_time TIME        NOT NULL,
    end_time   TIME        NOT NULL,
    room       VARCHAR(100) DEFAULT 'TBD',
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE
) ENGINE=InnoDB;

-- ──────────────────────────────────────────────────────────────
--  5. assignments
-- ──────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS assignments (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT          NOT NULL,
    user_id    INT          NOT NULL,
    title      VARCHAR(200) NOT NULL,
    due_date   DATE         DEFAULT NULL,
    priority   ENUM('High','Medium','Low') DEFAULT 'Medium',
    is_done    TINYINT(1)   DEFAULT 0,
    created_at DATETIME     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE
) ENGINE=InnoDB;

-- ──────────────────────────────────────────────────────────────
--  6. practicals
-- ──────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS practicals (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    subject_id      INT          NOT NULL,
    user_id         INT          NOT NULL,
    title           VARCHAR(200) NOT NULL,
    lab_number      VARCHAR(50)  DEFAULT '',
    description     TEXT         DEFAULT '',
    submission_date DATE         DEFAULT NULL,
    is_done         TINYINT(1)   DEFAULT 0,
    created_at      DATETIME     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE
) ENGINE=InnoDB;

-- ──────────────────────────────────────────────────────────────
--  7. exams
-- ──────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS exams (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT          NOT NULL,
    user_id    INT          NOT NULL,
    type       ENUM('Mid','Final','Quiz') DEFAULT 'Mid',
    exam_date  DATE         DEFAULT NULL,
    exam_time  TIME         DEFAULT NULL,
    location   VARCHAR(200) DEFAULT '',
    syllabus   TEXT         DEFAULT '',
    created_at DATETIME     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE
) ENGINE=InnoDB;

-- ──────────────────────────────────────────────────────────────
--  8. notes
-- ──────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS notes (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT          NOT NULL,
    user_id    INT          NOT NULL,
    title      VARCHAR(200) NOT NULL,
    tag        ENUM('Unit','Revision','Formula') DEFAULT 'Unit',
    content    TEXT         DEFAULT '',
    file_path  VARCHAR(500) DEFAULT NULL,
    created_at DATETIME     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE
) ENGINE=InnoDB;

-- ──────────────────────────────────────────────────────────────
--  9. attendance
-- ──────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS attendance (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    subject_id    INT          NOT NULL,
    user_id       INT          NOT NULL,
    class_slot_id INT          NOT NULL,
    date          DATE         NOT NULL,
    status        ENUM('present','absent') DEFAULT 'absent',
    created_at    DATETIME     DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_attendance (subject_id, class_slot_id, date),
    FOREIGN KEY (subject_id)    REFERENCES subjects(id)    ON DELETE CASCADE,
    FOREIGN KEY (user_id)       REFERENCES users(id)       ON DELETE CASCADE,
    FOREIGN KEY (class_slot_id) REFERENCES class_slots(id) ON DELETE CASCADE
) ENGINE=InnoDB;
