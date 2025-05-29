PRAGMA FOREIGN_KEYS = ON;

DROP TABLE IF EXISTS USERS;
CREATE TABLE USERS(
    ID_USER INTEGER PRIMARY KEY AUTOINCREMENT UNIQUE,
    USERNAME VARCHAR(255) NOT NULL UNIQUE,
    PASSWORD VARCHAR(255) NOT NULL,
    EMAIL VARCHAR(255) NOT NULL UNIQUE,
    TYPE VARCHAR(255) NOT NULL CHECK (TYPE IN ('STUDENT', 'TUTOR', 'ADMIN'))
);

INSERT INTO USERS(USERNAME, PASSWORD, EMAIL, TYPE) VALUES
('martim', '$2y$10$KhO3NI22.eu0Nj4XOmO3n.hH/ILLRxsvAlxei.h40Y9Qyi1XlVKXO ', 'martim@gmail.com', 'ADMIN'),
('joana', '$2y$10$KhO3NI22.eu0Nj4XOmO3n.hH/ILLRxsvAlxei.h40Y9Qyi1XlVKXO ', 'joana@gmail.com', 'ADMIN'),
('joao123', '$2y$10$KhO3NI22.eu0Nj4XOmO3n.hH/ILLRxsvAlxei.h40Y9Qyi1XlVKXO ', 'joao@gmail.com', 'TUTOR'),
('pedro123', '$2y$10$KhO3NI22.eu0Nj4XOmO3n.hH/ILLRxsvAlxei.h40Y9Qyi1XlVKXO ', 'pedro@gmail.com', 'TUTOR'),
('ana123', '$2y$10$KhO3NI22.eu0Nj4XOmO3n.hH/ILLRxsvAlxei.h40Y9Qyi1XlVKXO ', 'ana@gmail.com','TUTOR'),
('luisa123', '$2y$10$KhO3NI22.eu0Nj4XOmO3n.hH/ILLRxsvAlxei.h40Y9Qyi1XlVKXO ', 'luisa@gmail.com', 'TUTOR'),
('carlos123', '$2y$10$KhO3NI22.eu0Nj4XOmO3n.hH/ILLRxsvAlxei.h40Y9Qyi1XlVKXO ', 'carlos@gmail.com', 'TUTOR'),
('maria123', '$2y$10$KhO3NI22.eu0Nj4XOmO3n.hH/ILLRxsvAlxei.h40Y9Qyi1XlVKXO ', 'maria@gmail.com', 'TUTOR'),
('jose123', '$2y$10$KhO3NI22.eu0Nj4XOmO3n.hH/ILLRxsvAlxei.h40Y9Qyi1XlVKXO ', 'jose@gmail.com', 'TUTOR'),
('luis123', '$2y$10$KhO3NI22.eu0Nj4XOmO3n.hH/ILLRxsvAlxei.h40Y9Qyi1XlVKXO ', 'luis@gmail.com', 'TUTOR'),
('catia123', '$2y$10$KhO3NI22.eu0Nj4XOmO3n.hH/ILLRxsvAlxei.h40Y9Qyi1XlVKXO ', 'catia@gmail.com', 'TUTOR'),
('simaobarbosa' , '$2y$10$KhO3NI22.eu0Nj4XOmO3n.hH/ILLRxsvAlxei.h40Y9Qyi1XlVKXO ', 'simao@gmail.com', 'TUTOR'),
('sara123', '$2y$10$KhO3NI22.eu0Nj4XOmO3n.hH/ILLRxsvAlxei.h40Y9Qyi1XlVKXO ', 'sara@gmail.com', 'STUDENT'),
('tiago123', '$2y$10$KhO3NI22.eu0Nj4XOmO3n.hH/ILLRxsvAlxei.h40Y9Qyi1XlVKXO ', 'tiago@gmail.com', 'STUDENT'),
('ana_maria', '$2y$10$KhO3NI22.eu0Nj4XOmO3n.hH/ILLRxsvAlxei.h40Y9Qyi1XlVKXO ', 'anamaria@gmail.com', 'STUDENT'),
('joana_maria', '$2y$10$KhO3NI22.eu0Nj4XOmO3n.hH/ILLRxsvAlxei.h40Y9Qyi1XlVKXO ', 'joanamaria@gmail.com', 'STUDENT'),
('maria_joao', '$2y$10$KhO3NI22.eu0Nj4XOmO3n.hH/ILLRxsvAlxei.h40Y9Qyi1XlVKXO ', 'mariajoao@gmail.com', 'STUDENT'),
('joao_pedro', '$2y$10$KhO3NI22.eu0Nj4XOmO3n.hH/ILLRxsvAlxei.h40Y9Qyi1XlVKXO ', 'joaopedro@gmail.com', 'STUDENT'),
('pedro_luis', '$2y$10$KhO3NI22.eu0Nj4XOmO3n.hH/ILLRxsvAlxei.h40Y9Qyi1XlVKXO ', 'pedroluis@gmail.com', 'STUDENT'),
('luis_maria', '$2y$10$KhO3NI22.eu0Nj4XOmO3n.hH/ILLRxsvAlxei.h40Y9Qyi1XlVKXO ', 'luismaria@gmail.com', 'STUDENT'),
('carlos_joao', '$2y$10$KhO3NI22.eu0Nj4XOmO3n.hH/ILLRxsvAlxei.h40Y9Qyi1XlVKXO ', 'carlosjoao@gmail.com', 'STUDENT'),
('camila123', '$2y$10$KhO3NI22.eu0Nj4XOmO3n.hH/ILLRxsvAlxei.h40Y9Qyi1XlVKXO ', 'camila@gmail.com', 'STUDENT'),
('ana_catarina', '$2y$10$KhO3NI22.eu0Nj4XOmO3n.hH/ILLRxsvAlxei.h40Y9Qyi1XlVKXO ', 'anacat@gmail.com', 'STUDENT'),
('joana_catarina', '$2y$10$KhO3NI22.eu0Nj4XOmO3n.hH/ILLRxsvAlxei.h40Y9Qyi1XlVKXO ', 'joanacat@gmail.com', 'STUDENT'),
('carolinadeslandes', '$2y$10$KhO3NI22.eu0Nj4XOmO3n.hH/ILLRxsvAlxei.h40Y9Qyi1XlVKXO ', 'carolina@gmail.com', 'STUDENT'),
('joaopedro123', '$2y$10$KhO3NI22.eu0Nj4XOmO3n.hH/ILLRxsvAlxei.h40Y9Qyi1XlVKXO ', 'joaop@gmail.com', 'STUDENT'),
('pedroluis123', '$2y$10$KhO3NI22.eu0Nj4XOmO3n.hH/ILLRxsvAlxei.h40Y9Qyi1XlVKXO ', 'pedroluis123@gmail.com', 'STUDENT'),
('testestudent', '$2y$10$KhO3NI22.eu0Nj4XOmO3n.hH/ILLRxsvAlxei.h40Y9Qyi1XlVKXO ', 'teste@gmail.com', 'STUDENT'),
('testetutor', '$2y$10$KhO3NI22.eu0Nj4XOmO3n.hH/ILLRxsvAlxei.h40Y9Qyi1XlVKXO ', 'teste2@gmail.com', 'TUTOR');


