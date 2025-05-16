PRAGMA FOREIGN_KEYS = ON;

DROP TABLE IF EXISTS SCHOOL_INSTITUTION;

CREATE TABLE SCHOOL_INSTITUTION(
    NAME VARCHAR(255) NOT NULL PRIMARY KEY
);

INSERT INTO SCHOOL_INSTITUTION (NAME) VALUES
('Xavier School for Gifted Youngsters'),
('Hogwarts'),
('School of Life'),
('Tech Academy');

DROP TABLE IF EXISTS USERS;
CREATE TABLE USERS(
    ID_USER INTEGER PRIMARY KEY AUTOINCREMENT,
    USERNAME VARCHAR(255) NOT NULL UNIQUE,
    PASSWORD VARCHAR(255) NOT NULL,
    EMAIL VARCHAR(255) NOT NULL UNIQUE,
    TYPE VARCHAR(255) NOT NULL CHECK (TYPE IN ('STUDENT', 'TUTOR', 'ADMIN'))
);

INSERT INTO USERS (USERNAME, PASSWORD, EMAIL, TYPE) VALUES
('john_doe', 'password123', 'normal@guy.com', 'STUDENT'),
('alice_w', 'alice123', 'alice@kuzco.com', 'STUDENT'),
('harry_p', 'hogwarts', 'harry@hogwarts.com', 'STUDENT'),
('socrates', 'philosopher', 'socrates@life.com', 'STUDENT'),
('tony_s', 'ironman', 'tony@tech.com', 'STUDENT'),
('walter_w', 'heisenberg', 'chemistry@lover.com', 'TUTOR'),
('martim_t', 'tutor123', 'martim@email.com', 'TUTOR'),
('barbara_b', 'datascience', 'babs@email.com', 'TUTOR'),
('poot_l', 'poot123', 'poot@email.com', 'TUTOR'),
('kuzco_t', 'emperorsnew', 'kuzco@gmail.com', 'TUTOR'),
('andressa_u', 'philosophy', 'andressa@email.com', 'TUTOR'),
('karla_g', 'bingobongo', 'karla@email.com', 'TUTOR'),
('admin1', '74913f5cd5f61ec0bcfdb775414c2fb3d161b620', 'admin@learn2day.com', 'ADMIN'),
('martimadmin', 'martim123', 'martim@admin', 'ADMIN');

DROP TABLE IF EXISTS STUDENT;

CREATE TABLE STUDENT(
    ID_STUDENT INTEGER NOT NULL,
    NAME VARCHAR(255) NOT NULL,
    DATE_OF_BIRTH DATE NOT NULL,
    PROFILE_IMAGE VARCHAR(255) NOT NULL,
    DESCRIPTION TEXT,
    SCHOOL_INSTITUTION VARCHAR(255) NOT NULL,
    PRIMARY KEY (ID_STUDENT),
    FOREIGN KEY (ID_STUDENT) REFERENCES USERS(ID_USER) ON DELETE CASCADE,
    FOREIGN KEY (SCHOOL_INSTITUTION) REFERENCES SCHOOL_INSTITUTION(NAME) ON DELETE CASCADE
);

