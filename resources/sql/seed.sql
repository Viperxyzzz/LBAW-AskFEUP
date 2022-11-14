DROP SCHEMA IF EXISTS lbaw2221 CASCADE;
CREATE SCHEMA lbaw2221;
SET search_path TO lbaw2221;

DROP TABLE IF EXISTS topic CASCADE;
CREATE TABLE topic(
    topic_id INTEGER PRIMARY KEY,
    topic_name TEXT UNIQUE NOT NULL, 
    num_views INTEGER NOT NULL
);

DROP TABLE IF EXISTS tag CASCADE;
CREATE TABLE tag(
    tag_id SERIAL PRIMARY KEY,
    tag_name TEXT UNIQUE NOT NULL
);

DROP TABLE IF EXISTS badge CASCADE;
CREATE TABLE badge(
    badge_id SERIAL PRIMARY KEY, 
    badge_name TEXT UNIQUE NOT NULL
);

DROP TABLE IF EXISTS users CASCADE;
CREATE TABLE users (
    user_id SERIAL PRIMARY KEY,
    username TEXT UNIQUE,
    email TEXT NOT NULL UNIQUE,
    name TEXT NOT NULL,
    password TEXT NOT NULL, 
    score INTEGER NOT NULL,
    is_moderator BOOLEAN NOT NULL,
    is_admin BOOLEAN NOT NULL,
    remember_token VARCHAR
);

DROP TABLE IF EXISTS question CASCADE;
CREATE TABLE question (
    question_id SERIAL PRIMARY KEY, 
    title TEXT NOT NULL,
    full_text TEXT NOT NULL, 
    num_votes INTEGER NOT NULL CHECK (num_votes >= 0),
    num_views INTEGER NOT NULL CHECK (num_views >= 0),
    num_answers INTEGER NOT NULL CHECK (num_answers >= 0),
    date DATE NOT NULL CHECK (date <= CURRENT_TIMESTAMP),
    was_edited BOOLEAN NOT NULL DEFAULT FALSE,
    author_id INTEGER REFERENCES users (user_id) ON UPDATE CASCADE ON DELETE SET NULL
);


DROP TABLE IF EXISTS answer CASCADE;
CREATE TABLE answer (
    answer_id SERIAL PRIMARY KEY,
    full_text TEXT NOT NULL,
    num_votes INTEGER NOT NULL CHECK (num_votes >= 0),
    is_correct BOOLEAN NOT NULL,
    was_edited BOOLEAN NOT NULL DEFAULT FALSE,
    date TIMESTAMP WITH TIME ZONE DEFAULT now() NOT NULL CHECK (date <= CURRENT_TIMESTAMP),
    question_id INTEGER NOT NULL REFERENCES question (question_id) ON UPDATE CASCADE ON DELETE CASCADE,
    user_id INTEGER NOT NULL REFERENCES users (user_id) ON UPDATE CASCADE ON DELETE SET NULL
);

DROP TABLE IF EXISTS notification CASCADE;
CREATE TABLE notification (
    notification_id SERIAL PRIMARY KEY,
    notification_text TEXT NOT NULL,
    date TIMESTAMP WITH TIME ZONE DEFAULT now() NOT NULL CHECK (date <= CURRENT_TIMESTAMP),
    viewed BOOLEAN NOT NULL DEFAULT FALSE,
    user_id INTEGER NOT NULL REFERENCES users (user_id) ON UPDATE CASCADE ON DELETE SET NULL
);

DROP TABLE IF EXISTS comment CASCADE;
CREATE TABLE comment(
  comment_id SERIAL PRIMARY key,
  full_text TEXT NOT NULL,
  num_votes INTEGER NOT NULL CONSTRAINT num_votes_ck CHECK (num_votes >= 0),
  date DATE NOT NULL CHECK (date <= CURRENT_TIMESTAMP),
  question_id INTEGER NOT NULL REFERENCES question (question_id) ON UPDATE CASCADE ON DELETE CASCADE, 
  answer_id INTEGER REFERENCES answer (answer_id) ON UPDATE CASCADE ON DELETE CASCADE, 
  user_id INTEGER NOT NULL REFERENCES users (user_id) ON UPDATE CASCADE ON DELETE SET NULL
 );
 
 DROP TABLE IF EXISTS report CASCADE;
 create table report(
   report_id SERIAL PRIMARY KEY,
   reason TEXT NOT NULL,
   date DATE NOT NULL CHECK (date <= CURRENT_TIMESTAMP),
   question_id INTEGER NOT NULL REFERENCES question (question_id) ON UPDATE CASCADE ON DELETE CASCADE,
   answer_id INTEGER REFERENCES answer (answer_id) ON UPDATE CASCADE ON DELETE CASCADE,
   comment_id INTEGER REFERENCES comment (comment_id) ON UPDATE CASCADE ON DELETE CASCADE
);

DROP TABLE IF EXISTS topic_tag CASCADE;
CREATE TABLE topic_tag(
        topic_id INTEGER REFERENCES topic (topic_id) ON UPDATE CASCADE,
        tag_id INTEGER REFERENCES tag (tag_id) ON UPDATE CASCADE,
        PRIMARY KEY (topic_id, tag_id)
);


DROP TABLE IF EXISTS user_tag CASCADE;
CREATE table user_tag(
        user_id INTEGER REFERENCES users (user_id) ON UPDATE CASCADE,
        tag_id INTEGER REFERENCES tag (tag_id) ON UPDATE CASCADE,
        PRIMARY KEY (user_id, tag_id)
);

DROP TABLE IF EXISTS user_badge CASCADE;
CREATE TABLE user_badge
(
    user_id INTEGER,
    badge_id INTEGER,
    num_supports INTEGER,
    date TIMESTAMP
    WITH TIME ZONE DEFAULT now() NOT NULL CHECK (date <= CURRENT_TIMESTAMP),
    PRIMARY KEY (user_id, badge_id)
);

DROP TABLE IF EXISTS user_badge_support CASCADE;
CREATE TABLE user_badge_support
(
    user_who_supports INTEGER REFERENCES users (user_id) ON UPDATE CASCADE,
    user_who_achieves INTEGER,
    badge_id INTEGER,
    PRIMARY KEY (user_who_supports, user_who_achieves, badge_id),
    FOREIGN KEY (user_who_achieves, badge_id) REFERENCES user_badge (user_id, badge_id)
);

DROP TABLE IF EXISTS question_tag CASCADE;
CREATE TABLE question_tag
(
    question_id INTEGER,
    tag_id INTEGER,
    PRIMARY KEY (question_id, tag_id)
);

DROP TABLE IF EXISTS question_user_follower CASCADE;
CREATE TABLE question_user_follower
(
    question_id INTEGER REFERENCES question (question_id) ON UPDATE CASCADE,
    user_id INTEGER REFERENCES users (user_id) ON UPDATE CASCADE,
    PRIMARY KEY (question_id, user_id)
);



--Triggers

-- Create trigger to increment num of answers after insert on questions.
CREATE OR REPLACE FUNCTION num_answers_update() RETURNS TRIGGER AS
$FUNC1$
BEGIN
        IF (TG_OP = 'INSERT') THEN
                UPDATE question SET num_answers = num_answers + 1
                WHERE question_id = NEW.question_id; 
        
        ELSIF (TG_OP = 'DELETE') THEN
              UPDATE question SET num_answers = num_answers - 1
              WHERE question_id = OLD.question_id; 
        END IF;
        RETURN NULL;
END
$FUNC1$
LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS num_answers_update ON answer CASCADE;
CREATE TRIGGER num_answers_update
    AFTER INSERT OR DELETE ON answer
    FOR EACH ROW EXECUTE FUNCTION num_answers_update();


CREATE OR REPLACE FUNCTION num_supports_update() RETURNS TRIGGER AS 
$FUNC2$
BEGIN
    IF (TG_OP = 'INSERT') THEN
            UPDATE user_badge
            SET num_supports = num_supports + 1
            WHERE user_id = NEW.user_who_achieves AND badge_id = NEW.badge_id;
        
    ELSIF (TG_OP = 'DELETE') THEN
            UPDATE user_badge
            SET num_supports = num_supports - 1
            WHERE user_id = OLD.user_who_achieves AND badge_id = OLD.badge_id;
    END IF;
    RETURN NULL;
END
$FUNC2$
LANGUAGE plpgsql;

-- Create trigger to increment num of supports after insert on user_badge.
DROP TRIGGER IF EXISTS num_supports_update ON user_badge_support CASCADE;
CREATE TRIGGER num_supports_update
    AFTER INSERT OR DELETE ON user_badge_support 
    FOR EACH ROW EXECUTE FUNCTION num_supports_update();


