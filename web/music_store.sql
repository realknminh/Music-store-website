CREATE DATABASE IF NOT EXISTS music_store;
USE music_store;

-- Table for users (authentication)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- hashed password
    role ENUM('admin', 'user') DEFAULT 'user', -- Role
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123, hashed)
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@musicstore.com', '$2y$10$eImiTXuW', 'admin')
ON DUPLICATE KEY UPDATE username=username;

-- Table for music genres
CREATE TABLE genres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL
);

-- Table for products (music tracks or albums)
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    artist VARCHAR(255) NOT NULL,
    genre_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) NOT NULL,
    track VARCHAR(255) NOT NULL, 
    FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE CASCADE
);

INSERT INTO genres (name) VALUES ('jazz'), ('rock'), ('pop'), ('classical'), ('hip-hop'), ('r&b')
ON DUPLICATE KEY UPDATE name=name;

INSERT INTO products (title, artist, genre_id, price, image, track) VALUES
('Kind of Blue', 'Miles Davis', (SELECT id FROM genres WHERE name='jazz'), 12.99, 'images/kind_of_blue.jpg', 'tracks/kind_of_blue.mp3'),
('Back in Black', 'AC/DC', (SELECT id FROM genres WHERE name='rock'), 15.99, 'images/back_in_black.jpg', 'tracks/back_in_black.mp3'),
('Highway to Hell', 'AC/DC', (SELECT id FROM genres WHERE name='rock'), 15.99, 'images/highway_to_hell.jpg', 'tracks/highway_to_hell.mp3'),
('The Dark Side of the Moon', 'Pink Floyd', (SELECT id FROM genres WHERE name='rock'), 14.99, 'images/dark_side_of_the_moon.jpg', 'tracks/dark_side_of_the_moon.mp3'),
('Wish You Were Here', 'Pink Floyd', (SELECT id FROM genres WHERE name='rock'), 14.99, 'images/wish_you_were_here.jpg', 'tracks/wish_you_were_here.mp3'),
('The Wall', 'Pink Floyd', (SELECT id FROM genres WHERE name='rock'), 14.99, 'images/the_wall.jpg', 'tracks/the_wall.mp3'),
('Thriller', 'Michael Jackson', (SELECT id FROM genres WHERE name='pop'), 14.99, 'images/thriller.jpg', 'tracks/thriller.mp3'),
('Beethoven Symphony No. 9', 'Ludwig van Beethoven', (SELECT id FROM genres WHERE name='classical'), 9.99, 'images/symphony9.jpg', 'tracks/symphony9.mp3'),
('Snap Out Of It', 'Arctic Monkeys', (SELECT id FROM genres WHERE name='rock'), 13.99, 'images/snap.jpg', 'tracks/snap.mp3'),
('Do I Wanna Know?', 'Arctic Monkeys', (SELECT id FROM genres WHERE name='rock'), 1.29, 'images/do_i_wanna_know.jpg', 'tracks/do_i_wanna_know.mp3'),
('505', 'Arctic Monkeys', (SELECT id FROM genres WHERE name='rock'), 1.29, 'images/505.jpg', 'tracks/505.mp3'),
('Born to Die', 'Lana Del Rey', (SELECT id FROM genres WHERE name='pop'), 13.99, 'images/born_to_die.png', 'tracks/born_to_die.mp3'),
('Summertime Sadness', 'Lana Del Rey', (SELECT id FROM genres WHERE name='pop'), 1.49, 'images/summertime_sadness.jpg', 'tracks/summertime_sadness.mp3'),
('Young and Beautiful', 'Lana Del Rey', (SELECT id FROM genres WHERE name='pop'), 1.49, 'images/young_and_beautiful.jpg', 'tracks/young_and_beautiful.mp3'),
('Blinding Lights', 'The Weeknd', (SELECT id FROM genres WHERE name='pop'), 12.50, 'images/blinding.png', 'tracks/blinding_lights.mp3'),
('Goosebumps', 'Travis Scott', (SELECT id FROM genres WHERE name='hip-hop'), 1.99, 'images/goosebumps.jpg', 'tracks/goosebumps.mp3'),
('Highest in the Room', 'Travis Scott', (SELECT id FROM genres WHERE name='hip-hop'), 1.99, 'images/highest_in_the_room.png', 'tracks/highest_in_the_room.mp3'),
('Astroworld', 'Travis Scott', (SELECT id FROM genres WHERE name='hip-hop'), 14.99, 'images/astroworld.png', 'tracks/astroworld.mp3'),
('Telepatía', 'Kali Uchis', (SELECT id FROM genres WHERE name='r&b'), 2.54, 'images/telepatia.jpg', 'tracks/telepatia.mp3'),
('After the Storm', 'Kali Uchis', (SELECT id FROM genres WHERE name='r&b'), 1.49, 'images/after_the_storm.jpg', 'tracks/after_the_storm.mp3'),
('Isolation', 'Kali Uchis', (SELECT id FROM genres WHERE name='r&b'), 12.99, 'images/isolation.jpg', 'tracks/isolation.mp3'),
('Red Moon in Venus', 'Kali Uchis', (SELECT id FROM genres WHERE name='r&b'), 13.99, 'images/red_moon_in_venus.jpg', 'tracks/red_moon_in_venus.mp3'),
('Good Days', 'SZA', (SELECT id FROM genres WHERE name='r&b'), 1.3, 'images/good_days.jpg', 'tracks/good_days.mp3'),
('Kill Bill', 'SZA', (SELECT id FROM genres WHERE name='r&b'), 13.4, 'images/kill_bill.jpg', 'tracks/kill_bill.mp3'),
('SOS', 'SZA', (SELECT id FROM genres WHERE name='r&b'), 14.99, 'images/sos.jpg', 'tracks/sos.mp3'),
('Ctrl', 'SZA', (SELECT id FROM genres WHERE name='r&b'), 14.99, 'images/ctrl.png', 'tracks/ctrl.mp3'),
('Nuts', 'Lil Peep', (SELECT id FROM genres WHERE name='hip-hop'), 11.99, 'images/nuts.jpg', 'tracks/nuts.mp3')
ON DUPLICATE KEY UPDATE title=title;
