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

DROP TABLE IF EXISTS STUDENT;

CREATE TABLE STUDENT(
    ID_STUDENT INTEGER PRIMARY KEY AUTOINCREMENT,
    NAME VARCHAR(255) NOT NULL,
    DATE_OF_BIRTH DATE NOT NULL,
    PROFILE_IMAGE VARCHAR(255) NOT NULL,
    EMAIL VARCHAR(255) NOT NULL,
    DESCRIPTION TEXT,
    SCHOOL_INSTITUTION VARCHAR(255) REFERENCES SCHOOL_INSTITUTION(NAME) ON DELETE CASCADE
);

INSERT INTO STUDENT (NAME, DATE_OF_BIRTH, PROFILE_IMAGE, EMAIL, DESCRIPTION, SCHOOL_INSTITUTION) VALUES
('John Doe', '2007-04-16','https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQrsM_hPnYdEBpv_XmL8SIGCjtSBpT1aX0bxA&s', 'normal@guy.com', 'Someone', 'School of Life'),
('Alice Wonderland', '2005-03-15', 'https://images.unsplash.com/photo-1502685104226-ee32379fefbe', 'alice@kuzco.com', 'Curious student', 'Xavier School for Gifted Youngsters'),
('Harry Potter', '2004-07-31', 'https://images.unsplash.com/photo-1544005313-94ddf0286df2', 'harry@hogwarts.com', 'Aspiring wizard', 'Hogwarts'),
('Socrates Wise', '2003-06-01', 'https://images.unsplash.com/photo-1527980965255-d3b416303d12', 'socrates@life.com', 'Seeker of truth', 'School of Life'),
('Tony Stark', '2002-05-29', 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d', 'tony@tech.com', 'Technology enthusiast', 'Tech Academy');


DROP TABLE IF EXISTS TUTOR;

CREATE TABLE TUTOR(
    ID_TUTOR INTEGER PRIMARY KEY AUTOINCREMENT,
    NAME VARCHAR(255) NOT NULL,
    DATE_OF_BIRTH DATE NOT NULL,
    PROFILE_IMAGE VARCHAR(255) NOT NULL,
    EMAIL VARCHAR(255) NOT NULL,
    DESCRIPTION TEXT
);

INSERT INTO TUTOR (NAME, DATE_OF_BIRTH, PROFILE_IMAGE, EMAIL, DESCRIPTION) VALUES
('Walter White', '1980-12-25', 'https://upload.wikimedia.org/wikipedia/en/0/03/Walter_White_S5B.png', 'chemistry@lover.com', 'Heisenberg, I am the one who knocks'),
('Martim', '2005-12-09', 'https://avatars.githubusercontent.com/u/169788723?v=4', 'martim@email.com', 'Hi, I am '),
('Bárbara Bandeira', '2000-12-09', 'https://znaki.fm/static/content/thumbs/1200x900/f/13/du3jdq---c4x3x50px50p--428a0e52cb7e5cc0bb3b3ce64cdc313f.jpg', 'babs@email.com', "Hi! I'm Barbara, a data scientist who enjoys working with data and turning it into actionable insights. Let's explore the world of data together!"),
('Poot Lovato', '2000-12-09', 'https://pbs.twimg.com/profile_images/660935401620697088/2pcj9lh4_400x400.jpg', 'poot@email.com', "Hi! I am Poot, a data scientist who enjoys working with data and turning it into actionable insights. Let's explore the world of data together!"),
('Kuzco', '2000-12-09', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQrsM_hPnYdEBpv_XmL8SIGCjtSBpT1aX0bxA&s', 'kuzco@gmail.com', "Hi! I'm Kuzco, a data scientist who enjoys working with data and turning it into actionable insights. Let's explore the world of data together!"),
('Andressa Urach', '1989-02-23', 'https://img.band.uol.com.br/image/2025/02/10/andressa-urach-vai-abrir-lanchonete-12311_400x300.jpg', 'andressa@email.com', 'Hi! My name is Andressa Urach, I am a philosophy teacher at FLUP.'),
('Karla Sofía Gascon', '1900-01-24', 'https://hips.hearstapps.com/hmg-prod/images/karla-sofia-gascon-retirada-vida-publica-67a6273971a78.jpg', 'karla@email.com', 'Hi! Me llamo Karla Sofía Gacon, Bingo.');
DROP TABLE IF EXISTS STUDENT_TUTOR;

CREATE TABLE STUDENT_TUTOR(
    STUDENT INTEGER REFERENCES STUDENT(ID_STUDENT) ON DELETE CASCADE,
    TUTOR INTEGER REFERENCES TUTOR(ID_TUTOR) ON DELETE CASCADE,
    PRIMARY KEY (STUDENT, TUTOR)
);

INSERT INTO STUDENT_TUTOR (STUDENT, TUTOR) VALUES
(1, 5),
(2, 1), 
(4, 5);

DROP TABLE IF EXISTS REQUEST;

CREATE TABLE REQUEST(
    ACCEPTED BOOLEAN NOT NULL DEFAULT FALSE,
    STUDENT INTEGER REFERENCES STUDENT(ID_STUDENT) ON DELETE CASCADE,
    TUTOR INTEGER REFERENCES TUTOR(ID_TUTOR) ON DELETE CASCADE,
    PRIMARY KEY (STUDENT, TUTOR)
);

INSERT INTO REQUEST (ACCEPTED, STUDENT, TUTOR) VALUES
(TRUE, 1, 5),
(TRUE, 2, 1),
(FALSE, 3, 2),
(TRUE, 4, 5),
(FALSE, 5, 4);

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
(1, 'Monday', '09:00', '11:00'),
(2, 'Tuesday', '14:00', '16:00'),
(3, 'Wednesday', '10:00', '12:00'),
(4, 'Thursday', '15:00', '17:00'),
(5, 'Friday', '08:00', '10:00');

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
(1, 5, 'Monday', '15:00', '16:00'),
(2, 1, 'Tuesday', '10:00', '11:00'),
(4, 5, 'Thursday', '14:00', '15:00');

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
    LANGUAGE REFERENCES LANGUAGE(DESIGNATION) ON DELETE CASCADE,
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
    LANGUAGE REFERENCES LANGUAGE(DESIGNATION) ON DELETE CASCADE,
    PRIMARY KEY (TUTOR, LANGUAGE)
);

INSERT INTO TUTOR_LANGUAGE (TUTOR, LANGUAGE) VALUES
(1, 'English'),
(2, 'English'),
(2, 'French'),
(3, 'English'),
(3, 'Spanish'),
(4, 'English'),
(4, 'Italian'),
(5, 'Greek');

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
    SUBJECT REFERENCES SUBJECT(DESIGNATION) ON DELETE CASCADE,
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
    SUBJECT REFERENCES SUBJECT(DESIGNATION) ON DELETE CASCADE,
    GRADE INTEGER NOT NULL,
    CHECK (GRADE>=1 AND GRADE<=12),
    PRIMARY KEY (TUTOR, SUBJECT)
);

INSERT INTO TUTOR_SUBJECT (TUTOR, SUBJECT, GRADE) VALUES
(1, 'History', 12),
(2, 'Chemistry', 12),
(2, 'Phylosophy', 12),
(3, 'Chemistry', 12),
(4, 'Mathematics', 12),
(5, 'Phylosophy', 12);

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
(1, 'History and Philosophy', 'Oxford University'),
(2, 'Literature', 'Cambridge University'),
(3, 'Chemical Engineering', 'MIT'),
(4, 'Computer Science', 'Stanford University'),
(5, 'History and Philosophy', 'University of Athens');