-- Create trigger to increment num of answers after insert on questions.
CREATE OR REPLACE FUNCTION first_question() RETURNS TRIGGER AS
$FUNC3$
BEGIN
        IF NOT EXISTS (SELECT * FROM question WHERE author_id = NEW.author_id) THEN
                INSERT INTO user_badge 
                    SELECT
                        NEW.author_id,
                        (SELECT badge_id FROM badge WHERE badge_name = 'First question'),
                        0,
                        now();
                UPDATE users SET score = score + 10 WHERE user_id = NEW.author_id; 
        END IF;
        RETURN NEW;

END
$FUNC3$
LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS first_question ON question CASCADE;
CREATE TRIGGER first_question
    BEFORE INSERT ON question
    FOR EACH ROW EXECUTE FUNCTION first_question();


--
CREATE OR REPLACE FUNCTION first_answer() RETURNS TRIGGER AS
$FUNC4$
BEGIN
        IF NOT EXISTS (SELECT * FROM answer WHERE user_id = NEW.user_id) THEN
                INSERT INTO user_badge (user_id, badge_id, num_supports, date) 
                	VALUES (
                                NEW.user_id,
                                (SELECT badge_id FROM badge WHERE badge_name = 'First answer'),
                                0,
                                now()
                            );
                UPDATE users SET score = score + 10 WHERE user_id = NEW.user_id; 
        END IF;
        RETURN NEW;

END
$FUNC4$
LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS first_answer ON answer CASCADE;
CREATE TRIGGER first_answer
    BEFORE INSERT ON answer
    FOR EACH ROW EXECUTE FUNCTION first_answer();


--
CREATE OR REPLACE FUNCTION novice_achievement() RETURNS TRIGGER AS $FUNC5$
BEGIN
    
    INSERT INTO user_badge (user_id, badge_id, num_supports, date) 
    VALUES (
                NEW.user_id,
                (SELECT badge_id FROM badge WHERE badge_name = 'Novice'),
                0,
                now()
            );
    RETURN NULL;
END
$FUNC5$
LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS novice_achievement ON users CASCADE;
CREATE TRIGGER novice_achievement
    AFTER INSERT ON users
    FOR EACH ROW EXECUTE FUNCTION novice_achievement();


--
CREATE OR REPLACE FUNCTION score_achievement() RETURNS TRIGGER AS $FUNC6$
BEGIN
    IF OLD.score < 100 AND NEW.score >= 100 THEN
        INSERT INTO user_badge (user_id, badge_id, num_supports, date) 
        VALUES (
                    NEW.user_id,
                    (SELECT badge_id FROM badge WHERE badge_name = 'Rising star'),
                    0,
                    now()
                );
    ELSIF OLD.score < 1000 AND NEW.score >= 1000 THEN
        INSERT INTO user_badge (user_id, badge_id, num_supports, date) 
        VALUES (
                    NEW.user_id,
                    (SELECT badge_id FROM badge WHERE badge_name = 'Expert'),
                    0,
                    now()
                );
    ELSIF OLD.score < 10000 AND NEW.score >= 10000 THEN
        INSERT INTO user_badge (user_id, badge_id, num_supports, date) 
        VALUES (
                    NEW.user_id,
                    (SELECT badge_id FROM badge WHERE badge_name = 'Master'),
                    0,
                    now()
                );
    END IF;
    RETURN NULL;
END
$FUNC6$
LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS score_achievement ON users CASCADE;
CREATE TRIGGER score_achievement
    AFTER UPDATE ON users
    FOR EACH ROW EXECUTE FUNCTION score_achievement();


CREATE OR REPLACE FUNCTION increment_score() RETURNS TRIGGER AS 
$FUNC7$
BEGIN
    IF (TG_TABLE_NAME = 'question') THEN
        UPDATE users SET score = score + 20 where user_id = NEW.author_id;
    ELSIF (TG_TABLE_NAME = 'answer' AND TG_OP = 'INSERT') THEN
        UPDATE users SET score = score + 50 where user_id = NEW.user_id;
    ELSIF (TG_TABLE_NAME = 'answer' AND TG_OP = 'UPDATE') THEN
        IF OLD.is_correct = 'No' AND NEW.is_correct = 'Yes' THEN
            UPDATE users SET score = score + 100 WHERE user_id = NEW.user_id;
        END IF; 
    ELSIF (TG_TABLE_NAME = 'comment') THEN
        UPDATE users SET score = score + 15 WHERE user_id = NEW.user_id;
    END IF;
    RETURN NULL;
END
$FUNC7$
LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS increment_score ON question CASCADE;
CREATE TRIGGER increment_score
    AFTER INSERT ON question
    FOR EACH ROW EXECUTE FUNCTION increment_score();

DROP TRIGGER IF EXISTS increment_score ON answer CASCADE;
CREATE TRIGGER increment_score
    AFTER INSERT OR UPDATE ON answer
    FOR EACH ROW EXECUTE FUNCTION increment_score();

DROP TRIGGER IF EXISTS increment_score ON comment CASCADE;
CREATE TRIGGER increment_score
    AFTER INSERT ON comment
    FOR EACH ROW EXECUTE FUNCTION increment_score();


CREATE OR REPLACE FUNCTION was_edited() RETURNS TRIGGER AS
$FUNC8$
BEGIN
    IF TG_TABLE_NAME = 'answer' THEN
        IF OLD.full_text <> NEW.full_text THEN
            UPDATE answer SET was_edited = 'Yes' WHERE user_id = NEW.user_id;
        END IF;
    ELSIF TG_TABLE_NAME = 'question' THEN
        IF OLD.full_text <> NEW.full_text THEN
            UPDATE question SET was_edited = 'Yes' WHERE author_id = NEW.author_id;
        END IF;     
    END IF;
    RETURN NULL;
END
$FUNC8$
LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS was_edited ON answer CASCADE;
CREATE TRIGGER was_edited
    AFTER UPDATE ON answer
    FOR EACH ROW EXECUTE FUNCTION was_edited();

DROP TRIGGER IF EXISTS was_edited ON question CASCADE;
CREATE TRIGGER was_edited
    AFTER UPDATE ON question
    FOR EACH ROW EXECUTE FUNCTION was_edited();


--INDEXES

DROP INDEX IF EXISTS numVotesComment_idx CASCADE;
CREATE INDEX numVotesComment_idx ON comment USING btree(num_votes);
DROP INDEX IF EXISTS numVotesQuestion_idx CASCADE;
CREATE INDEX numVotesQuestion_idx ON comment USING btree(num_votes);
	
ALTER TABLE question
ADD COLUMN tsvectors TSVECTOR;


CREATE OR REPLACE FUNCTION question_search_update() RETURNS TRIGGER AS $$
BEGIN
 IF TG_OP = 'INSERT' THEN
        NEW.tsvectors = (
         setweight(to_tsvector('english', NEW.title), 'A') ||
         setweight(to_tsvector('english', NEW.full_text), 'B')
        );
 END IF;
 IF TG_OP = 'UPDATE' THEN
         IF (NEW.title <> OLD.title OR NEW.full_text <> OLD.full_text) THEN
           NEW.tsvectors = (
             setweight(to_tsvector('english', NEW.title), 'A') ||
             setweight(to_tsvector('english', NEW.full_text), 'B')
           );
         END IF;
 END IF;
 RETURN NEW;
END $$
LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS question_update ON question CASCADE;
CREATE TRIGGER question_update
 BEFORE INSERT OR UPDATE ON question
 FOR EACH ROW
 EXECUTE PROCEDURE question_search_update();

DROP INDEX IF EXISTS search_idx CASCADE;
CREATE INDEX search_idx ON question USING gist(tsvectors);


INSERT INTO topic(topic_id,topic_name,num_views) VALUES (1,'Programming',345);
INSERT INTO topic(topic_id,topic_name,num_views) VALUES (2,'Animals',172);
INSERT INTO topic(topic_id,topic_name,num_views) VALUES (3,'Birds',97);
INSERT INTO topic(topic_id,topic_name,num_views) VALUES (4,'Books',137);
INSERT INTO topic(topic_id,topic_name,num_views) VALUES (5,'Buildings',127);
INSERT INTO topic(topic_id,topic_name,num_views) VALUES (6,'Cars',125);
INSERT INTO topic(topic_id,topic_name,num_views) VALUES (7,'Celebrities',195);
INSERT INTO topic(topic_id,topic_name,num_views) VALUES (8,'Celebrations',71);
INSERT INTO topic(topic_id,topic_name,num_views) VALUES (9,'Cities',188);
INSERT INTO topic(topic_id,topic_name,num_views) VALUES (10,'Clothes',72);
INSERT INTO topic(topic_id,topic_name,num_views) VALUES (11,'Comic book',99);
INSERT INTO topic(topic_id,topic_name,num_views) VALUES (12,'Countries',127);
INSERT INTO topic(topic_id,topic_name,num_views) VALUES (13,'Currencies',145);
INSERT INTO topic(topic_id,topic_name,num_views) VALUES (14,'Diseases',83);
INSERT INTO topic(topic_id,topic_name,num_views) VALUES (15,'Drinks',179);
INSERT INTO topic(topic_id,topic_name,num_views) VALUES (16,'Electronic goods',63);
INSERT INTO topic(topic_id,topic_name,num_views) VALUES (17,'Movies',62);
INSERT INTO topic(topic_id,topic_name,num_views) VALUES (18,'Food',100);
INSERT INTO topic(topic_id,topic_name,num_views) VALUES (19,'Football',175);
INSERT INTO topic(topic_id,topic_name,num_views) VALUES (20,'Hobbies',138);
INSERT INTO topic(topic_id,topic_name,num_views) VALUES (21,'Jobs',116);
INSERT INTO topic(topic_id,topic_name,num_views) VALUES (22,'Languages',114);
INSERT INTO topic(topic_id,topic_name,num_views) VALUES (23,'Music',178);