DROP TABLE IF EXISTS STUDENT;

CREATE TABLE STUDENT(
    ID_STUDENT VARCHAR(255) NOT NULL,
    NAME VARCHAR(255) NOT NULL,
    DATE_OF_BIRTH DATE NOT NULL,
    PROFILE_IMAGE VARCHAR(255) NOT NULL,
    DESCRIPTION TEXT,
    PRIMARY KEY (ID_STUDENT),
    FOREIGN KEY (ID_STUDENT) REFERENCES USERS(USERNAME) ON DELETE CASCADE
);

INSERT INTO STUDENT(ID_STUDENT, NAME, DATE_OF_BIRTH, PROFILE_IMAGE, DESCRIPTION) VALUES
('sara123', 'Sara', '2005-12-09', 'sara.png', 'Hi! I am Sara.'),
('tiago123', 'Tiago', '2005-12-09', 'tiago.png', 'Hi! I am Tiago.'),
('ana_maria', 'Ana Maria', '2005-12-09', 'anamaria.png', 'Hi! I am Ana Maria.'),
('joana_maria', 'Joana Maria', '2005-12-09', 'joanamaria.png', 'Hi! I am Joana Maria.'),
('maria_joao', 'Maria Joao', '2005-12-09', 'mariajoao.png', 'Hi! I am Maria Joao.'),
('joao_pedro', 'Joao Pedro', '2005-12-09', 'joaopedro.png', 'Hi! I am Joao Pedro.'),
('pedro_luis', 'Pedro Luis', '2005-12-09', 'pedroluis.png', 'Hi! I am Pedro Luis.'),
('luis_maria', 'Luis Maria', '2005-12-09', 'luismaria.png', 'Hi! I am Luis Maria.'),
('carlos_joao', 'Carlos Joao', '2005-12-09', 'carlosjoao.png', 'Hi! I am Carlos Joao.'),
('camila123','Camila','2005-12-09','camila.png','Hi! I am Camila.'),
('ana_catarina','Ana Catarina','2005-12-09','anacat.png','Hi! I am Ana Catarina.'),
('joana_catarina','Joana Catarina','2005-12-09','joanacat.png','Hi! I am Joana Catarina.'),
('carolinadeslandes','Carolina Deslandes','2005-12-09','carolina.png','Hi! I am Carolina Deslandes.'),
('joaopedro123','Joao Pedro','2005-12-09','joaop.png','Hi! I am Joao Pedro 123.'),
('pedroluis123','Pedro Luis','2005-12-09','pedroluis123.png','Hi! I am Pedro Luis 123.'),
('testestudent','Mariana','2005-12-09','mariana.png','Hi! I am Test Student.');