INSERT INTO STUDENT (ID_STUDENT, NAME, DATE_OF_BIRTH, PROFILE_IMAGE, DESCRIPTION, SCHOOL_INSTITUTION) VALUES
(1, 'John Doe', '2007-04-16','https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQrsM_hPnYdEBpv_XmL8SIGCjtSBpT1aX0bxA&s', 'Someone', 'School of Life'),
(2, 'Alice Wonderland', '2005-03-15', 'https://images.unsplash.com/photo-1502685104226-ee32379fefbe', 'Curious student', 'Xavier School for Gifted Youngsters'),
(3, 'Harry Potter', '2004-07-31', 'https://images.unsplash.com/photo-1544005313-94ddf0286df2', 'Aspiring wizard', 'Hogwarts'),
(4, 'Socrates Wise', '2003-06-01', 'https://images.unsplash.com/photo-1527980965255-d3b416303d12', 'Seeker of truth', 'School of Life'),
(5, 'Tony Stark', '2002-05-29', 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d', 'Technology enthusiast', 'Tech Academy');

DROP TABLE IF EXISTS TUTOR;

CREATE TABLE TUTOR(
    ID_TUTOR INTEGER NOT NULL,
    NAME VARCHAR(255) NOT NULL,
    DATE_OF_BIRTH DATE NOT NULL,
    PROFILE_IMAGE VARCHAR(255) NOT NULL,
    DESCRIPTION TEXT,
    PRIMARY KEY (ID_TUTOR),
    FOREIGN KEY (ID_TUTOR) REFERENCES USERS(ID_USER) ON DELETE CASCADE
);

INSERT INTO TUTOR (ID_TUTOR, NAME, DATE_OF_BIRTH, PROFILE_IMAGE, DESCRIPTION) VALUES
(6, 'Walter White', '1980-12-25', 'https://upload.wikimedia.org/wikipedia/en/0/03/Walter_White_S5B.png', 'Heisenberg, I am the one who knocks'),
(7, 'Martim', '2005-12-09', 'https://avatars.githubusercontent.com/u/169788723?v=4','Hi, I am '),
(8, 'Bárbara Bandeira', '2000-12-09', 'https://znaki.fm/static/content/thumbs/1200x900/f/13/du3jdq---c4x3x50px50p--428a0e52cb7e5cc0bb3b3ce64cdc313f.jpg', "Hi! I'm Barbara, a data scientist who enjoys working with data and turning it into actionable insights. Let's explore the world of data together!"),
(9, 'Poot Lovato', '2000-12-09', 'https://pbs.twimg.com/profile_images/660935401620697088/2pcj9lh4_400x400.jpg', "Hi! I am Poot, a data scientist who enjoys working with data and turning it into actionable insights. Let's explore the world of data together!"),
(10, 'Kuzco', '2000-12-09', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQrsM_hPnYdEBpv_XmL8SIGCjtSBpT1aX0bxA&s', "Hi! I'm Kuzco, a data scientist who enjoys working with data and turning it into actionable insights. Let's explore the world of data together!"),
(11, 'Andressa Urach', '1989-02-23', 'https://img.band.uol.com.br/image/2025/02/10/andressa-urach-vai-abrir-lanchonete-12311_400x300.jpg', 'Hi! My name is Andressa Urach, I am a philosophy teacher at FLUP.'),
(12, 'Karla Sofía Gascon', '1900-01-24', 'https://hips.hearstapps.com/hmg-prod/images/karla-sofia-gascon-retirada-vida-publica-67a6273971a78.jpg', 'Hi! Me llamo Karla Sofía Gacon, Bingo.');

CREATE TABLE ADMIN(
    ID_ADMIN INTEGER NOT NULL,
    NAME VARCHAR(255) NOT NULL,
    DATE_OF_BIRTH DATE NOT NULL,
    PROFILE_IMAGE VARCHAR(255) NOT NULL,
    DESCRIPTION TEXT,
    PRIMARY KEY (ID_ADMIN),
    FOREIGN KEY (ID_ADMIN) REFERENCES USERS(ID_USER) ON DELETE CASCADE
);

INSERT INTO ADMIN (ID_ADMIN, NAME, DATE_OF_BIRTH, PROFILE_IMAGE, DESCRIPTION) VALUES
(13, 'System Admin', '1990-01-01', 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png', 'System Administrator'),
(14, 'Martim Admin', '1990-01-01', 'https://avatars.githubusercontent.com/u/169788723?v=4', 'System Administrator');

DROP TABLE IF EXISTS STUDENT_TUTOR;

CREATE TABLE STUDENT_TUTOR(
    STUDENT INTEGER REFERENCES STUDENT(ID_STUDENT) ON DELETE CASCADE,
    TUTOR INTEGER REFERENCES TUTOR(ID_TUTOR) ON DELETE CASCADE,
    PRIMARY KEY (STUDENT, TUTOR)
);

INSERT INTO STUDENT_TUTOR (STUDENT, TUTOR) VALUES
(1, 10),
(2, 6), 
(4, 10);

DROP TABLE IF EXISTS REQUEST;

CREATE TABLE REQUEST(
    ACCEPTED BOOLEAN NOT NULL DEFAULT FALSE,
    STUDENT INTEGER REFERENCES STUDENT(ID_STUDENT) ON DELETE CASCADE,
    TUTOR INTEGER REFERENCES TUTOR(ID_TUTOR) ON DELETE CASCADE,
    PRIMARY KEY (STUDENT, TUTOR)
);

INSERT INTO REQUEST (ACCEPTED, STUDENT, TUTOR) VALUES
(TRUE, 1, 10),
(TRUE, 2, 6),
(FALSE, 3, 7),
(TRUE, 4, 10),
(FALSE, 5, 11);

DROP TABLE IF EXISTS RATING;

CREATE TABLE RATING (
    ID_RATING INTEGER PRIMARY KEY AUTOINCREMENT,
    CLASSIFICATION INTEGER CHECK (
        CLASSIFICATION >= 0 AND 
        CLASSIFICATION <= 5 AND 
        (CLASSIFICATION * 10) % 5 = 0
    ),
    COMMENTARY TEXT,
    STUDENT_ID INTEGER NOT NULL,
    FOREIGN KEY (STUDENT_ID) REFERENCES STUDENT(ID_STUDENT) ON DELETE CASCADE
);

INSERT INTO RATING (CLASSIFICATION, COMMENTARY, STUDENT_ID) VALUES
(4, 'Good effort', 1),
(5, 'Excellent work', 2),
(3, 'Needs improvement', 3);

DROP TABLE IF EXISTS DAY;

CREATE TABLE DAY(
    WEEK_DAY VARCHAR(255) NOT NULL PRIMARY KEY,
    CHECK (WEEK_DAY IN ('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'))
);

INSERT INTO DAY (WEEK_DAY) VALUES
('Monday'),
('Tuesday'),
('Wednesday'),
('Thursday'),
('Friday'),
('Saturday'),
('Sunday');

DROP TABLE IF EXISTS STUDENT_AVAILABILITY;

CREATE TABLE STUDENT_AVAILABILITY(
    STUDENT INTEGER REFERENCES STUDENT(ID_STUDENT) ON DELETE CASCADE,
    WEEK_DAY VARCHAR(255) REFERENCES DAY(WEEK_DAY) ON DELETE CASCADE,
    START_TIME TIME NOT NULL,
    END_TIME TIME NOT NULL,
    PRIMARY KEY (STUDENT,WEEK_DAY)
);

INSERT INTO STUDENT_AVAILABILITY (STUDENT, WEEK_DAY, START_TIME, END_TIME) VALUES
(1, 'Monday', '15:00', '17:00'),
(2, 'Tuesday', '10:00', '12:00'),
(3, 'Wednesday', '09:00', '11:00'),
(4, 'Thursday', '14:00', '16:00'),
(5, 'Friday', '13:00', '15:00');

DROP TABLE IF EXISTS TUTOR_AVAILABILITY;

CREATE TABLE TUTOR_AVAILABILITY(
    TUTOR INTEGER REFERENCES TUTOR(ID_TUTOR) ON DELETE CASCADE,
    WEEK_DAY VARCHAR(255) REFERENCES DAY(WEEK_DAY) ON DELETE CASCADE,
    START_TIME TIME NOT NULL,
    END_TIME TIME NOT NULL,
    PRIMARY KEY (TUTOR, WEEK_DAY)
);

INSERT INTO TUTOR_AVAILABILITY (TUTOR, WEEK_DAY, START_TIME, END_TIME) VALUES
(6, 'Monday', '09:00', '11:00'),
(7, 'Tuesday', '14:00', '16:00'),
(8, 'Wednesday', '10:00', '12:00'),
(9, 'Thursday', '15:00', '17:00'),
(10, 'Friday', '08:00', '10:00');

DROP TABLE IF EXISTS CONTACT;

CREATE TABLE CONTACT(
    STUDENT INTEGER REFERENCES STUDENT(ID_STUDENT) ON DELETE CASCADE,
    TUTOR INTEGER REFERENCES TUTOR(ID_TUTOR) ON DELETE CASCADE,
    WEEK_DAY VARCHAR(255) REFERENCES DAY(WEEK_DAY) ON DELETE CASCADE,
    START_TIME TIME NOT NULL,
    END_TIME TIME NOT NULL,
    PRIMARY KEY (STUDENT, TUTOR)
);

INSERT INTO CONTACT (STUDENT, TUTOR, WEEK_DAY, START_TIME, END_TIME) VALUES
(1, 10, 'Monday', '15:00', '16:00'),
(2, 6, 'Tuesday', '10:00', '11:00'),
(4, 10, 'Thursday', '14:00', '15:00');

DROP TABLE IF EXISTS LANGUAGE;

CREATE TABLE LANGUAGE(
    DESIGNATION VARCHAR(255) NOT NULL PRIMARY KEY
);

INSERT INTO LANGUAGE (DESIGNATION) VALUES
('English'),
('Spanish'),
('French'),
('Portuguese'),
('German'),
('Italian'),
('Greek'),
('Russian'),
('Japanese'),
('Chinese'),
('Swedish');

DROP TABLE IF EXISTS STUDENT_LANGUAGE;

CREATE TABLE STUDENT_LANGUAGE(
    STUDENT INTEGER REFERENCES STUDENT(ID_STUDENT) ON DELETE CASCADE,
    LANGUAGE VARCHAR(255) REFERENCES LANGUAGE(DESIGNATION) ON DELETE CASCADE,
    PRIMARY KEY (STUDENT, LANGUAGE)
);

INSERT INTO STUDENT_LANGUAGE (STUDENT, LANGUAGE) VALUES
(1, 'English'),
(2, 'English'),
(2, 'French'),
(3, 'English'),
(4, 'Greek'),
(5, 'English'),
(5, 'Portuguese');

DROP TABLE IF EXISTS TUTOR_LANGUAGE;

CREATE TABLE TUTOR_LANGUAGE(
    TUTOR INTEGER REFERENCES TUTOR(ID_TUTOR) ON DELETE CASCADE,
    LANGUAGE VARCHAR(255) REFERENCES LANGUAGE(DESIGNATION) ON DELETE CASCADE,
    PRIMARY KEY (TUTOR, LANGUAGE)
);

INSERT INTO TUTOR_LANGUAGE (TUTOR, LANGUAGE) VALUES
(6, 'English'),
(7, 'English'),
(7, 'French'),
(8, 'English'),
(8, 'Spanish'),
(9, 'English'),
(9, 'Italian'),
(10, 'Greek');

DROP TABLE IF EXISTS SUBJECT;

CREATE TABLE SUBJECT(
    DESIGNATION VARCHAR(255) NOT NULL PRIMARY KEY
);

INSERT INTO SUBJECT (DESIGNATION) VALUES
('English'),
('Mathematics'),
('Physics'),
('Chemistry'),
('Phylosophy'),
('Biology'),
('History'),
('Geography');

DROP TABLE IF EXISTS STUDENT_SUBJECT;

CREATE TABLE STUDENT_SUBJECT(
    STUDENT INTEGER REFERENCES STUDENT(ID_STUDENT) ON DELETE CASCADE,
    SUBJECT VARCHAR(255) REFERENCES SUBJECT(DESIGNATION) ON DELETE CASCADE,
    GRADE INTEGER NOT NULL,
    CHECK (GRADE>=1 AND GRADE<=12),
    PRIMARY KEY (STUDENT, SUBJECT)
);

INSERT INTO STUDENT_SUBJECT (STUDENT, SUBJECT, GRADE) VALUES
(1, 'History', 10),
(2, 'Physics', 11),
(2, 'Mathematics', 11),
(3, 'Phylosophy', 12),
(4, 'Phylosophy', 12),
(5, 'Chemistry', 12),
(5, 'Mathematics', 12);

DROP TABLE IF EXISTS TUTOR_SUBJECT;

CREATE TABLE TUTOR_SUBJECT(
    TUTOR INTEGER REFERENCES TUTOR(ID_TUTOR) ON DELETE CASCADE,
    SUBJECT VARCHAR(255) REFERENCES SUBJECT(DESIGNATION) ON DELETE CASCADE,
    GRADE INTEGER NOT NULL,
    CHECK (GRADE>=1 AND GRADE<=12),
    PRIMARY KEY (TUTOR, SUBJECT)
);

INSERT INTO TUTOR_SUBJECT (TUTOR, SUBJECT, GRADE) VALUES
(6, 'History', 12),
(7, 'Chemistry', 12),
(7, 'Phylosophy', 12),
(8, 'Chemistry', 12),
(9, 'Mathematics', 12),
(10, 'Phylosophy', 12);

DROP TABLE IF EXISTS DEGREE;

CREATE TABLE DEGREE(
    DESIGNATION VARCHAR(255) NOT NULL PRIMARY KEY
);

INSERT INTO DEGREE (DESIGNATION) VALUES
('Chemical Engineering'),
('Literature'),
('History and Philosophy'),
('Mechanical Engineering'),
('Computer Science'),
('Physics Engineering');

DROP TABLE IF EXISTS TUTOR_DEGREE;

CREATE TABLE TUTOR_DEGREE(
    TUTOR INTEGER REFERENCES TUTOR(ID_TUTOR) ON DELETE CASCADE,
    DEGREE VARCHAR(255) REFERENCES DEGREE(DESIGNATION) ON DELETE CASCADE,
    UNIVERSITY VARCHAR(255) NOT NULL,
    PRIMARY KEY (TUTOR, DEGREE)
);

INSERT INTO TUTOR_DEGREE (TUTOR, DEGREE, UNIVERSITY) VALUES
(6, 'History and Philosophy', 'Oxford University'),
(7, 'Literature', 'Cambridge University'),
(8, 'Chemical Engineering', 'MIT'),
(9, 'Computer Science', 'Stanford University'),
(10, 'History and Philosophy', 'University of Athens');