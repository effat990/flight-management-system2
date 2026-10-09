CREATE DATABASE IF NOT EXISTS flyair CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE flyair;

CREATE TABLE IF NOT EXISTS passengers (
  passenger_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  phone VARCHAR(30) NOT NULL,
  email VARCHAR(160) NOT NULL,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS airports (
  airport_id INT AUTO_INCREMENT PRIMARY KEY,
  airport_name VARCHAR(160) NOT NULL,
  city VARCHAR(120) NOT NULL,
  country VARCHAR(120) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS flights (
  flight_id INT AUTO_INCREMENT PRIMARY KEY,
  flight_name VARCHAR(120) NOT NULL,
  route VARCHAR(160) NOT NULL,
  departure_date DATE NOT NULL,
  departure_time TIME NOT NULL DEFAULT '09:00:00',
  boarding_time TIME NOT NULL DEFAULT '08:15:00',
  gate_no VARCHAR(20) NOT NULL DEFAULT 'A01',
  total_seats INT NOT NULL DEFAULT 180,
  seats_available INT NOT NULL DEFAULT 180,
  travel_time VARCHAR(40) NOT NULL DEFAULT '1h 00m',
  base_fare DECIMAL(10,2) NOT NULL DEFAULT 5000,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Keep existing installations compatible with the new dynamic flight fields.
-- Keep existing installations compatible with the new dynamic flight fields.
ALTER TABLE flights ADD COLUMN IF NOT EXISTS departure_time TIME NOT NULL DEFAULT '09:00:00';
ALTER TABLE flights ADD COLUMN IF NOT EXISTS boarding_time TIME NOT NULL DEFAULT '08:15:00';
ALTER TABLE flights ADD COLUMN IF NOT EXISTS gate_no VARCHAR(20) NOT NULL DEFAULT 'A01';
UPDATE flights SET departure_time = '09:00:00' WHERE departure_time IS NULL OR departure_time = '00:00:00';
UPDATE flights SET boarding_time = '08:15:00' WHERE boarding_time IS NULL OR boarding_time = '00:00:00';
UPDATE flights SET gate_no = 'A01' WHERE gate_no IS NULL OR TRIM(gate_no) = '';

ALTER TABLE flights ADD COLUMN IF NOT EXISTS travel_time VARCHAR(40) NOT NULL DEFAULT '1h 00m';
ALTER TABLE flights ADD COLUMN IF NOT EXISTS base_fare DECIMAL(10,2) NOT NULL DEFAULT 5000;
UPDATE flights SET travel_time = '1h 00m' WHERE travel_time IS NULL OR TRIM(travel_time) = '';
UPDATE flights SET base_fare = 5000 WHERE base_fare IS NULL;

CREATE TABLE IF NOT EXISTS bookings (
  booking_id INT AUTO_INCREMENT PRIMARY KEY,
  passenger_id INT NOT NULL,
  flight_id INT NOT NULL,
  airport_id INT NULL,
  booking_date DATE NOT NULL,
  seat_number VARCHAR(10) NOT NULL,
  travel_class VARCHAR(30) NOT NULL DEFAULT 'Economy',
  passenger_count INT NOT NULL DEFAULT 1,
  age_group VARCHAR(10) NOT NULL DEFAULT 'Adult',
  gender VARCHAR(10) NOT NULL DEFAULT 'Male',
  status VARCHAR(20) NOT NULL DEFAULT 'Booked',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (passenger_id) REFERENCES passengers(passenger_id),
  FOREIGN KEY (flight_id) REFERENCES flights(flight_id),
  FOREIGN KEY (airport_id) REFERENCES airports(airport_id)
);

CREATE TABLE IF NOT EXISTS payments (
  payment_id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL,
  payment_date DATE NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  method VARCHAR(60) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (booking_id) REFERENCES bookings(booking_id)
);

INSERT INTO airports (airport_name, city, country) VALUES
('Hazrat Shahjalal International Airport','Dhaka','Bangladesh'),
('Osmani International Airport','Sylhet','Bangladesh'),
('Shah Amanat International Airport','Chittagong','Bangladesh'),
('Cox''s Bazar Airport','Cox''s Bazar','Bangladesh'),
('Netaji Subhas Chandra Bose International Airport','Kolkata','India'),
('Chhatrapati Shivaji Maharaj International Airport','Mumbai','India'),
('Hamad International Airport','Doha','Qatar'),
('Dubai International Airport','Dubai','United Arab Emirates'),
('John F. Kennedy International Airport','New York','United States'),
('Heathrow Airport','London','United Kingdom'),
('Changi Airport','Singapore','Singapore'),
('Kuala Lumpur International Airport','Kuala Lumpur','Malaysia');

INSERT INTO flights (flight_name,route,departure_date,departure_time,boarding_time,gate_no,total_seats,seats_available,travel_time,base_fare) VALUES
('US-Bangla Airlines','Dhaka to Sylhet',CURRENT_DATE + INTERVAL 1 DAY,'09:00:00','08:15:00','A01',160,160,'55m',4500),
('US-Bangla Airlines','Dhaka to New York',CURRENT_DATE + INTERVAL 5 DAY,'20:30:00','19:45:00','B12',260,260,'16h 20m',125000),
('US-Bangla Airlines','Dhaka to New Jersey',CURRENT_DATE + INTERVAL 6 DAY,'21:15:00','20:30:00','B14',260,260,'16h 45m',128000),
('US-Bangla Airlines','Dhaka to Jeddah',CURRENT_DATE + INTERVAL 3 DAY,'18:20:00','17:35:00','C07',180,180,'7h 10m',52000),
('Air Astra','Dhaka to Sylhet',CURRENT_DATE + INTERVAL 1 DAY,'10:30:00','09:45:00','A03',70,70,'55m',4200),
('Air Astra','Chittagong to Dhaka',CURRENT_DATE + INTERVAL 3 DAY,'12:20:00','11:35:00','A06',70,70,'50m',4300),
('Biman Bangladesh Airlines','Dhaka to London',CURRENT_DATE + INTERVAL 4 DAY,'22:00:00','21:15:00','D02',270,270,'11h 35m',105000),
('Biman Bangladesh Airlines','Dhaka to Dubai',CURRENT_DATE + INTERVAL 2 DAY,'16:45:00','16:00:00','C12',270,270,'5h 35m',48000),
('Biman Bangladesh Airlines','Dhaka to Jeddah',CURRENT_DATE + INTERVAL 3 DAY,'17:50:00','17:05:00','C15',270,270,'7h 20m',50000),
('Novoair','Dhaka to Rajshahi',CURRENT_DATE + INTERVAL 3 DAY,'12:20:00','11:35:00','A06',70,70,'50m',4000),
('IndiGo','Dhaka to Kolkata',CURRENT_DATE + INTERVAL 2 DAY,'11:40:00','10:55:00','E04',186,186,'1h 05m',9500),
('IndiGo','Dhaka to Mumbai',CURRENT_DATE + INTERVAL 4 DAY,'13:30:00','12:45:00','E06',186,186,'3h 00m',18000),
('Qatar Airways','Dhaka to Doha',CURRENT_DATE + INTERVAL 2 DAY,'15:20:00','14:35:00','C18',254,254,'5h 35m',47000),
('Emirates','Dhaka to Dubai',CURRENT_DATE + INTERVAL 2 DAY,'23:10:00','22:25:00','D10',300,300,'5h 25m',52000),
('Singapore Airlines','Dhaka to Singapore',CURRENT_DATE + INTERVAL 3 DAY,'19:40:00','18:55:00','E12',240,240,'4h 10m',42000),
('Turkish Airlines','Dhaka to Istanbul',CURRENT_DATE + INTERVAL 4 DAY,'20:50:00','20:05:00','D15',280,280,'9h 20m',78000),
('Malaysia Airlines','Dhaka to Kuala Lumpur',CURRENT_DATE + INTERVAL 3 DAY,'01:30:00','00:45:00','E15',240,240,'4h 05m',39000),
('Qatar Airways','Dhaka to New York',CURRENT_DATE + INTERVAL 7 DAY,'23:40:00','22:55:00','B20',254,254,'20h 10m',135000);

-- Additional airport choices requested in the specification.
INSERT INTO airports (airport_name, city, country) VALUES
('Indira Gandhi International Airport','New Delhi','India'),
('Chennai International Airport','Chennai','India'),
('Toronto Pearson International Airport','Toronto','Canada'),
('Vancouver International Airport','Vancouver','Canada'),
('Hamad International Airport','Doha','Qatar'),
('King Abdulaziz International Airport','Jeddah','Saudi Arabia'),
('Newark Liberty International Airport','Newark','United States'),
('LaGuardia Airport','New York','United States'),
('John F. Kennedy International Airport','New York','United States');
