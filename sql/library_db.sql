-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 06, 2025 at 05:47 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `library_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `book_id` varchar(40) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `month_published` varchar(20) DEFAULT NULL,
  `year_published` int(11) NOT NULL,
  `category` varchar(50) NOT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `date_added` date NOT NULL,
  `count_in_library` int(11) NOT NULL DEFAULT 0,
  `available_copies` int(11) NOT NULL DEFAULT 0,
  `status` enum('available','borrowed','archived','') NOT NULL DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`book_id`, `title`, `author`, `month_published`, `year_published`, `category`, `cover_image`, `date_added`, `count_in_library`, `available_copies`, `status`) VALUES
('ACOMAY062015-FAN00005', 'A Court of Thorns and Roses', 'Sarah J. Maas', 'May', 2015, 'Fantasy', 'uploads/covers/a_court_of_thorns_and_roses.jpg', '2025-07-06', 5, 5, 'available'),
('ADDEC062009-CHILD00019', 'Adventure Time', 'yayaya', 'December', 2009, 'Children', 'uploads/covers/adventure_time.jpg', '2025-07-06', 19, 16, 'available'),
('AGAAUG061996-FAN00005', 'A Game of Thrones', 'George R.R. Martin', 'August', 1996, 'Fantasy', 'uploads/covers/a_game_of_thrones.jpg', '2025-07-06', 5, 5, 'available'),
('AMAAUG062012-FIC00007', 'A Man Called Ove', 'Fredrik Backman', 'August', 2012, 'Fiction', 'uploads/covers/a_man_called_ove.jpg', '2025-07-06', 7, 7, 'available'),
('ANNNOV061939-MYS00004', 'And Then There Were None', 'Agatha Christie', 'November', 1939, 'Mystery', 'uploads/covers/and_then_there_were_none.jpg', '2025-07-06', 4, 4, 'available'),
('ATOCT062018-SEL00006', 'Atomic Habits', 'James Clear', 'October', 2018, 'Self-Help', 'uploads/covers/atomic_habits.jpg', '2025-07-06', 6, 6, 'available'),
('BECNOV062018-BIO00005', 'Becoming', 'Michelle Obama', 'November', 2018, 'Biography', 'uploads/covers/becoming.jpg', '2025-07-06', 5, 5, 'available'),
('BELSSEP061987-CLA00006', 'Beloved', 'Toni Morrison', 'September', 1987, 'Classics', 'uploads/covers/beloved.jpg', '2025-07-06', 6, 6, 'available'),
('BILJUL062014-THR00008', 'Big Little Lies', 'Liane Moriarty', 'July', 2014, 'Thriller', 'uploads/covers/big_little_lies.jpg', '2025-07-06', 8, 8, 'available'),
('BORNOV062016-MEM00007', 'Born a Crime', 'Trevor Noah', 'November', 2016, 'Memoir', 'uploads/covers/born_a_crime.jpg', '2025-07-06', 7, 7, 'available'),
('CAFEB062872-HOR00002', 'Carmilla', 'J. Sheridan Le Fanu', 'February', 1872, 'Horror', 'uploads/covers/carmilla.jpg', '2025-07-06', 2, 2, 'available'),
('CIRAPR062018-MYT00006', 'Circe', 'Madeline Miller', 'April', 2018, 'Mythology', 'uploads/covers/circe.jpg', '2025-07-06', 6, 6, 'available'),
('CRIDEC061866-CLA00005', 'Crime and Punishment', 'Fyodor Dostoevsky', 'December', 1866, 'Classics', 'uploads/covers/crime_and_punishment.jpg', '2025-07-06', 5, 5, 'available'),
('DUNAUG061965-SCI00010', 'Dune', 'Frank Herbert', 'August', 1965, 'Science Fiction', 'uploads/covers/dune.jpg', '2025-07-06', 10, 10, 'available'),
('EDUFEB062018-MEM00008', 'Educated', 'Tara Westover', 'February', 2018, 'Memoir', 'uploads/covers/educated.jpg', '2025-07-06', 8, 8, 'available'),
('ELIJUL062012-YOU00006', 'Eleanor & Park', 'Rainbow Rowell', 'July', 2012, 'Young Adult', 'uploads/covers/eleanor_and_park.jpg', '2025-07-06', 6, 6, 'available'),
('ENJAN061985-SCI00003', 'Ender’s Game', 'Orson Scott Card', 'January', 1985, 'Science Fiction', 'uploads/covers/enders_game.jpg', '2025-07-06', 3, 3, 'available'),
('EVESEP062015-YOU00002', 'Everything, Everything', 'Nicola Yoon', 'September', 2015, 'Young Adult', 'uploads/covers/everything_everything.jpg', '2025-07-06', 2, 2, 'available'),
('FRAJAN061818-HOR00006', 'Frankenstein', 'Mary Shelley', 'January', 1818, 'Horror', 'uploads/covers/frankenstein.jpg', '2025-07-06', 6, 6, 'available'),
('GEJUN061949-DYS00006', '1984', 'George Orwell', 'June', 1949, 'Dystopian', 'uploads/covers/1984.jpg', '2025-07-06', 6, 6, 'available'),
('GONMAY062012-THR00003', 'Gone Girl', 'Gillian Flynn', 'May', 2012, 'Thriller', 'uploads/covers/gone_girl.jpg', '2025-07-06', 3, 3, 'available'),
('GOOMAY061990-FAN00003', 'Good Omens', 'Neil Gaiman & Terry Pratchett', 'May', 1990, 'Fantasy', 'uploads/covers/good_omens.jpg', '2025-07-06', 3, 3, 'available'),
('HAJJUN061997-FAN00009', 'Harry Potter and the Philosopher’s Stone', 'J.K. Rowling', 'June', 1997, 'Fantasy', 'uploads/covers/harry_potter_and_the_philosophers_stone.jpg', '2025-07-06', 9, 9, 'available'),
('IKWNOV061969-MEM00004', 'I Know Why the Caged Bird Sings', 'Maya Angelou', 'November', 1969, 'Memoir', 'uploads/covers/i_know_why_the_caged_bird_sings.jpg', '2025-07-06', 4, 4, 'available'),
('KLAMAR062021-SCI00004', 'Klara and the Sun', 'Kazuo Ishiguro', 'March', 2021, 'Science Fiction', 'uploads/covers/klara_and_the_sun.jpg', '2025-07-06', 4, 4, 'available'),
('LITSEP061868-CLA00003', 'Little Women', 'Louisa May Alcott', 'September', 1868, 'Classics', 'uploads/covers/little_women.jpg', '2025-07-06', 3, 3, 'available'),
('MIFJUL062006-FAN00008', 'Mistborn: The Final Empire', 'Brandon Sanderson', 'July', 2006, 'Fantasy', 'uploads/covers/mistborn_the_final_empire.jpg', '2025-07-06', 8, 8, 'available'),
('NEUJUL061984-SCI00008', 'Neuromancer', 'William Gibson', 'July', 1984, 'Science Fiction', 'uploads/covers/neuromancer.jpg', '2025-07-06', 8, 8, 'available'),
('NONAUG062018-FIC00004', 'Normal People', 'Sally Rooney', 'August', 2018, 'Fiction', 'uploads/covers/normal_people.jpg', '2025-07-06', 4, 4, 'available'),
('OUTNOV062008-BUS00002', 'Outliers', 'Malcolm Gladwell', 'November', 2008, 'Business', 'uploads/covers/outliers.jpg', '2025-07-06', 2, 2, 'available'),
('PRHMAY062021-SCI00006', 'Project Hail Mary', 'Andy Weir', 'May', 2021, 'Science Fiction', 'uploads/covers/project_hail_mary.jpg', '2025-07-06', 6, 6, 'available'),
('PRIJAN061813-ROM00008', 'Pride and Prejudice', 'Jane Austen', 'January', 1813, 'Romance', 'uploads/covers/pride_and_prejudice.jpg', '2025-07-06', 8, 8, 'available'),
('THAJAN061910-PHI00001', 'The Art of War', 'Sun Tzu', 'January', 1910, 'Philosophy', 'uploads/covers/the_art_of_war.jpg', '2025-07-06', 1, 1, 'available'),
('THAJUN061876-CLA00004', 'The Adventures of Tom Sawyer', 'Mark Twain', 'June', 1876, 'Classics', 'uploads/covers/the_adventures_of_tom_sawyer.jpg', '2025-07-06', 4, 4, 'available'),
('THAMAY061988-PHI00009', 'The Alchemist', 'Paulo Coelho', 'May', 1988, 'Philosophy', 'uploads/covers/the_alchemist.jpg', '2025-07-06', 9, 9, 'available'),
('THBSEP062005-HIS00004', 'The Book Thief', 'Markus Zusak', 'September', 2005, 'Historical Fiction', 'uploads/covers/the_book_thief.jpg', '2025-07-06', 4, 4, 'available'),
('THBSEP062017-YOU00003', 'They Both Die at the End', 'Adam Silvera', 'September', 2017, 'Young Adult', 'uploads/covers/they_both_die_at_the_end.jpg', '2025-07-06', 3, 3, 'available'),
('THCAPR061951-CLA00002', 'The Catcher in the Rye', 'J.D. Salinger', 'April', 1951, 'Classics', 'uploads/covers/the_catcher_in_the_rye.jpg', '2025-07-06', 2, 2, 'available'),
('THCAPR062016-THR00004', 'The Couple Next Door', 'Shari Lapena', 'April', 2016, 'Thriller', 'uploads/covers/the_couple_next_door.jpg', '2025-07-06', 4, 4, 'available'),
('THDJAN062003-MYS00003', 'The Da Vinci Code', 'Dan Brown', 'January', 2003, 'Mystery', 'uploads/covers/the_da_vinci_code.jpg', '2025-07-06', 3, 3, 'available'),
('THFAPR062021-HOR00007', 'The Final Girl Support Group', 'Grady Hendrix', 'April', 2021, 'Horror', 'uploads/covers/the_final_girl_support_group.jpg', '2025-07-06', 7, 7, 'available'),
('THFJAN062012-YOU00005', 'The Fault in Our Stars', 'John Green', 'January', 2012, 'Young Adult', 'uploads/covers/the_fault_in_our_stars.jpg', '2025-07-06', 5, 5, 'available'),
('THGAPR061925-CLA00009', 'The Great Gatsby', 'F. Scott Fitzgerald', 'April', 1925, 'Classics', 'uploads/covers/the_great_gatsby.jpg', '2025-07-06', 9, 9, 'available'),
('THGAUG062005-MYS00003', 'The Girl with the Dragon Tattoo', 'Stieg Larsson', 'August', 2005, 'Mystery', 'uploads/covers/the_girl_with_the_dragon_tattoo.jpg', '2025-07-06', 3, 3, 'available'),
('THHSEP061937-FAN00002', 'The Hobbit', 'J.R.R. Tolkien', 'September', 1937, 'Fantasy', 'uploads/covers/the_hobbit.jpg', '2025-07-06', 2, 2, 'available'),
('THHSEP062008-DYS00001', 'The Hunger Games', 'Suzanne Collins', 'September', 2008, 'Dystopian', 'uploads/covers/the_hunger_games.jpg', '2025-07-06', 1, 1, 'available'),
('THKMAY062003-HIS00007', 'The Kite Runner', 'Khaled Hosseini', 'May', 2003, 'Historical Fiction', 'uploads/covers/the_kite_runner.jpg', '2025-07-06', 7, 7, 'available'),
('THLMAR061969-SCI00006', 'The Left Hand of Darkness', 'Ursula K. Le Guin', 'March', 1969, 'Science Fiction', 'uploads/covers/the_left_hand_of_darkness.jpg', '2025-07-06', 6, 6, 'available'),
('THMAUG062020-FIC00002', 'The Midnight Library', 'Matt Haig', 'August', 2020, 'Fiction', 'uploads/covers/the_midnight_library.jpg', '2025-07-06', 2, 2, 'available'),
('THMFEB062011-SCI00005', 'The Martian', 'Andy Weir', 'February', 2011, 'Science Fiction', 'uploads/covers/the_martian.jpg', '2025-07-06', 5, 5, 'available'),
('THNMAR062007-FAN00007', 'The Name of the Wind', 'Patrick Rothfuss', 'March', 2007, 'Fantasy', 'uploads/covers/the_name_of_the_wind.jpg', '2025-07-06', 7, 7, 'available'),
('THNSEP062011-FAN00007', 'The Night Circus', 'Erin Morgenstern', 'September', 2011, 'Fantasy', 'uploads/covers/the_night_circus.jpg', '2025-07-06', 7, 7, 'available'),
('THPAUG061997-SEL00009', 'The Power of Now', 'Eckhart Tolle', 'August', 1997, 'Self-Help', 'uploads/covers/the_power_of_now.jpg', '2025-07-06', 9, 9, 'available'),
('THSFEB062019-THR00007', 'The Silent Patient', 'Alex Michaelides', 'February', 2019, 'Thriller', 'uploads/covers/the_silent_patient.jpg', '2025-07-06', 7, 7, 'available'),
('THSJUN062017-ROM00009', 'The Seven Husbands of Evelyn Hugo', 'Taylor Jenkins Reid', 'June', 2017, 'Romance', 'uploads/covers/the_seven_husbands_of_evelyn_hugo.jpg', '2025-07-06', 9, 9, 'available'),
('THSNOV062016-YOU00006', 'The Sun Is Also a Star', 'Nicola Yoon', 'November', 2016, 'Young Adult', 'uploads/covers/the_sun_is_also_a_star.jpg', '2025-07-06', 6, 6, 'available'),
('THTAPR061925-CLA00005', 'The Trial', 'Franz Kafka', 'April', 1925, 'Classics', 'uploads/covers/the_trial.jpg', '2025-07-06', 5, 5, 'available'),
('THTSEP062020-MYS00007', 'The Thursday Murder Club', 'Richard Osman', 'September', 2020, 'Mystery', 'uploads/covers/the_thursday_murder_club.jpg', '2025-07-06', 7, 7, 'available'),
('TOKJUL061960-CLA00003', 'To Kill a Mockingbird', 'Harper Lee', 'July', 1960, 'Classics', 'uploads/covers/to_kill_a_mockingbird.jpg', '2025-07-06', 3, 3, 'available'),
('WHBJAN062016-MEM00002', 'When Breath Becomes Air', 'Paul Kalanithi', 'January', 2016, 'Memoir', 'uploads/covers/when_breath_becomes_air.jpg', '2025-07-06', 2, 2, 'available'),
('WUHNOV061847-CLA00001', 'Wuthering Heights', 'Emily Brontë', 'November', 1847, 'Classics', 'uploads/covers/wuthering_heights.jpg', '2025-07-06', 1, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `borrowings`
--

CREATE TABLE `borrowings` (
  `borrow_id` int(11) NOT NULL,
  `book_id` varchar(40) NOT NULL,
  `user_id` int(11) NOT NULL,
  `borrow_date` date NOT NULL,
  `due_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `fine_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('borrowed','returned','overdue','') NOT NULL DEFAULT 'borrowed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrowings`
