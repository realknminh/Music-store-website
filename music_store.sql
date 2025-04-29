CREATE DATABASE IF NOT EXISTS music_store;
USE music_store;

-- Table for users (authentication)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user', -- Role
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for music genres
CREATE TABLE genres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL
);

-- Insert default admin user (password: admin123, hashed)
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@example.com', '$2y$10$vuXqLN5dMWMHJdB5dM/UA.u25UjehV2A4LsvFMqp3Pcj4AoJ4IJ9q', 'admin')
ON DUPLICATE KEY UPDATE username=username;

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

CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

INSERT INTO genres (name) VALUES ('electronic'), ('rock'), ('pop'), ('hip-hop'), ('r&b')
ON DUPLICATE KEY UPDATE name=name;

INSERT INTO products (title, artist, genre_id, price, image, track) VALUES
('The Less I Know The Better', 'Tame Impala', (SELECT id FROM genres WHERE name='pop'), 1.29, 'images/less.jpg', 'tracks/the_less_i_know_the_better.mp3'),
('New Person, Same Old Mistakes', 'Tame Impala', (SELECT id FROM genres WHERE name='rock'), 1.29, 'images/newperson.jpg', 'tracks/new_person_same_old_mistakes.mp3'),
('Your Face', 'Wisp', (SELECT id FROM genres WHERE name='electronic'), 10.99, 'images/your_face.jpg', 'tracks/your_face.mp3'),
('Snap Out Of It', 'Arctic Monkeys', (SELECT id FROM genres WHERE name='rock'), 13.99, 'images/snap.jpg', 'tracks/snap.mp3'),
('Do I Wanna Know?', 'Arctic Monkeys', (SELECT id FROM genres WHERE name='rock'), 1.29, 'images/do_i_wanna_know.jpg', 'tracks/do_i_wanna_know.mp3'),
('Beauty and the Beast', 'Justin Bieber & Nicky Minaj', (SELECT id FROM genres WHERE name='electronic'), 2.49, 'images/beauty.jpg', 'tracks/beauty_and_the_beast.mp3'),
('505', 'Arctic Monkeys', (SELECT id FROM genres WHERE name='rock'), 1.29, 'images/505.jpg', 'tracks/505.mp3'),
('Born to Die', 'Lana Del Rey', (SELECT id FROM genres WHERE name='pop'), 13.99, 'images/born_to_die.png', 'tracks/born_to_die.mp3'),
('One More Night', 'Maroon 5', (SELECT id FROM genres WHERE name='pop'), 1.29, 'images/onemorenight.jpg', 'tracks/onemorenight.mp3'),
('Summertime Sadness', 'Lana Del Rey', (SELECT id FROM genres WHERE name='pop'), 1.49, 'images/summertime_sadness.jpg', 'tracks/summertime_sadness.mp3'),
('Animals', 'Martin Garrix', (SELECT id FROM genres WHERE name='electronic'), 1.99, 'images/animals.jpg', 'tracks/animals.mp3'),
('Young and Beautiful', 'Lana Del Rey', (SELECT id FROM genres WHERE name='pop'), 1.49, 'images/young_and_beautiful.jpg', 'tracks/young_and_beautiful.mp3'),
('Blinding Lights', 'The Weeknd', (SELECT id FROM genres WHERE name='pop'), 12.50, 'images/blinding.png', 'tracks/blinding_lights.mp3'),
('Goosebumps', 'Travis Scott', (SELECT id FROM genres WHERE name='hip-hop'), 1.99, 'images/goosebumps.jpg', 'tracks/goosebumps.mp3'),
('Highest in the Room', 'Travis Scott', (SELECT id FROM genres WHERE name='hip-hop'), 1.99, 'images/highest_in_the_room.png', 'tracks/highest_in_the_room.mp3'),
('Astroworld', 'Travis Scott', (SELECT id FROM genres WHERE name='hip-hop'), 14.99, 'images/astroworld.png', 'tracks/astroworld.mp3'),
('Telepatía', 'Kali Uchis', (SELECT id FROM genres WHERE name='r&b'), 2.54, 'images/telepatia.jpg', 'tracks/telepatia.mp3'),
('After the Storm', 'Kali Uchis', (SELECT id FROM genres WHERE name='r&b'), 1.49, 'images/after_the_storm.jpg', 'tracks/after_the_storm.mp3'),
('Isolation', 'Kali Uchis', (SELECT id FROM genres WHERE name='r&b'), 12.99, 'images/isolation.png', 'tracks/isolation.mp3'),
('Red Moon in Venus', 'Kali Uchis', (SELECT id FROM genres WHERE name='r&b'), 13.99, 'images/redmoon.jpg', 'tracks/red_moon_in_venus.mp3'),
('Good Days', 'SZA', (SELECT id FROM genres WHERE name='r&b'), 1.30, 'images/good_days.jpg', 'tracks/good_days.mp3'),
('Kill Bill', 'SZA', (SELECT id FROM genres WHERE name='r&b'), 13.40, 'images/kill_bill.jpg', 'tracks/kill_bill.mp3'),
('Nuts', 'Lil Peep', (SELECT id FROM genres WHERE name='hip-hop'), 11.99, 'images/nuts.jpg', 'tracks/nuts.mp3'),
('Yummy', 'Justin Bieber', (SELECT id FROM genres WHERE name='pop'), 1.29, 'images/yummy.png', 'tracks/yummy.mp3'),
('Doubt', 'Twenty One Pilots', (SELECT id FROM genres WHERE name='rock'), 1.49, 'images/doubt.jpg', 'tracks/doubt.mp3'),
('Deadroses', 'Blackbear', (SELECT id FROM genres WHERE name='r&b'), 5.33, 'images/deadroses.png', 'tracks/deadroses.mp3'),
('A Piece of You', 'Nathaniel', (SELECT id FROM genres WHERE name='pop'), 5.45, 'images/pieceofyou.png', 'tracks/a_piece_of_you.mp3'),
('Bad', 'Wave to Earth', (SELECT id FROM genres WHERE name='pop'), 7.09, 'images/bad.jpg', 'tracks/bad.mp3'),
('Seasons', 'Wave to Earth', (SELECT id FROM genres WHERE name='pop'), 3.88, 'images/seasons.jpg', 'tracks/seasons.mp3'),
('Kibeem', 'Kidsai', (SELECT id FROM genres WHERE name='pop'), 21.99, 'images/kibeem.jpg', 'tracks/kibeem.mp3'),
('Sangmai', 'Kidsai', (SELECT id FROM genres WHERE name='pop'), 10.99, 'images/sangmai.jpg', 'tracks/sangmai.mp3'),
('Saydam', 'Kidsai', (SELECT id FROM genres WHERE name='pop'), 8.88, 'images/saydam.jpg', 'tracks/saydam.mp3'),
('Giayphut', 'Kidsai', (SELECT id FROM genres WHERE name='pop'), 9.99, 'images/giayphut.jpg', 'tracks/giayphut.mp3'),
('My Love Mine All Mine', 'Mitski', (SELECT id FROM genres WHERE name='r&b'), 1.99, 'images/mineallmine.jpg', 'tracks/my_love_mine_all_mine.mp3'),
('Fashion Week', 'Blackbear', (SELECT id FROM genres WHERE name='r&b'), 1.99, 'images/fashion.jpg', 'tracks/fashion_week.mp3'),
('Here With Me', 'd4vd', (SELECT id FROM genres WHERE name='r&b'), 1.99, 'images/here_with_me.jpg', 'tracks/here_with_me.mp3'),
('Romantic Homicide', 'd4vd', (SELECT id FROM genres WHERE name='r&b'), 1.99, 'images/romantic.jpg', 'tracks/romantic_homicide.mp3'),
('Die For You', 'The Weeknd', (SELECT id FROM genres WHERE name='r&b'), 1.99, 'images/die_for_you.jpg', 'tracks/die_for_you.mp3'),
('20 Min', 'Lil Uzi Vert', (SELECT id FROM genres WHERE name='hip-hop'), 1.99, 'images/20.jpg', 'tracks/20_min.mp3'),
('Lady Killer', 'G-Eazy', (SELECT id FROM genres WHERE name='hip-hop'), 1.99, 'images/lady.jpg', 'tracks/lady_killer.mp3'),
('Tumblr Girls', 'G-Eazy', (SELECT id FROM genres WHERE name='hip-hop'), 1.99, 'images/tumblr.jpg', 'tracks/tumblr_girls.mp3'),
('Dark Red', 'Steve Lacy', (SELECT id FROM genres WHERE name='r&b'), 1.99, 'images/dark_red.jpg', 'tracks/dark_red.mp3'),
('Hype Boy', 'New Jeans', (SELECT id FROM genres WHERE name='pop'), 1.99, 'images/hypeboy.jpg', 'tracks/hype_boy.mp3'),
('Attention', 'New Jeans', (SELECT id FROM genres WHERE name='pop'), 1.99, 'images/attention.jpg', 'tracks/attention.mp3'),
('Get Up', 'New Jeans', (SELECT id FROM genres WHERE name='pop'), 1.99, 'images/getup.png', 'tracks/omg.mp3'),
('Cookie', 'New Jeans', (SELECT id FROM genres WHERE name='pop'), 1.99, 'images/cookie.jpg', 'tracks/cookie.mp3'),
('White Tee', 'Summer Walker', (SELECT id FROM genres WHERE name='r&b'), 1.99, 'images/white.jpg', 'tracks/white_tee.mp3'),
('What Do I Do', 'SZA', (SELECT id FROM genres WHERE name='r&b'), 1.99, 'images/whatdo.jpg', 'tracks/what_do_i_do.mp3')
ON DUPLICATE KEY UPDATE title=title;