INSERT INTO tag(tag_id,tag_name) VALUES (1,'javascript');
INSERT INTO tag(tag_id,tag_name) VALUES (2,'python');
INSERT INTO tag(tag_id,tag_name) VALUES (3,'java');
INSERT INTO tag(tag_id,tag_name) VALUES (4,'c#');
INSERT INTO tag(tag_id,tag_name) VALUES (5,'php');
INSERT INTO tag(tag_id,tag_name) VALUES (6,'android');
INSERT INTO tag(tag_id,tag_name) VALUES (7,'html');
INSERT INTO tag(tag_id,tag_name) VALUES (8,'jquery');
INSERT INTO tag(tag_id,tag_name) VALUES (9,'c++');
INSERT INTO tag(tag_id,tag_name) VALUES (10,'css');
INSERT INTO tag(tag_id,tag_name) VALUES (11,'ios');
INSERT INTO tag(tag_id,tag_name) VALUES (12,'mysql');
INSERT INTO tag(tag_id,tag_name) VALUES (13,'sql');
INSERT INTO tag(tag_id,tag_name) VALUES (14,'r');
INSERT INTO tag(tag_id,tag_name) VALUES (15,'node.js');
INSERT INTO tag(tag_id,tag_name) VALUES (16,'reactjs');
INSERT INTO tag(tag_id,tag_name) VALUES (17,'arrays');
INSERT INTO tag(tag_id,tag_name) VALUES (18,'c');
INSERT INTO tag(tag_id,tag_name) VALUES (19,'consectetur');
INSERT INTO tag(tag_id,tag_name) VALUES (20,'tempora');
INSERT INTO tag(tag_id,tag_name) VALUES (21,'quaerat');
INSERT INTO tag(tag_id,tag_name) VALUES (22,'neque');
INSERT INTO tag(tag_id,tag_name) VALUES (23,'labore');
INSERT INTO tag(tag_id,tag_name) VALUES (24,'quisquam');
INSERT INTO tag(tag_id,tag_name) VALUES (25,'lorem');
INSERT INTO tag(tag_id,tag_name) VALUES (26,'quisquamont');
INSERT INTO tag(tag_id,tag_name) VALUES (27,'modi');
INSERT INTO tag(tag_id,tag_name) VALUES (28,'aliquam');
INSERT INTO tag(tag_id,tag_name) VALUES (29,'velit');
INSERT INTO tag(tag_id,tag_name) VALUES (30,'magnam');
INSERT INTO tag(tag_id,tag_name) VALUES (31,'voluptatem');
INSERT INTO tag(tag_id,tag_name) VALUES (32,'quaeratus');
INSERT INTO tag(tag_id,tag_name) VALUES (33,'quaeratis');
INSERT INTO tag(tag_id,tag_name) VALUES (34,'dolore');
INSERT INTO tag(tag_id,tag_name) VALUES (35,'temporalius');
INSERT INTO tag(tag_id,tag_name) VALUES (36,'dolor');
INSERT INTO tag(tag_id,tag_name) VALUES (37,'etincidunt');
INSERT INTO tag(tag_id,tag_name) VALUES (38,'ipsum');
INSERT INTO tag(tag_id,tag_name) VALUES (39,'eius');
INSERT INTO tag(tag_id,tag_name) VALUES (40,'adipisci');
INSERT INTO tag(tag_id,tag_name) VALUES (41,'quisqumanos');
  
INSERT INTO badge (badge_id,badge_name)
VALUES
  (1, 'First question'),
  (2, 'First answer'),
  (3, 'Novice'),
  (4, 'Rising star'),
  (5, 'Expert'),
  (6, 'Master');

insert into users (user_id, username, email, name, password, score, is_moderator, is_admin) values (1, 'jimmypage', 'jimmy@gmail.com', 'Jimmy Page', '$2a$10$Mo8xt4RxzVLU3JYiX8Z/oekMnV4lBs4Ftk/1M183CbYHzul9nIw2G', 62, false, true);
insert into users (user_id, username, email, name, password, score, is_moderator, is_admin) values (2, 'dfincher', 'david@gmail.com', 'David Fincher', 'lbaw;2223', 127, true, false);
insert into users (user_id, username, email, name, password, score, is_moderator, is_admin) values (3, 'torvalds', 'linus@gmail.com', 'Linus Torvalds', 'lbaw;2233', 248, false, false);
insert into users (user_id, username, email, name, password, score, is_moderator, is_admin) values (4, 'gbertolaccini3', 'gbertolaccini3@xrea.com', 'Gilburt Bertolaccini', 'XT89prvEWa', 37, false, false);
insert into users (user_id, username, email, name, password, score, is_moderator, is_admin) values (5, 'mdirr4', 'mdirr4@photobucket.com', 'Marrilee Dirr', '58gPXs', 233, false, false);
insert into users (user_id, username, email, name, password, score, is_moderator, is_admin) values (6, 'rmerry5', 'rmerry5@unc.edu', 'Rainer Merry', 'KipwGTWz', 59, false, false);
insert into users (user_id, username, email, name, password, score, is_moderator, is_admin) values (7, 'gtheyer6', 'gtheyer6@clickbank.net', 'Gibb Theyer', 'lZcgphFLr', 181, false, false);
insert into users (user_id, username, email, name, password, score, is_moderator, is_admin) values (8, 'xcandlish7', 'xcandlish7@yellowbook.com', 'Xenos Candlish', 'b98b8HbM', 12, false, false);
insert into users (user_id, username, email, name, password, score, is_moderator, is_admin) values (9, 'eheight8', 'eheight8@bing.com', 'Eolanda Height', 'YJaYzjpg3dRP', 243, false, false);
insert into users (user_id, username, email, name, password, score, is_moderator, is_admin) values (10, 'bguttridge9', 'bguttridge9@vinaora.com', 'Bunni Guttridge', '2LgCIP4Ve', 124, false, false);
insert into users (user_id, username, email, name, password, score, is_moderator, is_admin) values (11, 'ibeartupa', 'ibeartupa@netscape.com', 'Ira Beartup', 'SIZpEo', 153, false, false);
insert into users (user_id, username, email, name, password, score, is_moderator, is_admin) values (12, 'mcallejab', 'mcallejab@pagesperso-orange.fr', 'Merilee Calleja', 'PDNywA2sCS', 103, false, false);
insert into users (user_id, username, email, name, password, score, is_moderator, is_admin) values (13, 'mshapterc', 'mshapterc@mapquest.com', 'Meggi Shapter', 'PAnB577xA', 182, false, false);
insert into users (user_id, username, email, name, password, score, is_moderator, is_admin) values (14, 'tgasconed', 'tgasconed@tuttocitta.it', 'Thayne Gascone', 'RIeLfTK1Axj', 101, false, false);
insert into users (user_id, username, email, name, password, score, is_moderator, is_admin) values (15, 'kpackhame', 'kpackhame@oracle.com', 'Kipp Packham', 'ejF1kJXS', 63, false, false);
insert into users (user_id, username, email, name, password, score, is_moderator, is_admin) values (16, 'mbedrosianf', 'mbedrosianf@google.de', 'Max Bedrosian', 'Y3LwxTa7rz9A', 67, false, false);
insert into users (user_id, username, email, name, password, score, is_moderator, is_admin) values (17, 'rmatschkeg', 'rmatschkeg@google.it', 'Robinette Matschke', 'AkAmfr', 9, false, false);
insert into users (user_id, username, email, name, password, score, is_moderator, is_admin) values (18, 'lbalchenh', 'lbalchenh@mapy.cz', 'Lanie Balchen', 'iAjghtEGmW', 253, false, false);
insert into users (user_id, username, email, name, password, score, is_moderator, is_admin) values (19, 'dtromani', 'dtromani@com.com', 'Dasi Troman', 'sbAoY81vTGn5', 112, false, false);
insert into users (user_id, username, email, name, password, score, is_moderator, is_admin) values (20, 'awattingj', 'awattingj@twitpic.com', 'Ayn Watting', 'xTUojEs', 137, false, false);
insert into users (user_id, username, email, name, password, score, is_moderator, is_admin) values (21, 'pmcphilemyk', 'pmcphilemyk@home.pl', 'Pamella McPhilemy', 'nTxuaX', 35, false, false);
insert into users (user_id, username, email, name, password, score, is_moderator, is_admin) values (22, 'scommonl', 'scommonl@amazon.com', 'Silvana Common', 'tEnbuXYtbvGL', 146, false, false);
insert into users (user_id, username, email, name, password, score, is_moderator, is_admin) values (23, 'rspaduzzam', 'rspaduzzam@oaic.gov.au', 'Rebe Spaduzza', '76dXqb', 57, false, false);
insert into users (user_id, username, email, name, password, score, is_moderator, is_admin) values (24, 'chayballn', 'chayballn@imgur.com', 'Corey Hayball', 'Z3SQrpPtl', 37, false, false);
insert into users (user_id, username, email, name, password, score, is_moderator, is_admin) values (25, 'cfrudeo', 'cfrudeo@time.com', 'Caz Frude', 'PxDYfqXe', 88, false, false);
insert into users (user_id, username, email, name, password, score, is_moderator, is_admin) values (26, 'rsimmonsp', 'rsimmonsp@about.com', 'Rozina Simmons', 'QdM9N3Ni', 123, false, false);
insert into users (user_id, username, email, name, password, score, is_moderator, is_admin) values (27, 'dtrevnaq', 'dtrevnaq@dagondesign.com', 'Dalt Trevna', 'n5r8U8', 179, false, false);
insert into users (user_id, username, email, name, password, score, is_moderator, is_admin) values (28, 'lhryniewiczr', 'lhryniewiczr@discovery.com', 'Lazaro Hryniewicz', 'Qf3YRI0glk', 74, false, false);
insert into users (user_id, username, email, name, password, score, is_moderator, is_admin) values (29, 'mshepherdsons', 'mshepherdsons@census.gov', 'Muriel Shepherdson', '3fEYLEfn8Yw', 49, false, false);
insert into users (user_id, username, email, name, password, score, is_moderator, is_admin) values (30, 'jbasellit', 'jbasellit@diigo.com', 'Jeannine Baselli', '8gl3KBL4xkk', 95, false, false);

