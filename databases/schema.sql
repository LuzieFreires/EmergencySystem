
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE residents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    address TEXT NOT NULL,
    contact_number VARCHAR(20) NOT NULL,
    emergency_contact VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE responders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    specialization VARCHAR(100) NOT NULL,
    contact_number VARCHAR(20) NOT NULL,
    status ENUM('available', 'busy', 'offline') DEFAULT 'available',
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS emergencies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    reported_by INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    location VARCHAR(255) NOT NULL,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    severity VARCHAR(20) NOT NULL,
    description TEXT,
    status VARCHAR(20) DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (reported_by) REFERENCES users(id)
);

ALTER TABLE emergencies
ADD COLUMN latitude DECIMAL(10, 8) AFTER location,
ADD COLUMN longitude DECIMAL(11, 8) AFTER latitude,
ADD COLUMN severity ENUM('low', 'medium', 'high', 'critical') NOT NULL AFTER description;

CREATE TABLE alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    severity ENUM('low', 'medium', 'high', 'critical') NOT NULL,
    status ENUM('active', 'resolved', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE medical_conditions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    resident_id INT,
    condition_name VARCHAR(100) NOT NULL,
    diagnosis_date DATE,
    severity ENUM('mild', 'moderate', 'severe') NOT NULL,
    status ENUM('active', 'managed', 'resolved') DEFAULT 'active',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (resident_id) REFERENCES residents(id)
);

CREATE TABLE medications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    resident_id INT,
    medication_name VARCHAR(100) NOT NULL,
    dosage VARCHAR(50) NOT NULL,
    frequency VARCHAR(50) NOT NULL,
    start_date DATE,
    end_date DATE,
    prescribing_doctor VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (resident_id) REFERENCES residents(id)
);

CREATE TABLE allergies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    resident_id INT,
    allergen VARCHAR(100) NOT NULL,
    reaction_type VARCHAR(100) NOT NULL,
    severity ENUM('mild', 'moderate', 'severe') NOT NULL,
    diagnosis_date DATE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (resident_id) REFERENCES residents(id)
);

CREATE TABLE medical_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    resident_id INT,
    event_type ENUM('surgery', 'hospitalization', 'major_illness', 'injury', 'other') NOT NULL,
    event_date DATE NOT NULL,
    description TEXT NOT NULL,
    facility VARCHAR(100),
    treating_doctor VARCHAR(100),
    outcome TEXT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (resident_id) REFERENCES residents(id)
);

CREATE TABLE emergency_contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    resident_id INT,
    name VARCHAR(100) NOT NULL,
    relationship VARCHAR(50) NOT NULL,
    primary_phone VARCHAR(20) NOT NULL,
    secondary_phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    is_primary BOOLEAN DEFAULT FALSE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (resident_id) REFERENCES residents(id)
);

CREATE TABLE IF NOT EXISTS emergency_responses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    emergency_id INT NOT NULL,
    responder_id INT NOT NULL,
    status ENUM('responding', 'completed') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (emergency_id) REFERENCES emergencies(id),
    FOREIGN KEY (responder_id) REFERENCES responders(id)
);

-- Add responder general information table
CREATE TABLE responder_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    responder_id INT NOT NULL,
    date_of_birth DATE NOT NULL,
    blood_type ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL,
    height DECIMAL(5,2),  -- in cm
    weight DECIMAL(5,2),  -- in kg
    certification_number VARCHAR(50) NOT NULL,
    certification_expiry DATE NOT NULL,
    years_of_experience INT NOT NULL,
    department VARCHAR(100) NOT NULL,
    shift_preference ENUM('morning', 'afternoon', 'night', 'flexible') DEFAULT 'flexible',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (responder_id) REFERENCES responders(id)
);

-- Add responder medical conditions table
CREATE TABLE responder_medical_conditions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    responder_id INT NOT NULL,
    condition_name VARCHAR(100) NOT NULL,
    diagnosis_date DATE,
    severity ENUM('mild', 'moderate', 'severe') NOT NULL,
    status ENUM('active', 'managed', 'resolved') DEFAULT 'active',
    treatment_plan TEXT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (responder_id) REFERENCES responders(id)
);

-- Add responder medications table
CREATE TABLE responder_medications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    responder_id INT NOT NULL,
    medication_name VARCHAR(100) NOT NULL,
    dosage VARCHAR(50) NOT NULL,
    frequency VARCHAR(50) NOT NULL,
    start_date DATE,
    end_date DATE,
    prescribing_doctor VARCHAR(100),
    purpose TEXT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (responder_id) REFERENCES responders(id)
);

-- Add responder allergies table
CREATE TABLE responder_allergies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    responder_id INT NOT NULL,
    allergen VARCHAR(100) NOT NULL,
    reaction_type VARCHAR(100) NOT NULL,
    severity ENUM('mild', 'moderate', 'severe') NOT NULL,
    diagnosis_date DATE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (responder_id) REFERENCES responders(id)
);

-- Add responder vaccination history
CREATE TABLE responder_vaccinations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    responder_id INT NOT NULL,
    vaccine_name VARCHAR(100) NOT NULL,
    dose_number INT NOT NULL,
    date_administered DATE NOT NULL,
    administered_by VARCHAR(100),
    facility VARCHAR(100),
    next_due_date DATE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (responder_id) REFERENCES responders(id)
);

-- Add responder fitness assessments
CREATE TABLE responder_fitness_assessments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    responder_id INT NOT NULL,
    assessment_date DATE NOT NULL,
    cardiovascular_endurance ENUM('poor', 'fair', 'good', 'excellent') NOT NULL,
    strength_assessment ENUM('poor', 'fair', 'good', 'excellent') NOT NULL,
    flexibility_assessment ENUM('poor', 'fair', 'good', 'excellent') NOT NULL,
    bmi DECIMAL(4,2),
    blood_pressure VARCHAR(20),
    resting_heart_rate INT,
    notes TEXT,
    assessed_by VARCHAR(100) NOT NULL,
    next_assessment_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (responder_id) REFERENCES responders(id)
);

-- Add responder certifications and training
CREATE TABLE responder_certifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    responder_id INT NOT NULL,
    certification_name VARCHAR(100) NOT NULL,
    issuing_organization VARCHAR(100) NOT NULL,
    certification_number VARCHAR(50),
    issue_date DATE NOT NULL,
    expiry_date DATE,
    status ENUM('active', 'expired', 'revoked', 'pending_renewal') NOT NULL,
    document_url VARCHAR(255),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (responder_id) REFERENCES responders(id)
);