DROP TABLE IF EXISTS TUTOR;

CREATE TABLE TUTOR(
    ID_TUTOR VARCHAR(255) NOT NULL,
    NAME VARCHAR(255) NOT NULL,
    DATE_OF_BIRTH DATE NOT NULL,
    PROFILE_IMAGE VARCHAR(255) NOT NULL,
    DESCRIPTION TEXT,
    PRIMARY KEY (ID_TUTOR),
    FOREIGN KEY (ID_TUTOR) REFERENCES USERS(USERNAME) ON DELETE CASCADE
);

INSERT INTO TUTOR(ID_TUTOR, NAME, DATE_OF_BIRTH, PROFILE_IMAGE, DESCRIPTION) VALUES
('joao123', 'Joao', '2005-12-09', 'joao.png', 'Hi! I am Joao.'),
('pedro123', 'Pedro', '2005-12-09', 'pedro.png', 'Hi! I am Pedro.'),
('ana123', 'Ana', '2005-12-09', 'ana.png', 'Hi! I am Ana.'),
('luisa123', 'Luisa', '2005-12-09', 'luisa.png', 'Hi! I am Luisa.'),
('carlos123', 'Carlos', '2005-12-09', 'carlos.png', 'Hi! I am Carlos.'),
('maria123', 'Maria', '2005-12-09', 'maria.png', 'Hi! I am Maria.'),
('jose123', 'Jose', '2005-12-09', 'jose.png', 'Hi! I am Jose.'),
('luis123','Luis','2005-12-09','luis.png','Hi! I am Luis.'),
('catia123','Catia','2005-12-09','catia.png','Hi! I am Catia.'),
('simaobarbosa','Simao Barbosa','2005-12-09','simao.png','Hi! I am Simao Barbosa.'),
('testetutor','Simone','2005-12-09','simone.png','Hi! I am Test Tutor.');


CREATE TABLE ADMIN(
    ID_ADMIN VARCHAR(255) NOT NULL,
    NAME VARCHAR(255) NOT NULL,
    DATE_OF_BIRTH DATE NOT NULL,
    PROFILE_IMAGE VARCHAR(255) NOT NULL,
    DESCRIPTION TEXT,
    PRIMARY KEY (ID_ADMIN),
    FOREIGN KEY (ID_ADMIN) REFERENCES USERS(USERNAME) ON DELETE CASCADE
);

INSERT INTO ADMIN(ID_ADMIN, NAME, DATE_OF_BIRTH, PROFILE_IMAGE, DESCRIPTION) VALUES
('martim', 'Martim', '2005-12-09', 'martim.png', 'Hi! I am Martim.'),
('joana', 'Joana', '2005-12-09', 'joana.png', 'Hi! I am Joana.');


DROP TABLE IF EXISTS STUDENT_TUTOR;

CREATE TABLE STUDENT_TUTOR(
    STUDENT VARCHAR(255) REFERENCES STUDENT(ID_STUDENT) ON DELETE CASCADE,
    TUTOR VARCHAR(255) REFERENCES TUTOR(ID_TUTOR) ON DELETE CASCADE,
    PRIMARY KEY (STUDENT, TUTOR)
);



DROP TABLE IF EXISTS REQUEST;