insert into question (question_id, title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) values (1, 'How to center a div?', 'What method can I use to center a div horizontally to the middle of the screen?', 17, 282, 1, '2021-12-01 04:24:58', false, 1);
insert into question (question_id, title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) values (2, 'How do I add multi-line comments in Python?', '# works for single line comments but Im wondering how to comment multiple lines of code', 5, 100, 0, '2021-01-01 05:26:58', false, 1);
insert into question (question_id, title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) values (3, 'What is the difference between git pull and git fetch?', 'I don`t understand the difference between the two.', 96, 192, 1, '2022-01-19 04:52:59', false, 3);
insert into question (question_id, title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) values (4, 'fermentum donec ut mauris eget massa tempor convallis nulla', 'eu orci mauris lacinia sapien quis libero nullam sit amet', 71, 242, 1, '2022-04-14 23:56:49', false, 20);
insert into question (question_id, title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) values (5, 'justo in blandit ultrices enim lorem ipsum dolor sit', 'sit amet lobortis sapien sapien non mi integer ac neque duis bibendum morbi non quam nec dui luctus rutrum', 1, 75, 1, '2022-06-08 01:57:24', false, 3);
insert into question (question_id, title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) values (6, 'lorem id ligula suspendisse ornare', 'ligula vehicula consequat morbi a ipsum integer a nibh in quis justo maecenas rhoncus aliquam lacus morbi quis tortor id nulla', 55, 6, 1, '2022-01-05 07:56:14', false, 7);
insert into question (question_id, title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) values (7, 'lobortis ligula sit amet eleifend pede', 'nunc viverra dapibus nulla suscipit ligula in lacus curabitur at ipsum ac tellus semper interdum', 93, 151, 1, '2021-11-18 00:14:50', true, 12);
insert into question (question_id, title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) values (8, 'eu massa donec dapibus duis', 'ac enim in tempor turpis nec euismod scelerisque quam turpis adipiscing lorem vitae mattis nibh ligula nec sem duis', 79, 196, 1, '2022-02-10 15:17:03', true, 5);
insert into question (question_id, title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) values (9, 'ac consequat metus sapien ut nunc vestibulum ante ipsum', 'duis aliquam convallis nunc proin at turpis a pede posuere nonummy integer non velit donec diam neque vestibulum eget vulputate ut ultrices vel augue vestibulum ante ipsum primis', 33, 199, 1, '2021-12-08 11:28:46', false, 5);
insert into question (question_id, title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) values (10, 'maecenas pulvinar lobortis est phasellus sit amet erat nulla tempus', 'magna vulputate luctus cum sociis natoque penatibus et magnis dis parturient montes nascetur ridiculus mus vivamus vestibulum', 61, 177, 1, '2022-01-11 19:38:34', false, 8);
insert into question (question_id, title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) values (11, 'lectus pellentesque eget nunc', 'amet consectetuer adipiscing elit proin interdum mauris non ligula pellentesque ultrices phasellus id sapien in sapien iaculis congue vivamus metus arcu adipiscing', 11, 132, 1, '2021-12-11 07:13:28', true, 3);
insert into question (question_id, title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) values (12, 'eros suspendisse accumsan tortor quis turpis sed ante vivamus tortor', 'quisque erat eros viverra eget congue eget semper rutrum nulla nunc purus phasellus in felis donec semper sapien a libero nam dui proin', 89, 52, 1, '2022-10-03 13:01:49', false, 16);
insert into question (question_id, title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) values (13, 'in hac habitasse platea dictumst etiam', 'faucibus accumsan odio curabitur convallis duis consequat dui nec nisi', 73, 261, 1, '2021-12-12 01:42:34', false, 6);
insert into question (question_id, title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) values (14, 'ultrices mattis odio donec vitae nisi nam', 'sapien cursus vestibulum proin eu mi nulla ac enim in tempor turpis nec euismod scelerisque quam turpis adipiscing', 85, 202, 1, '2022-01-16 01:16:00', false, 11);
insert into question (question_id, title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) values (15, 'luctus nec molestie sed justo', 'cras non velit nec nisi vulputate nonummy maecenas tincidunt lacus at velit vivamus vel nulla eget eros elementum pellentesque quisque porta volutpat erat quisque erat eros viverra eget congue', 25, 57, 1, '2022-01-08 10:02:51', false, 8);
insert into question (question_id, title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) values (16, 'tincidunt eu felis fusce posuere felis sed lacus morbi sem', 'cubilia curae donec pharetra magna vestibulum aliquet ultrices erat tortor sollicitudin mi sit amet lobortis sapien sapien non mi integer ac', 96, 142, 1, '2022-04-15 04:36:09', true, 15);
insert into question (question_id, title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) values (17, 'aliquam non mauris morbi non lectus', 'etiam vel augue vestibulum rutrum rutrum neque aenean auctor gravida sem praesent id massa id nisl venenatis lacinia aenean sit amet justo morbi ut', 35, 205, 1, '2022-05-18 17:07:00', true, 5);
insert into question (question_id, title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) values (18, 'donec dapibus duis at', 'massa tempor convallis nulla neque libero convallis eget eleifend luctus ultricies eu nibh quisque id justo sit amet sapien dignissim vestibulum vestibulum ante ipsum primis in', 42, 75, 1, '2022-01-14 12:32:50', true, 14);
insert into question (question_id, title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) values (19, 'erat tortor sollicitudin mi sit amet lobortis sapien sapien', 'blandit non interdum in ante vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae duis faucibus', 44, 239, 1, '2022-02-03 03:32:17', true, 1);
insert into question (question_id, title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) values (20, 'sodales scelerisque mauris sit amet eros suspendisse', 'sapien sapien non mi integer ac neque duis bibendum morbi non quam nec dui luctus rutrum', 9, 110, 1, '2022-02-12 14:11:22', true, 15);
insert into question (question_id, title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) values (21, 'pellentesque quisque porta volutpat erat quisque erat', 'curabitur convallis duis consequat dui nec nisi volutpat eleifend donec ut dolor morbi vel lectus in quam fringilla rhoncus mauris', 51, 243, 1, '2022-04-28 12:06:26', false, 3);
insert into question (question_id, title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) values (22, 'mauris non ligula pellentesque ultrices phasellus id sapien in', 'turpis eget elit sodales scelerisque mauris sit amet eros suspendisse accumsan tortor quis turpis sed ante vivamus tortor duis mattis egestas metus aenean fermentum donec ut', 32, 256, 1, '2022-07-21 06:49:22', true, 9);
insert into question (question_id, title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) values (23, 'imperdiet sapien urna pretium nisl ut', 'molestie nibh in lectus pellentesque at nulla suspendisse potenti cras in purus eu magna vulputate', 35, 284, 1, '2022-09-07 05:57:16', true, 15);
insert into question (question_id, title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) values (24, 'habitasse platea dictumst etiam faucibus cursus urna ut tellus', 'platea dictumst maecenas ut massa quis augue luctus tincidunt nulla mollis molestie lorem quisque ut erat curabitur gravida nisi at nibh in hac habitasse platea dictumst aliquam', 98, 116, 1, '2021-11-25 00:25:38', false, 2);
insert into question (question_id, title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) values (25, 'amet turpis elementum ligula vehicula consequat morbi', 'nisi vulputate nonummy maecenas tincidunt lacus at velit vivamus vel', 41, 216, 1, '2021-11-18 23:56:25', false, 4);
insert into question (question_id, title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) values (26, 'porttitor lorem id ligula suspendisse ornare consequat lectus in est', 'primis in faucibus orci luctus et ultrices posuere cubilia curae nulla dapibus dolor vel est donec odio justo sollicitudin ut suscipit a feugiat et eros vestibulum ac est lacinia', 96, 172, 1, '2022-09-02 19:04:52', false, 16);
insert into question (question_id, title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) values (27, 'tristique est et tempus semper est quam pharetra magna ac', 'varius nulla facilisi cras non velit nec nisi vulputate nonummy maecenas tincidunt lacus at velit vivamus vel nulla', 9, 102, 1, '2021-11-19 00:10:39', true, 5);
insert into question (question_id, title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) values (28, 'adipiscing elit proin risus praesent lectus vestibulum quam sapien', 'est phasellus sit amet erat nulla tempus vivamus in felis eu sapien cursus vestibulum proin eu', 42, 173, 1, '2022-07-26 03:19:55', false, 8);
insert into question (question_id, title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) values (29, 'non mauris morbi non lectus aliquam sit amet', 'id ornare imperdiet sapien urna pretium nisl ut volutpat sapien arcu', 85, 201, 1, '2022-01-11 10:24:07', false, 12);
insert into question (question_id, title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) values (30, 'lacus at turpis donec posuere metus vitae ipsum', 'est phasellus sit amet erat nulla tempus vivamus in felis', 6, 192, 1, '2022-06-29 22:50:36', false, 7);

insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (1, 'You can use the `text-align` property with the value `center`.', 31, true, false, '2022-05-18 00:01:14', 1, 2);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (2, 'Best way to do this is to use the `margin` property and set it to `0 auto`. This will make the horizontal margin equally divided', 41, true, false, '2021-11-08 13:01:18', 1, 3);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (3, 'git fetch is similar to pull but doesnt merge. i.e. it fetches remote updates but your local stays the same', 41, true, false, '2022-02-22 09:48:22', 2, 2);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (4, 'eget congue eget semper rutrum nulla nunc purus phasellus in felis donec semper sapien a libero nam dui proin', 39, true, true, '2022-04-15 18:47:13', 12, 26);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (5, 'rutrum neque aenean auctor gravida sem praesent id massa id nisl venenatis', 30, true, false, '2021-12-15 03:02:19', 30, 1);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (6, 'risus auctor sed tristique in tempus sit amet sem fusce consequat nulla', 41, true, false, '2022-06-01 14:27:04', 11, 13);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (7, 'vitae quam suspendisse potenti nullam porttitor lacus at turpis donec posuere metus vitae ipsum', 42, true, true, '2022-08-12 16:38:46', 26, 5);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (8, 'et magnis dis parturient montes nascetur ridiculus mus etiam vel augue vestibulum rutrum', 11, true, false, '2022-06-09 02:08:26', 14, 24);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (9, 'donec pharetra magna vestibulum aliquet ultrices erat tortor sollicitudin mi', 39, false, true, '2022-02-05 17:36:00', 10, 18);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (10, 'eros suspendisse accumsan tortor quis turpis sed ante vivamus tortor duis mattis egestas metus', 29, true, false, '2022-08-24 06:09:49', 14, 20);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (11, 'orci luctus et ultrices posuere cubilia curae donec pharetra magna vestibulum aliquet ultrices', 43, true, false, '2022-08-14 07:25:26', 12, 17);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (12, 'morbi non lectus aliquam sit amet diam in magna bibendum imperdiet nullam orci pede venenatis non sodales', 46, true, true, '2022-03-28 18:23:14', 9, 22);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (13, 'quam sapien varius ut blandit non interdum in ante vestibulum ante ipsum primis in', 15, true, false, '2022-03-26 22:06:15', 15, 13);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (14, 'interdum in ante vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae duis faucibus accumsan', 48, true, false, '2021-11-05 00:18:53', 20, 5);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (15, 'proin eu mi nulla ac enim in tempor turpis nec euismod scelerisque quam turpis adipiscing lorem', 44, false, true, '2022-07-22 22:14:34', 26, 4);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (16, 'mauris vulputate elementum nullam varius nulla facilisi cras non velit', 33, true, false, '2022-06-10 15:31:02', 3, 8);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (17, 'diam in magna bibendum imperdiet nullam orci pede venenatis non sodales sed tincidunt eu felis fusce', 34, false, true, '2022-02-19 01:12:48', 19, 1);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (18, 'urna pretium nisl ut volutpat sapien arcu sed augue aliquam erat volutpat in congue', 41, true, true, '2022-07-13 01:17:03', 15, 6);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (19, 'blandit mi in porttitor pede justo eu massa donec dapibus duis at velit eu est congue', 27, false, true, '2022-04-14 14:31:44', 11, 14);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (20, 'scelerisque mauris sit amet eros suspendisse accumsan tortor quis turpis sed ante vivamus', 42, false, false, '2021-11-10 14:09:42', 24, 23);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (21, 'facilisi cras non velit nec nisi vulputate nonummy maecenas tincidunt lacus at velit vivamus vel nulla eget eros', 5, false, false, '2021-11-21 15:19:35', 29, 2);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (22, 'orci nullam molestie nibh in lectus pellentesque at nulla suspendisse potenti cras in purus eu magna vulputate luctus cum', 47, true, false, '2022-06-09 12:16:01', 24, 18);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (23, 'laoreet ut rhoncus aliquet pulvinar sed nisl nunc rhoncus dui vel sem sed sagittis nam', 12, true, false,'2022-01-25 08:59:07', 17, 1);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (24, 'feugiat et eros vestibulum ac est lacinia nisi venenatis tristique fusce congue diam id ornare imperdiet sapien urna pretium', 39, true, false,'2022-06-18 23:26:06', 26, 17);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (25, 'ultricies eu nibh quisque id justo sit amet sapien dignissim vestibulum vestibulum ante', 43, true, false, '2022-09-10 11:06:45', 28, 13);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (26, 'penatibus et magnis dis parturient montes nascetur ridiculus mus etiam', 48, false, true, '2021-11-28 05:13:41', 26, 9);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (27, 'nulla ultrices aliquet maecenas leo odio condimentum id luctus nec molestie sed', 50, false, false, '2022-03-11 20:36:00', 6, 22);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (28, 'at vulputate vitae nisl aenean lectus pellentesque eget nunc donec quis orci', 23, false, true, '2022-09-07 07:06:28', 18, 18);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (29, 'eget eros elementum pellentesque quisque porta volutpat erat quisque erat eros', 19, true, true, '2021-12-29 16:25:42', 14, 6);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (30, 'faucibus accumsan odio curabitur convallis duis consequat dui nec nisi volutpat eleifend donec ut dolor morbi vel', 8, false, true, '2022-07-21 03:16:56', 1, 10);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (31, 'adipiscing elit proin risus praesent lectus vestibulum quam sapien varius ut blandit non interdum in', 13, false, false, '2022-10-19 18:51:57', 3, 28);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (32, 'bibendum felis sed interdum venenatis turpis enim blandit mi in porttitor pede justo eu', 45, false, false, '2021-12-07 19:23:05', 14, 6);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (33, 'quis turpis eget elit sodales scelerisque mauris sit amet eros suspendisse accumsan tortor', 3, false, false, '2022-08-08 03:31:11', 18, 18);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (34, 'fermentum justo nec condimentum neque sapien placerat ante nulla justo aliquam quis turpis eget elit sodales', 25, true, true, '2022-09-19 12:57:46', 27, 19);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (35, 'eleifend luctus ultricies eu nibh quisque id justo sit amet sapien dignissim vestibulum', 43, false, true, '2022-08-15 05:16:48', 25, 8);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (36, 'maecenas tincidunt lacus at velit vivamus vel nulla eget eros elementum', 1, false, true, '2022-01-26 07:51:04', 20, 7);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (37, 'vivamus metus arcu adipiscing molestie hendrerit at vulputate vitae nisl aenean lectus pellentesque eget nunc donec quis orci eget orci', 1, true, false, '2022-06-22 00:38:12', 29, 4);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (38, 'eros viverra eget congue eget semper rutrum nulla nunc purus phasellus in felis donec semper sapien a libero nam dui', 30, false, false, '2021-12-23 03:31:36', 18, 30);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (39, 'sed justo pellentesque viverra pede ac diam cras pellentesque volutpat dui maecenas tristique est et', 45, true, false,'2022-07-21 01:48:54', 7, 23);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (40, 'tortor id nulla ultrices aliquet maecenas leo odio condimentum id luctus nec molestie', 46, true, false, '2022-03-17 18:04:22', 18, 10);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (41, 'tempus vivamus in felis eu sapien cursus vestibulum proin eu mi', 20, false, false, '2022-08-12 10:53:25', 9, 11);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (42, 'mattis egestas metus aenean fermentum donec ut mauris eget massa tempor convallis nulla neque libero convallis eget eleifend luctus ultricies', 39, true, false, '2022-02-26 16:26:20', 22, 29);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (43, 'duis bibendum morbi non quam nec dui luctus rutrum nulla tellus in sagittis dui vel nisl duis ac nibh fusce', 35, false, false, '2021-12-10 02:44:32', 23, 18);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (44, 'vestibulum rutrum rutrum neque aenean auctor gravida sem praesent id', 22, true, true, '2022-04-14 11:06:28', 17, 8);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (45, 'non mi integer ac neque duis bibendum morbi non quam nec dui luctus rutrum nulla tellus in sagittis dui vel', 10, false, false, '2022-01-01 10:26:37', 14, 23);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (46, 'urna ut tellus nulla ut erat id mauris vulputate elementum nullam varius nulla', 2, true, true, '2022-07-27 04:03:32', 7, 19);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (47, 'justo maecenas rhoncus aliquam lacus morbi quis tortor id nulla ultrices', 38, false, true, '2022-04-11 03:13:53', 6, 7);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (48, 'vulputate ut ultrices vel augue vestibulum ante ipsum primis in faucibus orci', 17, true, false, '2022-02-14 18:45:51', 20, 18);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (49, 'at velit eu est congue elementum in hac habitasse platea dictumst morbi', 6, false, true, '2022-08-31 06:54:31', 3, 22);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (50, 'rhoncus mauris enim leo rhoncus sed vestibulum sit amet cursus id turpis integer aliquet massa id', 23, false, false, '2022-07-04 01:16:05', 18, 25);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (51, 'accumsan odio curabitur convallis duis consequat dui nec nisi volutpat eleifend donec ut dolor morbi vel', 37, false, true, '2022-01-07 13:20:34', 28, 9);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (52, 'ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae mauris viverra', 31, false, true, '2022-08-25 20:20:25', 25, 15);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (53, 'blandit non interdum in ante vestibulum ante ipsum primis in faucibus', 36, true, false, '2021-12-29 07:02:25', 4, 25);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (54, 'purus aliquet at feugiat non pretium quis lectus suspendisse potenti in eleifend quam a odio in hac habitasse platea', 31, false, true, '2022-06-02 05:30:10', 26, 14);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (55, 'risus semper porta volutpat quam pede lobortis ligula sit amet eleifend pede libero quis orci nullam molestie nibh', 21, false, false, '2022-05-11 17:14:33', 2, 3);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (56, 'sagittis dui vel nisl duis ac nibh fusce lacus purus aliquet at feugiat', 37, false, true, '2022-08-22 17:52:06', 30, 6);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (57, 'nulla elit ac nulla sed vel enim sit amet nunc viverra dapibus nulla', 15, false, false, '2022-06-09 20:34:55', 29, 8);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (58, 'amet lobortis sapien sapien non mi integer ac neque duis bibendum morbi non', 2, true, false, '2022-02-05 13:10:19', 26, 10);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (59, 'posuere cubilia curae donec pharetra magna vestibulum aliquet ultrices erat tortor sollicitudin mi sit amet lobortis', 44, false, true, '2022-05-12 11:45:34', 10, 16);
insert into answer (answer_id, full_text, num_votes, is_correct, was_edited, date, question_id, user_id) values (60, 'sem praesent id massa id nisl venenatis lacinia aenean sit amet justo morbi ut', 3, false, true, '2022-09-28 16:09:01', 4, 6);