--

INSERT INTO `borrowings` (`borrow_id`, `book_id`, `user_id`, `borrow_date`, `due_date`, `return_date`, `fine_amount`, `status`) VALUES
(3, 'ADDEC062009-CHILD00019', 1, '2025-06-23', '2025-06-30', NULL, 0.00, 'borrowed'),
(4, 'ADDEC062009-CHILD00019', 3, '2025-07-06', '2025-07-20', '2025-07-06', 0.00, 'returned'),
(5, 'ADDEC062009-CHILD00019', 3, '2025-06-30', '2025-07-05', NULL, 0.00, 'borrowed'),
(6, 'ADDEC062009-CHILD00019', 3, '2025-07-06', '2025-07-13', NULL, 0.00, 'borrowed'),
(7, 'WUHNOV061847-CLA00001', 1, '2025-07-06', '2025-07-13', NULL, 0.00, 'borrowed');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('admin','student') NOT NULL DEFAULT 'student'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `user_type`) VALUES
(1, 'librarian', '$2y$10$XjfnbIGv.mpnWW9M0J3Fp.6OeySYjRhTq4qD5L.SpqzVXMRq9W2O.', 'admin'),
(3, 'studentB', '$2y$10$VuGaMtuNdvkgthPON96WB.A/ZJx70mF8heVPQV8Co91gmxgscJiTK', 'student');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`book_id`);

--
-- Indexes for table `borrowings`
--
ALTER TABLE `borrowings`
  ADD PRIMARY KEY (`borrow_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `borrowings_ibfk_1` (`book_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `borrowings`
--
ALTER TABLE `borrowings`
  MODIFY `borrow_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `borrowings`
--
ALTER TABLE `borrowings`
  ADD CONSTRAINT `borrowings_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`),
  ADD CONSTRAINT `borrowings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
