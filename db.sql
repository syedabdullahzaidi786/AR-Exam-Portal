-- Create database
DROP DATABASE IF EXISTS dif_quiz_system;
CREATE DATABASE dif_quiz_system;
USE dif_quiz_system;

-- Create users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(50) NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create quizzes table
CREATE TABLE quizzes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    course_code VARCHAR(50) NOT NULL,
    duration INT NOT NULL,
    total_questions INT NOT NULL,
    exam_key VARCHAR(100) NOT NULL,
    status ENUM('open', 'closed') DEFAULT 'open'
);

-- Create questions table
CREATE TABLE quiz_questions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    quiz_id INT,
    question_text TEXT NOT NULL,
    option_a TEXT NOT NULL,
    option_b TEXT NOT NULL,
    option_c TEXT NOT NULL,
    option_d TEXT NOT NULL,
    correct_answer CHAR(1) NOT NULL,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
);

-- Create results table
CREATE TABLE quiz_results (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    quiz_id INT,
    score INT,
    total_questions INT,
    submission_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
);

-- Insert admin user
INSERT INTO users (name, email, password, is_admin) VALUES 
('Admin', 'admin', 'admin123', TRUE);

-- Insert sample quizzes
INSERT INTO quizzes (title, course_code, duration, total_questions, exam_key, status) VALUES
('Fundamentals OF Html', 'Html01', 10, 10, 'Html01', 'closed'),
('General Knowledge', 'GK01', 10, 10, 'GK01', 'open');

-- Insert sample questions for Fundamentals OF Html quiz
INSERT INTO quiz_questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_answer) VALUES
(1, 'What is TypeScript?', 'A programming language', 'A JavaScript framework', 'A superset of JavaScript', 'A database', 'c'),
(1, 'Which symbol is used for type annotation in TypeScript?', ':', ';', '->', '=>', 'a'),
(1, 'What is the file extension for TypeScript files?', '.ts', '.tsx', '.typescript', '.type', 'a'),
(1, 'Which of these is a TypeScript-specific feature?', 'Loops', 'Arrays', 'Type Inference', 'Console.log', 'c'),
(1, 'What is an interface in TypeScript?', 'A class', 'A type definition', 'A function', 'A variable', 'b');

-- Insert sample questions for General Knowledge quiz
INSERT INTO quiz_questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_answer) VALUES
(2, 'What is Html?', 'Markup Language', 'Programming Language', 'Database Language', 'Operating System', 'a'),
(2, 'Which company developed Chatgpt?', 'OpenAI', 'Meta', 'DeepSeek', 'Kimi', 'a'),
(2, 'Who developed Github Copilot?', 'Google', 'Meta', 'OpenAI & Microsoft', 'DeepSeek', 'c'),
(2, 'Which LLM is developed by Meta?', 'DeepSeek', 'OpenAI', 'Microsoft', 'LLaMA', 'd'),
(2, 'When was Blackbox AI first launched?', '2021', '2022', '2023', '2024', 'a');

-- Insert sample users
INSERT INTO users (name, email, password) VALUES
('Sir Sohaib', 'sohaib@dif', '123'),
('Sir Abdullah', 'abdullah@ardev', '123'),
('Miss Huma', 'camp2@dif', '123');

-- Insert sample quiz results
INSERT INTO quiz_results (user_id, quiz_id, score, total_questions) VALUES
(2, 1, 18, 20),
(2, 2, 15, 20),
(3, 1, 16, 20),
(4, 3, 12, 15);