INSERT INTO notification (notification_id,notification_text,date,viewed,user_id)
VALUES
  (1,'New answers to your question','May 25, 2022','No',1),
  (2,'New vote on your comment','Mar 4, 2022','No',2),
  (3,'Your answer was marked as correct','Aug 4, 2022','No',3),
  (4,'New answers to your question','Apr 26, 2022','No',4),
  (5,'You received a badge','Jun 1, 2022','No',5);

insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (1, 'Altough it might work, this should only be used with text.', 24, '2022-08-29 09:49:24',30, 1, 3);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (2, 'Awesome! This worked just fine.', 48, '2022-09-15 22:50:46',29, 2, 1);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (3, 'You probably want to skip the pull and just do a "git rebase origin" as the last step since you already fetched the changes.', 43, '2021-10-26 06:24:38',28, 3, 1);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (4, 'in magna bibendum imperdiet nullam orci pede venenatis non sodales sed tincidunt eu felis', 19, '2022-05-13 23:57:52',27, 52, 27);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (5, 'nisi vulputate nonummy maecenas tincidunt lacus at velit vivamus vel nulla eget eros elementum pellentesque quisque', 24, '2022-07-26 08:48:54',26, 50, 12);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (6, 'magna at nunc commodo placerat praesent blandit nam nulla integer pede justo lacinia eget', 31, '2022-05-13 14:47:21',25, 48, 2);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (7, 'quisque erat eros viverra eget congue eget semper rutrum nulla nunc', 18, '2022-06-19 14:43:15',24, 51, 11);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (8, 'nisi venenatis tristique fusce congue diam id ornare imperdiet sapien urna pretium nisl ut', 3, '2021-12-26 08:05:08',23, 26, 7);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (9, 'nibh fusce lacus purus aliquet at feugiat non pretium quis lectus suspendisse potenti', 36, '2022-08-30 19:39:37',22, 4, 27);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (10, 'sapien non mi integer ac neque duis bibendum morbi non quam nec dui luctus rutrum nulla tellus', 16, '2022-03-15 21:36:19',21, 21, 12);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (11, 'viverra eget congue eget semper rutrum nulla nunc purus phasellus in felis donec semper sapien a libero nam', 27, '2022-06-19 17:25:50',20, 26, 27);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (12, 'amet consectetuer adipiscing elit proin risus praesent lectus vestibulum quam sapien varius', 43, '2022-06-09 15:32:10',19, 26, 2);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (13, 'posuere cubilia curae donec pharetra magna vestibulum aliquet ultrices erat', 48, '2022-01-22 04:34:43',23, 52, 12);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (14, 'curabitur convallis duis consequat dui nec nisi volutpat eleifend donec ut dolor morbi vel lectus in quam fringilla', 7, '2021-12-18 22:08:37',18, 1, 1);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (15, 'vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae mauris viverra diam vitae quam suspendisse potenti', 35, '2021-11-23 01:59:06',17, 13, 10);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (16, 'lorem vitae mattis nibh ligula nec sem duis aliquam convallis nunc proin at turpis', 22, '2022-10-03 16:12:43',17, 28, 26);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (17, 'orci luctus et ultrices posuere cubilia curae donec pharetra magna vestibulum', 23, '2021-10-23 13:09:07',5, 11, 1);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (18, 'scelerisque mauris sit amet eros suspendisse accumsan tortor quis turpis sed ante vivamus tortor duis', 6, '2022-10-09 17:34:55',16, 34, 4);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (19, 'aenean sit amet justo morbi ut odio cras mi pede malesuada in', 36, '2021-12-26 06:55:46',14, 21, 6);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (20, 'est lacinia nisi venenatis tristique fusce congue diam id ornare', 5, '2022-10-18 12:57:38',15, 30, 1);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (21, 'eget vulputate ut ultrices vel augue vestibulum ante ipsum primis in faucibus orci luctus et ultrices', 30, '2021-11-07 20:16:13',15, 40, 10);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (22, 'sed vestibulum sit amet cursus id turpis integer aliquet massa id lobortis convallis tortor risus dapibus augue vel', 35, '2022-08-24 11:39:41',14, 35, 7);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (23, 'lorem quisque ut erat curabitur gravida nisi at nibh in hac habitasse', 30, '2021-11-01 03:20:41',18, 9, 9);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (24, 'felis eu sapien cursus vestibulum proin eu mi nulla ac', 27, '2022-04-22 09:34:25',19, 30, 18);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (25, 'ante vel ipsum praesent blandit lacinia erat vestibulum sed magna at nunc commodo placerat praesent blandit nam nulla integer pede', 29, '2021-12-05 19:20:34',13, 54, 12);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (26, 'dui luctus rutrum nulla tellus in sagittis dui vel nisl duis ac nibh fusce lacus purus aliquet at', 48, '2022-06-02 13:13:36',12, 46, 2);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (27, 'sed nisl nunc rhoncus dui vel sem sed sagittis nam congue risus semper porta volutpat quam pede lobortis', 3, '2022-10-01 13:55:50',11, 38, 27);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (28, 'in felis eu sapien cursus vestibulum proin eu mi nulla', 20, '2022-04-28 18:37:38',30, 11, 22);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (29, 'integer a nibh in quis justo maecenas rhoncus aliquam lacus morbi quis', 40, '2022-06-16 03:33:54',10, 34, 29);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (30, 'in tempus sit amet sem fusce consequat nulla nisl nunc nisl duis bibendum', 27, '2022-08-13 22:34:53',9, 16, 26);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (31, 'neque duis bibendum morbi non quam nec dui luctus rutrum nulla tellus in sagittis dui vel nisl duis ac nibh', 28, '2022-03-12 14:07:37',8, 45, 6);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (32, 'mi pede malesuada in imperdiet et commodo vulputate justo in blandit ultrices enim lorem ipsum dolor sit amet consectetuer adipiscing', 10, '2021-11-27 19:41:56',7, 46, 27);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (33, 'sed nisl nunc rhoncus dui vel sem sed sagittis nam congue risus semper porta volutpat quam pede lobortis', 24, '2022-03-03 13:51:57',6, 8, 17);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (34, 'tellus nisi eu orci mauris lacinia sapien quis libero nullam sit amet turpis elementum ligula vehicula consequat', 1, '2022-01-13 20:28:22',5, 20, 10);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (35, 'nisl duis bibendum felis sed interdum venenatis turpis enim blandit mi in porttitor pede justo eu', 41, '2022-07-15 07:33:00',4, 56, 30);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (36, 'porta volutpat quam pede lobortis ligula sit amet eleifend pede libero', 13, '2022-08-24 22:12:04',7, 41, 14);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (37, 'eget tempus vel pede morbi porttitor lorem id ligula suspendisse ornare consequat lectus in est risus', 32, '2022-04-18 19:09:49',3, 39, 7);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (38, 'odio odio elementum eu interdum eu tincidunt in leo maecenas pulvinar lobortis est phasellus sit', 26, '2021-11-13 21:27:31',2, 48, 22);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (39, 'nec molestie sed justo pellentesque viverra pede ac diam cras', 33, '2022-07-13 21:24:18',12, 36, 25);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (40, 'nulla mollis molestie lorem quisque ut erat curabitur gravida nisi', 30, '2021-11-11 11:18:43',1, 40, 2);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (41, 'amet sem fusce consequat nulla nisl nunc nisl duis bibendum felis sed interdum venenatis turpis enim', 8, '2022-09-30 21:04:43',1, 52, 8);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (42, 'luctus ultricies eu nibh quisque id justo sit amet sapien dignissim vestibulum', 50, '2021-10-29 04:24:32',2, 56, 21);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (43, 'nec nisi vulputate nonummy maecenas tincidunt lacus at velit vivamus vel nulla eget eros elementum pellentesque quisque porta volutpat erat', 13, '2022-10-08 03:40:53',3, 59, 8);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (44, 'purus aliquet at feugiat non pretium quis lectus suspendisse potenti in eleifend quam a odio in hac habitasse platea dictumst', 18, '2022-09-27 22:19:34',4, 59, 23);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (45, 'a feugiat et eros vestibulum ac est lacinia nisi venenatis tristique fusce congue diam id ornare imperdiet sapien', 44, '2022-07-10 01:04:11',5, 11, 6);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (46, 'lacus morbi sem mauris laoreet ut rhoncus aliquet pulvinar sed nisl nunc', 37, '2022-05-21 09:37:44',6, 31, 25);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (47, 'est donec odio justo sollicitudin ut suscipit a feugiat et', 1, '2022-09-11 17:17:15',7, 59, 8);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (48, 'mauris vulputate elementum nullam varius nulla facilisi cras non velit nec nisi vulputate nonummy', 22, '2022-04-16 21:38:30',8, 31, 20);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (49, 'pellentesque volutpat dui maecenas tristique est et tempus semper est', 41, '2021-12-12 12:23:19',9, 2, 1);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (50, 'potenti in eleifend quam a odio in hac habitasse platea dictumst maecenas ut massa quis augue luctus', 33, '2022-02-03 07:32:11',10, 39, 29);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (51, 'lacinia eget tincidunt eget tempus vel pede morbi porttitor lorem id', 42, '2021-11-27 01:18:51',11, 3, 24);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (52, 'tempus sit amet sem fusce consequat nulla nisl nunc nisl duis bibendum felis sed interdum venenatis', 27, '2022-03-04 16:30:59',12, 57, 18);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (53, 'tellus in sagittis dui vel nisl duis ac nibh fusce lacus purus aliquet at', 39, '2022-08-27 04:38:27',13, 50, 9);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (54, 'quam suspendisse potenti nullam porttitor lacus at turpis donec posuere metus vitae', 50, '2021-10-25 01:20:00',14, 3, 17);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (55, 'iaculis justo in hac habitasse platea dictumst etiam faucibus cursus urna ut tellus nulla ut', 11, '2022-10-11 06:41:39',14, 24, 21);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (56, 'eget eros elementum pellentesque quisque porta volutpat erat quisque erat', 46, '2022-07-20 16:07:09',19, 13, 1);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (57, 'quisque id justo sit amet sapien dignissim vestibulum vestibulum ante ipsum primis in faucibus', 22, '2022-10-20 19:33:37',15, 51, 30);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (58, 'in eleifend quam a odio in hac habitasse platea dictumst maecenas', 1, '2021-12-15 14:21:25',16, 36, 12);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (59, 'condimentum neque sapien placerat ante nulla justo aliquam quis turpis eget elit', 5, '2022-04-18 11:41:13',17, 44, 8);
insert into comment (comment_id, full_text, num_votes, date, question_id, answer_id, user_id) values (60, 'dui vel nisl duis ac nibh fusce lacus purus aliquet at feugiat non pretium quis', 49, '2022-06-30 22:51:50',18, 23, 11);

