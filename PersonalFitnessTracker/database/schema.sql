-- Personal Fitness Tracker Database Schema

CREATE DATABASE IF NOT EXISTS fitness_tracker;
USE fitness_tracker;

-- Users table (for all user types: admin, trainer, registered user)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    user_type ENUM('admin', 'trainer', 'user') NOT NULL DEFAULT 'user',
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Fitness tips (public content)
CREATE TABLE IF NOT EXISTS fitness_tips (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    category VARCHAR(50),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Workout routines (public content)
CREATE TABLE IF NOT EXISTS workout_routines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    difficulty_level ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
    duration_minutes INT,
    created_by INT,
    is_public BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Exercise details for workout routines
CREATE TABLE IF NOT EXISTS routine_exercises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    routine_id INT NOT NULL,
    exercise_name VARCHAR(200) NOT NULL,
    sets INT,
    reps INT,
    duration_seconds INT,
    rest_seconds INT,
    FOREIGN KEY (routine_id) REFERENCES workout_routines(id) ON DELETE CASCADE
);

-- Diet plans
CREATE TABLE IF NOT EXISTS diet_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    created_by INT,
    is_public BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Meal items in diet plans
CREATE TABLE IF NOT EXISTS diet_meals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    diet_plan_id INT NOT NULL,
    meal_name VARCHAR(200) NOT NULL,
    calories INT,
    protein_g DECIMAL(5,2),
    carbs_g DECIMAL(5,2),
    fats_g DECIMAL(5,2),
    meal_type ENUM('breakfast', 'lunch', 'dinner', 'snack') DEFAULT 'breakfast',
    FOREIGN KEY (diet_plan_id) REFERENCES diet_plans(id) ON DELETE CASCADE
);

-- User daily activity logs
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    activity_type ENUM('workout', 'meal', 'water') NOT NULL,
    activity_date DATE NOT NULL,
    workout_id INT NULL,
    meal_name VARCHAR(200) NULL,
    calories INT NULL,
    water_intake_ml INT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (workout_id) REFERENCES workout_routines(id) ON DELETE SET NULL
);

-- User feedback on routines/plans
CREATE TABLE IF NOT EXISTS feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    routine_id INT NULL,
    diet_plan_id INT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (routine_id) REFERENCES workout_routines(id) ON DELETE CASCADE,
    FOREIGN KEY (diet_plan_id) REFERENCES diet_plans(id) ON DELETE CASCADE
);

-- Trainer responses to feedback
CREATE TABLE IF NOT EXISTS trainer_responses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    feedback_id INT NOT NULL,
    trainer_id INT NOT NULL,
    response_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (feedback_id) REFERENCES feedback(id) ON DELETE CASCADE,
    FOREIGN KEY (trainer_id) REFERENCES users(id) ON DELETE CASCADE
);

-- User personal plans (saved routines/plans)
CREATE TABLE IF NOT EXISTS user_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    routine_id INT NULL,
    diet_plan_id INT NULL,
    plan_name VARCHAR(200),
    start_date DATE,
    end_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (routine_id) REFERENCES workout_routines(id) ON DELETE CASCADE,
    FOREIGN KEY (diet_plan_id) REFERENCES diet_plans(id) ON DELETE CASCADE
);

-- Insert default admin user
-- NOTE: The password hash below is a placeholder. 
-- Generate a new hash using: php helpers/generate-password.php your_password
-- Then update the password field in the database
INSERT INTO users (username, email, password, user_type, first_name, last_name) 
VALUES ('admin', 'admin@fitness.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Admin', 'User');

-- Insert sample trainer
-- NOTE: The password hash below is a placeholder.
-- Generate a new hash using: php helpers/generate-password.php your_password
-- Then update the password field in the database
INSERT INTO users (username, email, password, user_type, first_name, last_name) 
VALUES ('trainer1', 'trainer@fitness.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'trainer', 'John', 'Trainer');

-- Insert sample fitness tips
INSERT INTO fitness_tips (title, content, category) VALUES
('Stay Hydrated', 'Drink at least 8 glasses of water daily to maintain optimal body function and energy levels.', 'General'),
('Regular Exercise', 'Aim for at least 30 minutes of moderate exercise most days of the week for better health.', 'Exercise'),
('Balanced Diet', 'Include a variety of fruits, vegetables, lean proteins, and whole grains in your daily meals.', 'Nutrition'),
('Get Enough Sleep', 'Aim for 7-9 hours of quality sleep each night to support recovery and overall health.', 'General'),
('Warm Up Before Exercise', 'Always warm up for 5-10 minutes before intense workouts to prevent injuries.', 'Exercise');

-- Insert sample workout routines
INSERT INTO workout_routines (title, description, difficulty_level, duration_minutes, created_by, is_public) VALUES
('Morning Cardio', 'A light 20-minute cardio routine perfect for starting your day', 'beginner', 20, 2, TRUE),
('Full Body Strength', 'Complete strength training workout targeting all major muscle groups', 'intermediate', 45, 2, TRUE),
('HIIT Workout', 'High-intensity interval training for maximum calorie burn', 'advanced', 30, 2, TRUE);

-- Insert sample exercises for routines
INSERT INTO routine_exercises (routine_id, exercise_name, sets, reps, duration_seconds, rest_seconds) VALUES
(1, 'Jumping Jacks', 3, 20, NULL, 30),
(1, 'High Knees', 3, 30, NULL, 30),
(1, 'Burpees', 3, 10, NULL, 45),
(2, 'Push-ups', 3, 15, NULL, 60),
(2, 'Squats', 3, 20, NULL, 60),
(2, 'Plank', 3, NULL, 60, 60),
(3, 'Mountain Climbers', 4, NULL, 30, 15),
(3, 'Burpees', 4, 10, NULL, 20),
(3, 'Jump Squats', 4, 15, NULL, 20);

-- Insert sample diet plans
INSERT INTO diet_plans (title, description, created_by, is_public) VALUES
('Balanced Meal Plan', 'A well-rounded meal plan for maintaining healthy weight', 2, TRUE),
('High Protein Plan', 'Meal plan focused on protein intake for muscle building', 2, TRUE),
('Weight Loss Plan', 'Calorie-controlled meal plan for healthy weight loss', 2, TRUE);

-- Insert sample meals
INSERT INTO diet_meals (diet_plan_id, meal_name, calories, protein_g, carbs_g, fats_g, meal_type) VALUES
(1, 'Oatmeal with Berries', 300, 10, 50, 5, 'breakfast'),
(1, 'Grilled Chicken Salad', 400, 35, 20, 15, 'lunch'),
(1, 'Salmon with Vegetables', 500, 40, 30, 20, 'dinner'),
(2, 'Protein Smoothie', 350, 30, 25, 10, 'breakfast'),
(2, 'Chicken Breast with Rice', 550, 45, 40, 15, 'lunch'),
(2, 'Lean Beef with Sweet Potato', 600, 50, 45, 20, 'dinner');