CREATE TABLE REQUEST(
    ID_REQUEST INTEGER PRIMARY KEY AUTOINCREMENT,
    ACCEPTED BOOLEAN NOT NULL DEFAULT FALSE,
    STUDENT VARCHAR(255) REFERENCES STUDENT(ID_STUDENT) ON DELETE CASCADE,
    TUTOR VARCHAR(255) REFERENCES TUTOR(ID_TUTOR) ON DELETE CASCADE,
    REQUEST_DATE DATE NOT NULL,
    DATE_ACCEPTED DATE,
    MESSAGE TEXT,
    FOREIGN KEY (STUDENT) REFERENCES STUDENT(ID_STUDENT) ON DELETE CASCADE,
    FOREIGN KEY (TUTOR) REFERENCES TUTOR(ID_TUTOR) ON DELETE CASCADE,
    UNIQUE (STUDENT, TUTOR)
);

DROP TABLE IF EXISTS MESSAGE;

CREATE TABLE MESSAGE(
    ID_MESSAGE INTEGER PRIMARY KEY AUTOINCREMENT,
    SENDER VARCHAR(255) NOT NULL,
    RECEIVER VARCHAR(255) NOT NULL,
    CONTENT TEXT NOT NULL,
    TIMESTAMP DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (SENDER) REFERENCES USERS(USERNAME) ON DELETE CASCADE,
    FOREIGN KEY (RECEIVER) REFERENCES USERS(USERNAME) ON DELETE CASCADE
);


DROP TABLE IF EXISTS RATING;

CREATE TABLE RATING (
    ID_RATING INTEGER PRIMARY KEY AUTOINCREMENT,
    TUTOR VARCHAR(255) NOT NULL,
    STUDENT VARCHAR(255) NOT NULL,
    RATING INTEGER CHECK (
        RATING >= 0 AND 
        RATING <= 5 AND 
        (RATING * 10) % 5 = 0
    ),
    COMMENT TEXT,
    TIMESTAMP DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (STUDENT) REFERENCES STUDENT(ID_STUDENT) ON DELETE CASCADE,
    FOREIGN KEY (TUTOR) REFERENCES TUTOR(ID_TUTOR) ON DELETE CASCADE
);


DROP TABLE IF EXISTS LANGUAGE;

CREATE TABLE LANGUAGE(
    DESIGNATION VARCHAR(255) NOT NULL PRIMARY KEY
);

INSERT INTO LANGUAGE (DESIGNATION) VALUES
('English'),
('Spanish'),
('French'),
('German'),
('Mandarin'),
('Japanese'),
('Portuguese'),
('Russian'),
('Arabic'),
('Italian');


DROP TABLE IF EXISTS STUDENT_LANGUAGE;

CREATE TABLE STUDENT_LANGUAGE(
    STUDENT VARCHAR(225) REFERENCES STUDENT(ID_STUDENT) ON DELETE CASCADE,
    LANGUAGE VARCHAR(255) REFERENCES LANGUAGE(DESIGNATION) ON DELETE CASCADE,
    PRIMARY KEY (STUDENT, LANGUAGE)
);

INSERT INTO STUDENT_LANGUAGE (STUDENT, LANGUAGE) VALUES
('sara123', 'English'),
('tiago123', 'Spanish'),
('ana_maria', 'French'),
('joana_maria', 'German'),
('maria_joao', 'Mandarin'),
('joao_pedro', 'Japanese'),
('pedro_luis', 'Portuguese'),
('luis_maria', 'Russian'),
('carlos_joao', 'Arabic'),
('camila123','Italian'),
('ana_catarina','English'),
('joana_catarina','Spanish'),
('carolinadeslandes','French'),
('joaopedro123','German'),
('pedroluis123','Mandarin');



DROP TABLE IF EXISTS TUTOR_LANGUAGE;

CREATE TABLE TUTOR_LANGUAGE(
    TUTOR VARCHAR(225) REFERENCES TUTOR(ID_TUTOR) ON DELETE CASCADE,
    LANGUAGE VARCHAR(255) REFERENCES LANGUAGE(DESIGNATION) ON DELETE CASCADE,
    PRIMARY KEY (TUTOR, LANGUAGE)
);

INSERT INTO TUTOR_LANGUAGE (TUTOR, LANGUAGE) VALUES
('joao123', 'English'),
('pedro123', 'Spanish'),
('ana123', 'French'),
('luisa123', 'German'),
('carlos123', 'Mandarin'),
('maria123', 'Japanese'),
('jose123', 'Portuguese'),
('luis123','Russian'),
('catia123','Arabic'),
('simaobarbosa','Italian');



DROP TABLE IF EXISTS SUBJECT;

CREATE TABLE SUBJECT(
    DESIGNATION VARCHAR(255) NOT NULL PRIMARY KEY
);