INSERT INTO report (report_id,reason,date,question_id,answer_id,comment_id)
VALUES
  (1,'Spam','Dec 30, 2021',1,NULL,NULL),
  (2,'Hate speech','Mar 23, 2022',2,2,NULL),
  (3,'Harassment','May 26, 2022',3,3,3),
  (4,'blandit. Nam nulla magna, malesuada vel, convallis','Nov 16, 2021',4,NULL,NULL),
  (5,'sit amet, consectetuer adipiscing elit. Etiam laoreet,','Dec 3, 2021',5,5,5);

insert into topic_tag (topic_id, tag_id) values (1, 1);
insert into topic_tag (topic_id, tag_id) values (1, 2);
insert into topic_tag (topic_id, tag_id) values (1, 3);
insert into topic_tag (topic_id, tag_id) values (1, 4);
insert into topic_tag (topic_id, tag_id) values (1, 5);
insert into topic_tag (topic_id, tag_id) values (1, 6);
insert into topic_tag (topic_id, tag_id) values (1, 7);
insert into topic_tag (topic_id, tag_id) values (1, 8);
insert into topic_tag (topic_id, tag_id) values (1, 9);
insert into topic_tag (topic_id, tag_id) values (1, 10);
insert into topic_tag (topic_id, tag_id) values (1, 11);
insert into topic_tag (topic_id, tag_id) values (1, 12);
insert into topic_tag (topic_id, tag_id) values (1, 13);
insert into topic_tag (topic_id, tag_id) values (1, 14);
insert into topic_tag (topic_id, tag_id) values (1, 15);
insert into topic_tag (topic_id, tag_id) values (1, 16);
insert into topic_tag (topic_id, tag_id) values (1, 17);
insert into topic_tag (topic_id, tag_id) values (1, 18);
INSERT INTO topic_tag (topic_id, tag_id) VALUES (2,19);
INSERT INTO topic_tag (topic_id,tag_id) VALUES (3,20);
INSERT INTO topic_tag (topic_id,tag_id) VALUES (4,21);
INSERT INTO topic_tag (topic_id,tag_id) VALUES (5,22);
INSERT INTO topic_tag (topic_id,tag_id) VALUES (6,23);
INSERT INTO topic_tag (topic_id,tag_id) VALUES (7,24);
INSERT INTO topic_tag (topic_id,tag_id) VALUES (8,25);
INSERT INTO topic_tag (topic_id,tag_id) VALUES (9,26);
INSERT INTO topic_tag (topic_id,tag_id) VALUES (10,27);
INSERT INTO topic_tag (topic_id,tag_id) VALUES (11,28);
INSERT INTO topic_tag (topic_id,tag_id) VALUES (12,29);
INSERT INTO topic_tag (topic_id,tag_id) VALUES (13,30);
INSERT INTO topic_tag (topic_id,tag_id) VALUES (14,31);
INSERT INTO topic_tag (topic_id,tag_id) VALUES (15,32);
INSERT INTO topic_tag (topic_id,tag_id) VALUES (16,33);
INSERT INTO topic_tag (topic_id,tag_id) VALUES (17,34);
INSERT INTO topic_tag (topic_id,tag_id) VALUES (18,35);
INSERT INTO topic_tag (topic_id,tag_id) VALUES (19,36);
INSERT INTO topic_tag (topic_id,tag_id) VALUES (20,37);
INSERT INTO topic_tag (topic_id,tag_id) VALUES (21,38);
INSERT INTO topic_tag (topic_id,tag_id) VALUES (22,39);
INSERT INTO topic_tag (topic_id,tag_id) VALUES (23,40);

insert into user_tag (user_id, tag_id) values (3, 19);
insert into user_tag (user_id, tag_id) values (25, 34);
insert into user_tag (user_id, tag_id) values (9, 10);
insert into user_tag (user_id, tag_id) values (22, 35);
insert into user_tag (user_id, tag_id) values (13, 10);
insert into user_tag (user_id, tag_id) values (22, 25);
insert into user_tag (user_id, tag_id) values (18, 7);
insert into user_tag (user_id, tag_id) values (6, 32);
insert into user_tag (user_id, tag_id) values (18, 8);
insert into user_tag (user_id, tag_id) values (5, 28);
insert into user_tag (user_id, tag_id) values (20, 37);
insert into user_tag (user_id, tag_id) values (13, 34);
insert into user_tag (user_id, tag_id) values (27, 5);
insert into user_tag (user_id, tag_id) values (26, 40);
insert into user_tag (user_id, tag_id) values (10, 10);


-- USER_BADGE TABLE IS POPULATED BY USING A TRIGGER

insert into user_badge_support (user_who_supports, user_who_achieves, badge_id) values (19, 1, 3);
insert into user_badge_support (user_who_supports, user_who_achieves, badge_id) values (17, 2, 3);
insert into user_badge_support (user_who_supports, user_who_achieves, badge_id) values (7, 3, 3);
insert into user_badge_support (user_who_supports, user_who_achieves, badge_id) values (6, 1, 1);
insert into user_badge_support (user_who_supports, user_who_achieves, badge_id) values (1, 1, 4);
insert into user_badge_support (user_who_supports, user_who_achieves, badge_id) values (8, 20, 1);
insert into user_badge_support (user_who_supports, user_who_achieves, badge_id) values (24, 3, 1);
insert into user_badge_support (user_who_supports, user_who_achieves, badge_id) values (7, 16, 4);
insert into user_badge_support (user_who_supports, user_who_achieves, badge_id) values (17, 16, 4);
insert into user_badge_support (user_who_supports, user_who_achieves, badge_id) values (12, 16, 4);

insert into question_tag (question_id, tag_id) values (17, 8);
insert into question_tag (question_id, tag_id) values (2, 37);
insert into question_tag (question_id, tag_id) values (15, 32);
insert into question_tag (question_id, tag_id) values (9, 12);
insert into question_tag (question_id, tag_id) values (30, 30);
insert into question_tag (question_id, tag_id) values (30, 41);
insert into question_tag (question_id, tag_id) values (25, 27);
insert into question_tag (question_id, tag_id) values (26, 12);
insert into question_tag (question_id, tag_id) values (5, 8);
insert into question_tag (question_id, tag_id) values (2, 20);
insert into question_tag (question_id, tag_id) values (6, 33);
insert into question_tag (question_id, tag_id) values (16, 41);
insert into question_tag (question_id, tag_id) values (5, 27);
insert into question_tag (question_id, tag_id) values (27, 23);
insert into question_tag (question_id, tag_id) values (9, 14);
insert into question_tag (question_id, tag_id) values (28, 7);
insert into question_tag (question_id, tag_id) values (17, 19);
insert into question_tag (question_id, tag_id) values (3, 4);
insert into question_tag (question_id, tag_id) values (7, 1);
insert into question_tag (question_id, tag_id) values (10, 3);
insert into question_tag (question_id, tag_id) values (9, 23);
insert into question_tag (question_id, tag_id) values (24, 1);
insert into question_tag (question_id, tag_id) values (2, 27);
insert into question_tag (question_id, tag_id) values (27, 34);
insert into question_tag (question_id, tag_id) values (13, 9);
insert into question_tag (question_id, tag_id) values (3, 18);
insert into question_tag (question_id, tag_id) values (2, 18);
insert into question_tag (question_id, tag_id) values (8, 29);
insert into question_tag (question_id, tag_id) values (29, 29);
insert into question_tag (question_id, tag_id) values (21, 29);
insert into question_tag (question_id, tag_id) values (25, 16);
insert into question_tag (question_id, tag_id) values (26, 38);
insert into question_tag (question_id, tag_id) values (2, 33);
insert into question_tag (question_id, tag_id) values (19, 15);
insert into question_tag (question_id, tag_id) values (30, 31);
insert into question_tag (question_id, tag_id) values (21, 3);
insert into question_tag (question_id, tag_id) values (29, 20);
insert into question_tag (question_id, tag_id) values (29, 11);
insert into question_tag (question_id, tag_id) values (13, 23);
insert into question_tag (question_id, tag_id) values (3, 3);
insert into question_tag (question_id, tag_id) values (4, 12);
insert into question_tag (question_id, tag_id) values (4, 30);
insert into question_tag (question_id, tag_id) values (18, 31);
insert into question_tag (question_id, tag_id) values (17, 9);
insert into question_tag (question_id, tag_id) values (11, 18);
insert into question_tag (question_id, tag_id) values (20, 23);
insert into question_tag (question_id, tag_id) values (1, 22);
insert into question_tag (question_id, tag_id) values (26, 10);
insert into question_tag (question_id, tag_id) values (30, 24);
insert into question_tag (question_id, tag_id) values (1, 19);

insert into question_user_follower (question_id, user_id) values (30, 23);
insert into question_user_follower (question_id, user_id) values (13, 16);
insert into question_user_follower (question_id, user_id) values (24, 22);
insert into question_user_follower (question_id, user_id) values (10, 9);
insert into question_user_follower (question_id, user_id) values (26, 28);
insert into question_user_follower (question_id, user_id) values (2, 19);
insert into question_user_follower (question_id, user_id) values (27, 29);
insert into question_user_follower (question_id, user_id) values (13, 13);
insert into question_user_follower (question_id, user_id) values (30, 22);
insert into question_user_follower (question_id, user_id) values (7, 6);
insert into question_user_follower (question_id, user_id) values (8, 12);
insert into question_user_follower (question_id, user_id) values (3, 6);
insert into question_user_follower (question_id, user_id) values (9, 19);
insert into question_user_follower (question_id, user_id) values (24, 9);
insert into question_user_follower (question_id, user_id) values (16, 12);
insert into question_user_follower (question_id, user_id) values (22, 15);
insert into question_user_follower (question_id, user_id) values (9, 27);
insert into question_user_follower (question_id, user_id) values (8, 13);
insert into question_user_follower (question_id, user_id) values (15, 21);
insert into question_user_follower (question_id, user_id) values (9, 21);
insert into question_user_follower (question_id, user_id) values (13, 19);
insert into question_user_follower (question_id, user_id) values (7, 7);
insert into question_user_follower (question_id, user_id) values (2, 5);
insert into question_user_follower (question_id, user_id) values (9, 4);
insert into question_user_follower (question_id, user_id) values (25, 30);
insert into question_user_follower (question_id, user_id) values (11, 13);
insert into question_user_follower (question_id, user_id) values (9, 28);
insert into question_user_follower (question_id, user_id) values (6, 14);
insert into question_user_follower (question_id, user_id) values (22, 16);
insert into question_user_follower (question_id, user_id) values (26, 7);
insert into question_user_follower (question_id, user_id) values (14, 1);
insert into question_user_follower (question_id, user_id) values (17, 1);
insert into question_user_follower (question_id, user_id) values (7, 27);
insert into question_user_follower (question_id, user_id) values (19, 1);
insert into question_user_follower (question_id, user_id) values (13, 29);
insert into question_user_follower (question_id, user_id) values (7, 10);
insert into question_user_follower (question_id, user_id) values (18, 16);
insert into question_user_follower (question_id, user_id) values (8, 14);
insert into question_user_follower (question_id, user_id) values (4, 23);
insert into question_user_follower (question_id, user_id) values (15, 25);
insert into question_user_follower (question_id, user_id) values (19, 11);
insert into question_user_follower (question_id, user_id) values (23, 8);
insert into question_user_follower (question_id, user_id) values (23, 9);
insert into question_user_follower (question_id, user_id) values (21, 17);
insert into question_user_follower (question_id, user_id) values (7, 19);
insert into question_user_follower (question_id, user_id) values (29, 30);
insert into question_user_follower (question_id, user_id) values (3, 24);
insert into question_user_follower (question_id, user_id) values (6, 24);
insert into question_user_follower (question_id, user_id) values (23, 29);
insert into question_user_follower (question_id, user_id) values (19, 18);