INSERT INTO SUBJECT (DESIGNATION) VALUES
('Portuguese'),
('Mathematics'),
('English'),
('Social and Environmental Studies'),
('History and Geography of Portugal'),
('Natural Sciences'),
('French'),
('Spanish'),
('History'),
('Geography'),
('Physics and Chemistry'),
('German'),
('Philosophy'),
('Biology and Geology'),
('Descriptive Geometry'),
('Economics'),
('Mathematics Applied to Social Sciences'),
('Drawing'),
('History and Culture of the Arts');


DROP TABLE IF EXISTS STUDENT_SUBJECT;

CREATE TABLE STUDENT_SUBJECT(
    STUDENT VARCHAR(255) REFERENCES STUDENT(ID_STUDENT) ON DELETE CASCADE,
    SUBJECT VARCHAR(255) REFERENCES SUBJECT(DESIGNATION) ON DELETE CASCADE,
    STUDENT_LEVEL VARCHAR(255) REFERENCES STUDENT_LEVEL(DESIGNATION) ON DELETE CASCADE,
    PRIMARY KEY (STUDENT, SUBJECT, STUDENT_LEVEL)
);



DROP TABLE IF EXISTS TUTOR_SUBJECT;

CREATE TABLE TUTOR_SUBJECT(
    TUTOR VARCHAR(255) REFERENCES TUTOR(ID_TUTOR) ON DELETE CASCADE,
    SUBJECT VARCHAR(255) REFERENCES SUBJECT(DESIGNATION) ON DELETE CASCADE,
    TUTOR_LEVEL VARCHAR(255) REFERENCES TUTOR_LEVEL(DESIGNATION) ON DELETE CASCADE,
    PRIMARY KEY (TUTOR, SUBJECT, TUTOR_LEVEL)
);



DROP TABLE IF EXISTS TUTOR_LEVEL;

CREATE TABLE TUTOR_LEVEL(
    DESIGNATION VARCHAR(255) NOT NULL PRIMARY KEY
);

INSERT INTO TUTOR_LEVEL (DESIGNATION) VALUES
('Grade 1-4'),
('Grade 5-6'),
('Grade 7-9'),
('Grade 10-12');

DROP TABLE IF EXISTS STUDENT_LEVEL;

CREATE TABLE STUDENT_LEVEL(
    DESIGNATION VARCHAR(255) NOT NULL PRIMARY KEY
);

INSERT INTO STUDENT_LEVEL (DESIGNATION) VALUES
('Grade 1'),
('Grade 2'),
('Grade 3'),
('Grade 4'),
('Grade 5'),
('Grade 6'),
('Grade 7'),
('Grade 8'),
('Grade 9'),
('Grade 10'),
('Grade 11'),
('Grade 12');


INSERT INTO STUDENT_SUBJECT (STUDENT, SUBJECT, STUDENT_LEVEL) VALUES
('sara123', 'Portuguese', 'Grade 1'),
('tiago123', 'Mathematics', 'Grade 2'),
('ana_maria', 'English', 'Grade 3'),
('joana_maria', 'Social and Environmental Studies', 'Grade 4'),
('maria_joao', 'History and Geography of Portugal', 'Grade 5'),
('joao_pedro', 'Natural Sciences', 'Grade 6'),
('pedro_luis', 'French', 'Grade 7'),
('luis_maria', 'Spanish', 'Grade 8'),
('carlos_joao', 'History', 'Grade 9'),
('camila123','Geography','Grade 10'),
('ana_catarina','Physics and Chemistry','Grade 11'),
('joana_catarina','German','Grade 12'),
('carolinadeslandes','Philosophy','Grade 1'),
('joaopedro123','Biology and Geology','Grade 5'),
('pedroluis123','Descriptive Geometry','Grade 7');

INSERT INTO TUTOR_SUBJECT (TUTOR, SUBJECT, TUTOR_LEVEL) VALUES
('joao123', 'Portuguese', 'Grade 1-4'),
('pedro123', 'Mathematics', 'Grade 5-6'),
('ana123', 'English', 'Grade 7-9'),
('luisa123', 'Social and Environmental Studies', 'Grade 10-12'),
('carlos123', 'History and Geography of Portugal', 'Grade 1-4'),
('maria123', 'Natural Sciences', 'Grade 5-6'),
('jose123', 'French', 'Grade 7-9'),
('luis123','Spanish','Grade 10-12'),
('catia123','History','Grade 1-4'),
('simaobarbosa','Geography','Grade 5-6');
