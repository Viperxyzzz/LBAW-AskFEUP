DROP SCHEMA IF EXISTS lbaw2221 CASCADE;
CREATE SCHEMA lbaw2221;
SET search_path TO lbaw2221;

DROP TABLE IF EXISTS topic CASCADE;
CREATE TABLE topic(
    topic_id SERIAL PRIMARY KEY,
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
        IF NOT EXISTS (SELECT * FROM question WHERE author_id = NEW.author_id) AND
         NOT EXISTS (SELECT * FROM user_badge FULL OUTER JOIN badge USING(badge_id) 
         WHERE user_id = NEW.author_id AND badge_name = 'First question')

         THEN
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
        IF NOT EXISTS (SELECT * FROM answer WHERE user_id = NEW.user_id) AND
        NOT EXISTS (SELECT * FROM user_badge FULL OUTER JOIN badge USING(badge_id) 
         WHERE user_id = NEW.user_id AND badge_name = 'First answer')
        THEN
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

INSERT INTO topic(topic_name,num_views) VALUES ('Programming',345);
INSERT INTO topic(topic_name,num_views) VALUES ('Animals',172);
INSERT INTO topic(topic_name,num_views) VALUES ('Birds',97);
INSERT INTO topic(topic_name,num_views) VALUES ('Books',137);
INSERT INTO topic(topic_name,num_views) VALUES ('Buildings',127);
INSERT INTO topic(topic_name,num_views) VALUES ('Cars',125);
INSERT INTO topic(topic_name,num_views) VALUES ('Celebrities',195);
INSERT INTO topic(topic_name,num_views) VALUES ('Celebrations',71);
INSERT INTO topic(topic_name,num_views) VALUES ('Cities',188);
INSERT INTO topic(topic_name,num_views) VALUES ('Clothes',72);
INSERT INTO topic(topic_name,num_views) VALUES ('Comic book',99);
INSERT INTO topic(topic_name,num_views) VALUES ('Countries',127);
INSERT INTO topic(topic_name,num_views) VALUES ('Currencies',145);
INSERT INTO topic(topic_name,num_views) VALUES ('Diseases',83);
INSERT INTO topic(topic_name,num_views) VALUES ('Drinks',179);
INSERT INTO topic(topic_name,num_views) VALUES ('Electronic goods',63);
INSERT INTO topic(topic_name,num_views) VALUES ('Movies',62);
INSERT INTO topic(topic_name,num_views) VALUES ('Food',100);
INSERT INTO topic(topic_name,num_views) VALUES ('Football',175);
INSERT INTO topic(topic_name,num_views) VALUES ('Hobbies',138);
INSERT INTO topic(topic_name,num_views) VALUES ('Jobs',116);
INSERT INTO topic(topic_name,num_views) VALUES ('Languages',114);
INSERT INTO topic(topic_name,num_views) VALUES ('Music',178);


INSERT INTO tag(tag_name) VALUES ('javascript');
INSERT INTO tag(tag_name) VALUES ('python');
INSERT INTO tag(tag_name) VALUES ('java');
INSERT INTO tag(tag_name) VALUES ('c#');
INSERT INTO tag(tag_name) VALUES ('php');
INSERT INTO tag(tag_name) VALUES ('android');
INSERT INTO tag(tag_name) VALUES ('html');
INSERT INTO tag(tag_name) VALUES ('jquery');
INSERT INTO tag(tag_name) VALUES ('c++');
INSERT INTO tag(tag_name) VALUES ('css');
INSERT INTO tag(tag_name) VALUES ('ios');
INSERT INTO tag(tag_name) VALUES ('mysql');
INSERT INTO tag(tag_name) VALUES ('sql');
INSERT INTO tag(tag_name) VALUES ('r');
INSERT INTO tag(tag_name) VALUES ('node.js');
INSERT INTO tag(tag_name) VALUES ('reactjs');
INSERT INTO tag(tag_name) VALUES ('arrays');
INSERT INTO tag(tag_name) VALUES ('c');
INSERT INTO tag(tag_name) VALUES ('consectetur');
INSERT INTO tag(tag_name) VALUES ('tempora');
INSERT INTO tag(tag_name) VALUES ('quaerat');
INSERT INTO tag(tag_name) VALUES ('neque');
INSERT INTO tag(tag_name) VALUES ('labore');
INSERT INTO tag(tag_name) VALUES ('quisquam');
INSERT INTO tag(tag_name) VALUES ('lorem');
INSERT INTO tag(tag_name) VALUES ('quisquamont');
INSERT INTO tag(tag_name) VALUES ('modi');
INSERT INTO tag(tag_name) VALUES ('aliquam');
INSERT INTO tag(tag_name) VALUES ('velit');
INSERT INTO tag(tag_name) VALUES ('magnam');
INSERT INTO tag(tag_name) VALUES ('voluptatem');
INSERT INTO tag(tag_name) VALUES ('quaeratus');
INSERT INTO tag(tag_name) VALUES ('quaeratis');
INSERT INTO tag(tag_name) VALUES ('dolore');
INSERT INTO tag(tag_name) VALUES ('temporalius');
INSERT INTO tag(tag_name) VALUES ('dolor');
INSERT INTO tag(tag_name) VALUES ('etincidunt');
INSERT INTO tag(tag_name) VALUES ('ipsum');
INSERT INTO tag(tag_name) VALUES ('eius');
INSERT INTO tag(tag_name) VALUES ('adipisci');
INSERT INTO tag(tag_name) VALUES ('quisqumanos');
 
INSERT INTO badge(badge_name)
VALUES
  ('First question'),
  ('First answer'),
  ('Novice'),
  ('Rising star'),
  ('Expert'),
  ('Master');

INSERT INTO users(username,email,name,password,score,is_moderator,is_admin) VALUES ('jimmypage','jimmy@gmail.com','Jimmy Page','$2a$10$Ouen.rfaV99RokFnkwK5.e0D0/8fgIDx7yfnIhc3EUuom7jJuYBky',62,false,true);
INSERT INTO users(username,email,name,password,score,is_moderator,is_admin) VALUES ('dfincher','david@gmail.com','David Fincher','$2a$10$Ouen.rfaV99RokFnkwK5.e0D0/8fgIDx7yfnIhc3EUuom7jJuYBky',127,true,false);
INSERT INTO users(username,email,name,password,score,is_moderator,is_admin) VALUES ('torvalds','linus@gmail.com','Linus Torvalds','$2a$10$Ouen.rfaV99RokFnkwK5.e0D0/8fgIDx7yfnIhc3EUuom7jJuYBky',248,false,false);
INSERT INTO users(username,email,name,password,score,is_moderator,is_admin) VALUES ('gbertolaccini3','gbertolaccini3@xrea.com','Gilburt Bertolaccini','$2a$10$Ouen.rfaV99RokFnkwK5.e0D0/8fgIDx7yfnIhc3EUuom7jJuYBky',37,false,false);
INSERT INTO users(username,email,name,password,score,is_moderator,is_admin) VALUES ('mdirr4','mdirr4@photobucket.com','Marrilee Dirr','$2a$10$Ouen.rfaV99RokFnkwK5.e0D0/8fgIDx7yfnIhc3EUuom7jJuYBky',233,false,false);
INSERT INTO users(username,email,name,password,score,is_moderator,is_admin) VALUES ('rmerry5','rmerry5@unc.edu','Rainer Merry','$2a$10$Ouen.rfaV99RokFnkwK5.e0D0/8fgIDx7yfnIhc3EUuom7jJuYBky',59,false,false);
INSERT INTO users(username,email,name,password,score,is_moderator,is_admin) VALUES ('gtheyer6','gtheyer6@clickbank.net','Gibb Theyer','$2a$10$Ouen.rfaV99RokFnkwK5.e0D0/8fgIDx7yfnIhc3EUuom7jJuYBky',181,false,false);
INSERT INTO users(username,email,name,password,score,is_moderator,is_admin) VALUES ('xcandlish7','xcandlish7@yellowbook.com','Xenos Candlish','$2a$10$Ouen.rfaV99RokFnkwK5.e0D0/8fgIDx7yfnIhc3EUuom7jJuYBky',12,false,false);
INSERT INTO users(username,email,name,password,score,is_moderator,is_admin) VALUES ('eheight8','eheight8@bing.com','Eolanda Height','$2a$10$Ouen.rfaV99RokFnkwK5.e0D0/8fgIDx7yfnIhc3EUuom7jJuYBky',243,false,false);
INSERT INTO users(username,email,name,password,score,is_moderator,is_admin) VALUES ('bguttridge9','bguttridge9@vinaora.com','Bunni Guttridge','$2a$10$Ouen.rfaV99RokFnkwK5.e0D0/8fgIDx7yfnIhc3EUuom7jJuYBky',124,false,false);
INSERT INTO users(username,email,name,password,score,is_moderator,is_admin) VALUES ('ibeartupa','ibeartupa@netscape.com','Ira Beartup','$2a$10$Ouen.rfaV99RokFnkwK5.e0D0/8fgIDx7yfnIhc3EUuom7jJuYBky',153,false,false);
INSERT INTO users(username,email,name,password,score,is_moderator,is_admin) VALUES ('mcallejab','mcallejab@pagesperso-orange.fr','Merilee Calleja','$2a$10$Ouen.rfaV99RokFnkwK5.e0D0/8fgIDx7yfnIhc3EUuom7jJuYBky',103,false,false);
INSERT INTO users(username,email,name,password,score,is_moderator,is_admin) VALUES ('mshapterc','mshapterc@mapquest.com','Meggi Shapter','$2a$10$Ouen.rfaV99RokFnkwK5.e0D0/8fgIDx7yfnIhc3EUuom7jJuYBky',182,false,false);
INSERT INTO users(username,email,name,password,score,is_moderator,is_admin) VALUES ('tgasconed','tgasconed@tuttocitta.it','Thayne Gascone','$2a$10$Ouen.rfaV99RokFnkwK5.e0D0/8fgIDx7yfnIhc3EUuom7jJuYBky',101,false,false);
INSERT INTO users(username,email,name,password,score,is_moderator,is_admin) VALUES ('kpackhame','kpackhame@oracle.com','Kipp Packham','$2a$10$Ouen.rfaV99RokFnkwK5.e0D0/8fgIDx7yfnIhc3EUuom7jJuYBky',63,false,false);
INSERT INTO users(username,email,name,password,score,is_moderator,is_admin) VALUES ('mbedrosianf','mbedrosianf@google.de','Max Bedrosian','$2a$10$Ouen.rfaV99RokFnkwK5.e0D0/8fgIDx7yfnIhc3EUuom7jJuYBky',67,false,false);
INSERT INTO users(username,email,name,password,score,is_moderator,is_admin) VALUES ('rmatschkeg','rmatschkeg@google.it','Robinette Matschke','$2a$10$Ouen.rfaV99RokFnkwK5.e0D0/8fgIDx7yfnIhc3EUuom7jJuYBky',9,false,false);
INSERT INTO users(username,email,name,password,score,is_moderator,is_admin) VALUES ('lbalchenh','lbalchenh@mapy.cz','Lanie Balchen','$2a$10$Ouen.rfaV99RokFnkwK5.e0D0/8fgIDx7yfnIhc3EUuom7jJuYBky',253,false,false);
INSERT INTO users(username,email,name,password,score,is_moderator,is_admin) VALUES ('dtromani','dtromani@com.com','Dasi Troman','$2a$10$Ouen.rfaV99RokFnkwK5.e0D0/8fgIDx7yfnIhc3EUuom7jJuYBky',112,false,false);
INSERT INTO users(username,email,name,password,score,is_moderator,is_admin) VALUES ('awattingj','awattingj@twitpic.com','Ayn Watting','$2a$10$Ouen.rfaV99RokFnkwK5.e0D0/8fgIDx7yfnIhc3EUuom7jJuYBky',137,false,false);
INSERT INTO users(username,email,name,password,score,is_moderator,is_admin) VALUES ('pmcphilemyk','pmcphilemyk@home.pl','Pamella McPhilemy','$2a$10$Ouen.rfaV99RokFnkwK5.e0D0/8fgIDx7yfnIhc3EUuom7jJuYBky',35,false,false);
INSERT INTO users(username,email,name,password,score,is_moderator,is_admin) VALUES ('scommonl','scommonl@amazon.com','Silvana Common','$2a$10$Ouen.rfaV99RokFnkwK5.e0D0/8fgIDx7yfnIhc3EUuom7jJuYBky',146,false,false);
INSERT INTO users(username,email,name,password,score,is_moderator,is_admin) VALUES ('rspaduzzam','rspaduzzam@oaic.gov.au','Rebe Spaduzza','$2a$10$Ouen.rfaV99RokFnkwK5.e0D0/8fgIDx7yfnIhc3EUuom7jJuYBky',57,false,false);
INSERT INTO users(username,email,name,password,score,is_moderator,is_admin) VALUES ('chayballn','chayballn@imgur.com','Corey Hayball','$2a$10$Ouen.rfaV99RokFnkwK5.e0D0/8fgIDx7yfnIhc3EUuom7jJuYBky',37,false,false);
INSERT INTO users(username,email,name,password,score,is_moderator,is_admin) VALUES ('cfrudeo','cfrudeo@time.com','Caz Frude','$2a$10$Ouen.rfaV99RokFnkwK5.e0D0/8fgIDx7yfnIhc3EUuom7jJuYBky',88,false,false);
INSERT INTO users(username,email,name,password,score,is_moderator,is_admin) VALUES ('rsimmonsp','rsimmonsp@about.com','Rozina Simmons','$2a$10$Ouen.rfaV99RokFnkwK5.e0D0/8fgIDx7yfnIhc3EUuom7jJuYBky',123,false,false);
INSERT INTO users(username,email,name,password,score,is_moderator,is_admin) VALUES ('dtrevnaq','dtrevnaq@dagondesign.com','Dalt Trevna','$2a$10$Ouen.rfaV99RokFnkwK5.e0D0/8fgIDx7yfnIhc3EUuom7jJuYBky',179,false,false);
INSERT INTO users(username,email,name,password,score,is_moderator,is_admin) VALUES ('lhryniewiczr','lhryniewiczr@discovery.com','Lazaro Hryniewicz','$2a$10$Ouen.rfaV99RokFnkwK5.e0D0/8fgIDx7yfnIhc3EUuom7jJuYBky',74,false,false);
INSERT INTO users(username,email,name,password,score,is_moderator,is_admin) VALUES ('mshepherdsons','mshepherdsons@census.gov','Muriel Shepherdson','$2a$10$Ouen.rfaV99RokFnkwK5.e0D0/8fgIDx7yfnIhc3EUuom7jJuYBky',49,false,false);
INSERT INTO users(username,email,name,password,score,is_moderator,is_admin) VALUES ('jbasellit','jbasellit@diigo.com','Jeannine Baselli','$2a$10$Ouen.rfaV99RokFnkwK5.e0D0/8fgIDx7yfnIhc3EUuom7jJuYBky',95,false,false);

INSERT INTO question( title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) VALUES ( 'How to center a div?', 'What method can I use to center a div horizontally to the middle of the screen?', 17, 282, 1, '2021-12-01 04:24:58', false, 1);
INSERT INTO question( title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) VALUES ( 'How do I add multi-line comments in Python?', '# works for single line comments but Im wondering how to comment multiple lines of code', 5, 100, 0, '2021-01-01 05:26:58', false, 1);
INSERT INTO question( title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) VALUES ( 'What is the difference between git pull and git fetch?', 'I don`t understand the difference between the two.', 96, 192, 1, '2022-01-19 04:52:59', false, 3);
INSERT INTO question( title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) VALUES ( 'fermentum donec ut mauris eget massa tempor convallis nulla', 'eu orci mauris lacinia sapien quis libero nullam sit amet', 71, 242, 1, '2022-04-14 23:56:49', false, 20);
INSERT INTO question( title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) VALUES ( 'justo in blandit ultrices enim lorem ipsum dolor sit', 'sit amet lobortis sapien sapien non mi integer ac neque duis bibendum morbi non quam nec dui luctus rutrum', 1, 75, 1, '2022-06-08 01:57:24', false, 3);
INSERT INTO question( title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) VALUES ( 'lorem id ligula suspendisse ornare', 'ligula vehicula consequat morbi a ipsum integer a nibh in quis justo maecenas rhoncus aliquam lacus morbi quis tortor id nulla', 55, 6, 1, '2022-01-05 07:56:14', false, 7);
INSERT INTO question( title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) VALUES ( 'lobortis ligula sit amet eleifend pede', 'nunc viverra dapibus nulla suscipit ligula in lacus curabitur at ipsum ac tellus semper interdum', 93, 151, 1, '2021-11-18 00:14:50', true, 12);
INSERT INTO question( title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) VALUES ( 'eu massa donec dapibus duis', 'ac enim in tempor turpis nec euismod scelerisque quam turpis adipiscing lorem vitae mattis nibh ligula nec sem duis', 79, 196, 1, '2022-02-10 15:17:03', true, 5);
INSERT INTO question( title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) VALUES ( 'ac consequat metus sapien ut nunc vestibulum ante ipsum', 'duis aliquam convallis nunc proin at turpis a pede posuere nonummy integer non velit donec diam neque vestibulum eget vulputate ut ultrices vel augue vestibulum ante ipsum primis', 33, 199, 1, '2021-12-08 11:28:46', false, 5);
INSERT INTO question( title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) VALUES ( 'maecenas pulvinar lobortis est phasellus sit amet erat nulla tempus', 'magna vulputate luctus cum sociis natoque penatibus et magnis dis parturient montes nascetur ridiculus mus vivamus vestibulum', 61, 177, 1, '2022-01-11 19:38:34', false, 8);
INSERT INTO question( title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) VALUES ( 'lectus pellentesque eget nunc', 'amet consectetuer adipiscing elit proin interdum mauris non ligula pellentesque ultrices phasellus id sapien in sapien iaculis congue vivamus metus arcu adipiscing', 11, 132, 1, '2021-12-11 07:13:28', true, 3);
INSERT INTO question( title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) VALUES ( 'eros suspendisse accumsan tortor quis turpis sed ante vivamus tortor', 'quisque erat eros viverra eget congue eget semper rutrum nulla nunc purus phasellus in felis donec semper sapien a libero nam dui proin', 89, 52, 1, '2022-10-03 13:01:49', false, 16);
INSERT INTO question( title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) VALUES ( 'in hac habitasse platea dictumst etiam', 'faucibus accumsan odio curabitur convallis duis consequat dui nec nisi', 73, 261, 1, '2021-12-12 01:42:34', false, 6);
INSERT INTO question( title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) VALUES ( 'ultrices mattis odio donec vitae nisi nam', 'sapien cursus vestibulum proin eu mi nulla ac enim in tempor turpis nec euismod scelerisque quam turpis adipiscing', 85, 202, 1, '2022-01-16 01:16:00', false, 11);
INSERT INTO question( title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) VALUES ( 'luctus nec molestie sed justo', 'cras non velit nec nisi vulputate nonummy maecenas tincidunt lacus at velit vivamus vel nulla eget eros elementum pellentesque quisque porta volutpat erat quisque erat eros viverra eget congue', 25, 57, 1, '2022-01-08 10:02:51', false, 8);
INSERT INTO question( title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) VALUES ( 'tincidunt eu felis fusce posuere felis sed lacus morbi sem', 'cubilia curae donec pharetra magna vestibulum aliquet ultrices erat tortor sollicitudin mi sit amet lobortis sapien sapien non mi integer ac', 96, 142, 1, '2022-04-15 04:36:09', true, 15);
INSERT INTO question( title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) VALUES ( 'aliquam non mauris morbi non lectus', 'etiam vel augue vestibulum rutrum rutrum neque aenean auctor gravida sem praesent id massa id nisl venenatis lacinia aenean sit amet justo morbi ut', 35, 205, 1, '2022-05-18 17:07:00', true, 5);
INSERT INTO question( title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) VALUES ( 'donec dapibus duis at', 'massa tempor convallis nulla neque libero convallis eget eleifend luctus ultricies eu nibh quisque id justo sit amet sapien dignissim vestibulum vestibulum ante ipsum primis in', 42, 75, 1, '2022-01-14 12:32:50', true, 14);
INSERT INTO question( title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) VALUES ( 'erat tortor sollicitudin mi sit amet lobortis sapien sapien', 'blandit non interdum in ante vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae duis faucibus', 44, 239, 1, '2022-02-03 03:32:17', true, 1);
INSERT INTO question( title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) VALUES ( 'sodales scelerisque mauris sit amet eros suspendisse', 'sapien sapien non mi integer ac neque duis bibendum morbi non quam nec dui luctus rutrum', 9, 110, 1, '2022-02-12 14:11:22', true, 15);
INSERT INTO question( title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) VALUES ( 'pellentesque quisque porta volutpat erat quisque erat', 'curabitur convallis duis consequat dui nec nisi volutpat eleifend donec ut dolor morbi vel lectus in quam fringilla rhoncus mauris', 51, 243, 1, '2022-04-28 12:06:26', false, 3);
INSERT INTO question( title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) VALUES ( 'mauris non ligula pellentesque ultrices phasellus id sapien in', 'turpis eget elit sodales scelerisque mauris sit amet eros suspendisse accumsan tortor quis turpis sed ante vivamus tortor duis mattis egestas metus aenean fermentum donec ut', 32, 256, 1, '2022-07-21 06:49:22', true, 9);
INSERT INTO question( title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) VALUES ( 'imperdiet sapien urna pretium nisl ut', 'molestie nibh in lectus pellentesque at nulla suspendisse potenti cras in purus eu magna vulputate', 35, 284, 1, '2022-09-07 05:57:16', true, 15);
INSERT INTO question( title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) VALUES ( 'habitasse platea dictumst etiam faucibus cursus urna ut tellus', 'platea dictumst maecenas ut massa quis augue luctus tincidunt nulla mollis molestie lorem quisque ut erat curabitur gravida nisi at nibh in hac habitasse platea dictumst aliquam', 98, 116, 1, '2021-11-25 00:25:38', false, 2);
INSERT INTO question( title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) VALUES ( 'amet turpis elementum ligula vehicula consequat morbi', 'nisi vulputate nonummy maecenas tincidunt lacus at velit vivamus vel', 41, 216, 1, '2021-11-18 23:56:25', false, 4);
INSERT INTO question( title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) VALUES ( 'porttitor lorem id ligula suspendisse ornare consequat lectus in est', 'primis in faucibus orci luctus et ultrices posuere cubilia curae nulla dapibus dolor vel est donec odio justo sollicitudin ut suscipit a feugiat et eros vestibulum ac est lacinia', 96, 172, 1, '2022-09-02 19:04:52', false, 16);
INSERT INTO question( title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) VALUES ( 'tristique est et tempus semper est quam pharetra magna ac', 'varius nulla facilisi cras non velit nec nisi vulputate nonummy maecenas tincidunt lacus at velit vivamus vel nulla', 9, 102, 1, '2021-11-19 00:10:39', true, 5);
INSERT INTO question( title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) VALUES ( 'adipiscing elit proin risus praesent lectus vestibulum quam sapien', 'est phasellus sit amet erat nulla tempus vivamus in felis eu sapien cursus vestibulum proin eu', 42, 173, 1, '2022-07-26 03:19:55', false, 8);
INSERT INTO question( title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) VALUES ( 'non mauris morbi non lectus aliquam sit amet', 'id ornare imperdiet sapien urna pretium nisl ut volutpat sapien arcu', 85, 201, 1, '2022-01-11 10:24:07', false, 12);
INSERT INTO question( title, full_text, num_votes, num_views, num_answers, date, was_edited, author_id) VALUES ( 'lacus at turpis donec posuere metus vitae ipsum', 'est phasellus sit amet erat nulla tempus vivamus in felis', 6, 192, 1, '2022-06-29 22:50:36', false, 7);

INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'You can use the `text-align` property with the value `center`.', 31, true, false, '2022-05-18 00:01:14', 1, 2);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'Best way to do this is to use the `margin` property and set it to `0 auto`. This will make the horizontal margin equally divided', 41, true, false, '2021-11-08 13:01:18', 1, 3);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'git fetch is similar to pull but doesnt merge. i.e. it fetches remote updates but your local stays the same', 41, true, false, '2022-02-22 09:48:22', 2, 2);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'eget congue eget semper rutrum nulla nunc purus phasellus in felis donec semper sapien a libero nam dui proin', 39, true, true, '2022-04-15 18:47:13', 12, 26);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'rutrum neque aenean auctor gravida sem praesent id massa id nisl venenatis', 30, true, false, '2021-12-15 03:02:19', 30, 1);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'risus auctor sed tristique in tempus sit amet sem fusce consequat nulla', 41, true, false, '2022-06-01 14:27:04', 11, 13);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'vitae quam suspendisse potenti nullam porttitor lacus at turpis donec posuere metus vitae ipsum', 42, true, true, '2022-08-12 16:38:46', 26, 5);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'et magnis dis parturient montes nascetur ridiculus mus etiam vel augue vestibulum rutrum', 11, true, false, '2022-06-09 02:08:26', 14, 24);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'donec pharetra magna vestibulum aliquet ultrices erat tortor sollicitudin mi', 39, false, true, '2022-02-05 17:36:00', 10, 18);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'eros suspendisse accumsan tortor quis turpis sed ante vivamus tortor duis mattis egestas metus', 29, true, false, '2022-08-24 06:09:49', 14, 20);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'orci luctus et ultrices posuere cubilia curae donec pharetra magna vestibulum aliquet ultrices', 43, true, false, '2022-08-14 07:25:26', 12, 17);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'morbi non lectus aliquam sit amet diam in magna bibendum imperdiet nullam orci pede venenatis non sodales', 46, true, true, '2022-03-28 18:23:14', 9, 22);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'quam sapien varius ut blandit non interdum in ante vestibulum ante ipsum primis in', 15, true, false, '2022-03-26 22:06:15', 15, 13);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'interdum in ante vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae duis faucibus accumsan', 48, true, false, '2021-11-05 00:18:53', 20, 5);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'proin eu mi nulla ac enim in tempor turpis nec euismod scelerisque quam turpis adipiscing lorem', 44, false, true, '2022-07-22 22:14:34', 26, 4);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'mauris vulputate elementum nullam varius nulla facilisi cras non velit', 33, true, false, '2022-06-10 15:31:02', 3, 8);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'diam in magna bibendum imperdiet nullam orci pede venenatis non sodales sed tincidunt eu felis fusce', 34, false, true, '2022-02-19 01:12:48', 19, 1);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'urna pretium nisl ut volutpat sapien arcu sed augue aliquam erat volutpat in congue', 41, true, true, '2022-07-13 01:17:03', 15, 6);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'blandit mi in porttitor pede justo eu massa donec dapibus duis at velit eu est congue', 27, false, true, '2022-04-14 14:31:44', 11, 14);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'scelerisque mauris sit amet eros suspendisse accumsan tortor quis turpis sed ante vivamus', 42, false, false, '2021-11-10 14:09:42', 24, 23);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'facilisi cras non velit nec nisi vulputate nonummy maecenas tincidunt lacus at velit vivamus vel nulla eget eros', 5, false, false, '2021-11-21 15:19:35', 29, 2);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'orci nullam molestie nibh in lectus pellentesque at nulla suspendisse potenti cras in purus eu magna vulputate luctus cum', 47, true, false, '2022-06-09 12:16:01', 24, 18);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'laoreet ut rhoncus aliquet pulvinar sed nisl nunc rhoncus dui vel sem sed sagittis nam', 12, true, false,'2022-01-25 08:59:07', 17, 1);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'feugiat et eros vestibulum ac est lacinia nisi venenatis tristique fusce congue diam id ornare imperdiet sapien urna pretium', 39, true, false,'2022-06-18 23:26:06', 26, 17);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'ultricies eu nibh quisque id justo sit amet sapien dignissim vestibulum vestibulum ante', 43, true, false, '2022-09-10 11:06:45', 28, 13);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'penatibus et magnis dis parturient montes nascetur ridiculus mus etiam', 48, false, true, '2021-11-28 05:13:41', 26, 9);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'nulla ultrices aliquet maecenas leo odio condimentum id luctus nec molestie sed', 50, false, false, '2022-03-11 20:36:00', 6, 22);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'at vulputate vitae nisl aenean lectus pellentesque eget nunc donec quis orci', 23, false, true, '2022-09-07 07:06:28', 18, 18);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'eget eros elementum pellentesque quisque porta volutpat erat quisque erat eros', 19, true, true, '2021-12-29 16:25:42', 14, 6);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'faucibus accumsan odio curabitur convallis duis consequat dui nec nisi volutpat eleifend donec ut dolor morbi vel', 8, false, true, '2022-07-21 03:16:56', 1, 10);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'adipiscing elit proin risus praesent lectus vestibulum quam sapien varius ut blandit non interdum in', 13, false, false, '2022-10-19 18:51:57', 3, 28);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'bibendum felis sed interdum venenatis turpis enim blandit mi in porttitor pede justo eu', 45, false, false, '2021-12-07 19:23:05', 14, 6);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'quis turpis eget elit sodales scelerisque mauris sit amet eros suspendisse accumsan tortor', 3, false, false, '2022-08-08 03:31:11', 18, 18);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'fermentum justo nec condimentum neque sapien placerat ante nulla justo aliquam quis turpis eget elit sodales', 25, true, true, '2022-09-19 12:57:46', 27, 19);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'eleifend luctus ultricies eu nibh quisque id justo sit amet sapien dignissim vestibulum', 43, false, true, '2022-08-15 05:16:48', 25, 8);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'maecenas tincidunt lacus at velit vivamus vel nulla eget eros elementum', 1, false, true, '2022-01-26 07:51:04', 20, 7);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'vivamus metus arcu adipiscing molestie hendrerit at vulputate vitae nisl aenean lectus pellentesque eget nunc donec quis orci eget orci', 1, true, false, '2022-06-22 00:38:12', 29, 4);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'eros viverra eget congue eget semper rutrum nulla nunc purus phasellus in felis donec semper sapien a libero nam dui', 30, false, false, '2021-12-23 03:31:36', 18, 30);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'sed justo pellentesque viverra pede ac diam cras pellentesque volutpat dui maecenas tristique est et', 45, true, false,'2022-07-21 01:48:54', 7, 23);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'tortor id nulla ultrices aliquet maecenas leo odio condimentum id luctus nec molestie', 46, true, false, '2022-03-17 18:04:22', 18, 10);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'tempus vivamus in felis eu sapien cursus vestibulum proin eu mi', 20, false, false, '2022-08-12 10:53:25', 9, 11);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'mattis egestas metus aenean fermentum donec ut mauris eget massa tempor convallis nulla neque libero convallis eget eleifend luctus ultricies', 39, true, false, '2022-02-26 16:26:20', 22, 29);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'duis bibendum morbi non quam nec dui luctus rutrum nulla tellus in sagittis dui vel nisl duis ac nibh fusce', 35, false, false, '2021-12-10 02:44:32', 23, 18);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'vestibulum rutrum rutrum neque aenean auctor gravida sem praesent id', 22, true, true, '2022-04-14 11:06:28', 17, 8);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'non mi integer ac neque duis bibendum morbi non quam nec dui luctus rutrum nulla tellus in sagittis dui vel', 10, false, false, '2022-01-01 10:26:37', 14, 23);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'urna ut tellus nulla ut erat id mauris vulputate elementum nullam varius nulla', 2, true, true, '2022-07-27 04:03:32', 7, 19);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'justo maecenas rhoncus aliquam lacus morbi quis tortor id nulla ultrices', 38, false, true, '2022-04-11 03:13:53', 6, 7);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'vulputate ut ultrices vel augue vestibulum ante ipsum primis in faucibus orci', 17, true, false, '2022-02-14 18:45:51', 20, 18);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'at velit eu est congue elementum in hac habitasse platea dictumst morbi', 6, false, true, '2022-08-31 06:54:31', 3, 22);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'rhoncus mauris enim leo rhoncus sed vestibulum sit amet cursus id turpis integer aliquet massa id', 23, false, false, '2022-07-04 01:16:05', 18, 25);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'accumsan odio curabitur convallis duis consequat dui nec nisi volutpat eleifend donec ut dolor morbi vel', 37, false, true, '2022-01-07 13:20:34', 28, 9);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae mauris viverra', 31, false, true, '2022-08-25 20:20:25', 25, 15);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'blandit non interdum in ante vestibulum ante ipsum primis in faucibus', 36, true, false, '2021-12-29 07:02:25', 4, 25);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'purus aliquet at feugiat non pretium quis lectus suspendisse potenti in eleifend quam a odio in hac habitasse platea', 31, false, true, '2022-06-02 05:30:10', 26, 14);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'risus semper porta volutpat quam pede lobortis ligula sit amet eleifend pede libero quis orci nullam molestie nibh', 21, false, false, '2022-05-11 17:14:33', 2, 3);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'sagittis dui vel nisl duis ac nibh fusce lacus purus aliquet at feugiat', 37, false, true, '2022-08-22 17:52:06', 30, 6);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'nulla elit ac nulla sed vel enim sit amet nunc viverra dapibus nulla', 15, false, false, '2022-06-09 20:34:55', 29, 8);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'amet lobortis sapien sapien non mi integer ac neque duis bibendum morbi non', 2, true, false, '2022-02-05 13:10:19', 26, 10);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'posuere cubilia curae donec pharetra magna vestibulum aliquet ultrices erat tortor sollicitudin mi sit amet lobortis', 44, false, true, '2022-05-12 11:45:34', 10, 16);
INSERT INTO answer( full_text, num_votes, is_correct, was_edited, date, question_id, user_id) VALUES ( 'sem praesent id massa id nisl venenatis lacinia aenean sit amet justo morbi ut', 3, false, true, '2022-09-28 16:09:01', 4, 6);

INSERT INTO notification(notification_text,date,viewed,user_id)
VALUES
  ('New answers to your question','May 25, 2022','No',1),
  ('New vote on your comment','Mar 4, 2022','No',2),
  ('Your answer was marked as correct','Aug 4, 2022','No',3),
  ('New answers to your question','Apr 26, 2022','No',4),
  ('You received a badge','Jun 1, 2022','No',5);

INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'Altough it might work, this should only be used with text.', 24, '2022-08-29 09:49:24',30, 1, 3);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'Awesome! This worked just fine.', 48, '2022-09-15 22:50:46',29, 2, 1);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'You probably want to skip the pull and just do a "git rebase origin" as the last step since you already fetched the changes.', 43, '2021-10-26 06:24:38',28, 3, 1);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'in magna bibendum imperdiet nullam orci pede venenatis non sodales sed tincidunt eu felis', 19, '2022-05-13 23:57:52',27, 52, 27);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'nisi vulputate nonummy maecenas tincidunt lacus at velit vivamus vel nulla eget eros elementum pellentesque quisque', 24, '2022-07-26 08:48:54',26, 50, 12);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'magna at nunc commodo placerat praesent blandit nam nulla integer pede justo lacinia eget', 31, '2022-05-13 14:47:21',25, 48, 2);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'quisque erat eros viverra eget congue eget semper rutrum nulla nunc', 18, '2022-06-19 14:43:15',24, 51, 11);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'nisi venenatis tristique fusce congue diam id ornare imperdiet sapien urna pretium nisl ut', 3, '2021-12-26 08:05:08',23, 26, 7);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'nibh fusce lacus purus aliquet at feugiat non pretium quis lectus suspendisse potenti', 36, '2022-08-30 19:39:37',22, 4, 27);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'sapien non mi integer ac neque duis bibendum morbi non quam nec dui luctus rutrum nulla tellus', 16, '2022-03-15 21:36:19',21, 21, 12);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'viverra eget congue eget semper rutrum nulla nunc purus phasellus in felis donec semper sapien a libero nam', 27, '2022-06-19 17:25:50',20, 26, 27);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'amet consectetuer adipiscing elit proin risus praesent lectus vestibulum quam sapien varius', 43, '2022-06-09 15:32:10',19, 26, 2);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'posuere cubilia curae donec pharetra magna vestibulum aliquet ultrices erat', 48, '2022-01-22 04:34:43',23, 52, 12);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'curabitur convallis duis consequat dui nec nisi volutpat eleifend donec ut dolor morbi vel lectus in quam fringilla', 7, '2021-12-18 22:08:37',18, 1, 1);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae mauris viverra diam vitae quam suspendisse potenti', 35, '2021-11-23 01:59:06',17, 13, 10);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'lorem vitae mattis nibh ligula nec sem duis aliquam convallis nunc proin at turpis', 22, '2022-10-03 16:12:43',17, 28, 26);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'orci luctus et ultrices posuere cubilia curae donec pharetra magna vestibulum', 23, '2021-10-23 13:09:07',5, 11, 1);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'scelerisque mauris sit amet eros suspendisse accumsan tortor quis turpis sed ante vivamus tortor duis', 6, '2022-10-09 17:34:55',16, 34, 4);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'aenean sit amet justo morbi ut odio cras mi pede malesuada in', 36, '2021-12-26 06:55:46',14, 21, 6);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'est lacinia nisi venenatis tristique fusce congue diam id ornare', 5, '2022-10-18 12:57:38',15, 30, 1);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'eget vulputate ut ultrices vel augue vestibulum ante ipsum primis in faucibus orci luctus et ultrices', 30, '2021-11-07 20:16:13',15, 40, 10);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'sed vestibulum sit amet cursus id turpis integer aliquet massa id lobortis convallis tortor risus dapibus augue vel', 35, '2022-08-24 11:39:41',14, 35, 7);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'lorem quisque ut erat curabitur gravida nisi at nibh in hac habitasse', 30, '2021-11-01 03:20:41',18, 9, 9);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'felis eu sapien cursus vestibulum proin eu mi nulla ac', 27, '2022-04-22 09:34:25',19, 30, 18);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'ante vel ipsum praesent blandit lacinia erat vestibulum sed magna at nunc commodo placerat praesent blandit nam nulla integer pede', 29, '2021-12-05 19:20:34',13, 54, 12);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'dui luctus rutrum nulla tellus in sagittis dui vel nisl duis ac nibh fusce lacus purus aliquet at', 48, '2022-06-02 13:13:36',12, 46, 2);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'sed nisl nunc rhoncus dui vel sem sed sagittis nam congue risus semper porta volutpat quam pede lobortis', 3, '2022-10-01 13:55:50',11, 38, 27);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'in felis eu sapien cursus vestibulum proin eu mi nulla', 20, '2022-04-28 18:37:38',30, 11, 22);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'integer a nibh in quis justo maecenas rhoncus aliquam lacus morbi quis', 40, '2022-06-16 03:33:54',10, 34, 29);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'in tempus sit amet sem fusce consequat nulla nisl nunc nisl duis bibendum', 27, '2022-08-13 22:34:53',9, 16, 26);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'neque duis bibendum morbi non quam nec dui luctus rutrum nulla tellus in sagittis dui vel nisl duis ac nibh', 28, '2022-03-12 14:07:37',8, 45, 6);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'mi pede malesuada in imperdiet et commodo vulputate justo in blandit ultrices enim lorem ipsum dolor sit amet consectetuer adipiscing', 10, '2021-11-27 19:41:56',7, 46, 27);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'sed nisl nunc rhoncus dui vel sem sed sagittis nam congue risus semper porta volutpat quam pede lobortis', 24, '2022-03-03 13:51:57',6, 8, 17);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'tellus nisi eu orci mauris lacinia sapien quis libero nullam sit amet turpis elementum ligula vehicula consequat', 1, '2022-01-13 20:28:22',5, 20, 10);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'nisl duis bibendum felis sed interdum venenatis turpis enim blandit mi in porttitor pede justo eu', 41, '2022-07-15 07:33:00',4, 56, 30);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'porta volutpat quam pede lobortis ligula sit amet eleifend pede libero', 13, '2022-08-24 22:12:04',7, 41, 14);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'eget tempus vel pede morbi porttitor lorem id ligula suspendisse ornare consequat lectus in est risus', 32, '2022-04-18 19:09:49',3, 39, 7);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'odio odio elementum eu interdum eu tincidunt in leo maecenas pulvinar lobortis est phasellus sit', 26, '2021-11-13 21:27:31',2, 48, 22);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'nec molestie sed justo pellentesque viverra pede ac diam cras', 33, '2022-07-13 21:24:18',12, 36, 25);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'nulla mollis molestie lorem quisque ut erat curabitur gravida nisi', 30, '2021-11-11 11:18:43',1, 40, 2);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'amet sem fusce consequat nulla nisl nunc nisl duis bibendum felis sed interdum venenatis turpis enim', 8, '2022-09-30 21:04:43',1, 52, 8);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'luctus ultricies eu nibh quisque id justo sit amet sapien dignissim vestibulum', 50, '2021-10-29 04:24:32',2, 56, 21);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'nec nisi vulputate nonummy maecenas tincidunt lacus at velit vivamus vel nulla eget eros elementum pellentesque quisque porta volutpat erat', 13, '2022-10-08 03:40:53',3, 59, 8);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'purus aliquet at feugiat non pretium quis lectus suspendisse potenti in eleifend quam a odio in hac habitasse platea dictumst', 18, '2022-09-27 22:19:34',4, 59, 23);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'a feugiat et eros vestibulum ac est lacinia nisi venenatis tristique fusce congue diam id ornare imperdiet sapien', 44, '2022-07-10 01:04:11',5, 11, 6);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'lacus morbi sem mauris laoreet ut rhoncus aliquet pulvinar sed nisl nunc', 37, '2022-05-21 09:37:44',6, 31, 25);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'est donec odio justo sollicitudin ut suscipit a feugiat et', 1, '2022-09-11 17:17:15',7, 59, 8);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'mauris vulputate elementum nullam varius nulla facilisi cras non velit nec nisi vulputate nonummy', 22, '2022-04-16 21:38:30',8, 31, 20);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'pellentesque volutpat dui maecenas tristique est et tempus semper est', 41, '2021-12-12 12:23:19',9, 2, 1);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'potenti in eleifend quam a odio in hac habitasse platea dictumst maecenas ut massa quis augue luctus', 33, '2022-02-03 07:32:11',10, 39, 29);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'lacinia eget tincidunt eget tempus vel pede morbi porttitor lorem id', 42, '2021-11-27 01:18:51',11, 3, 24);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'tempus sit amet sem fusce consequat nulla nisl nunc nisl duis bibendum felis sed interdum venenatis', 27, '2022-03-04 16:30:59',12, 57, 18);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'tellus in sagittis dui vel nisl duis ac nibh fusce lacus purus aliquet at', 39, '2022-08-27 04:38:27',13, 50, 9);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'quam suspendisse potenti nullam porttitor lacus at turpis donec posuere metus vitae', 50, '2021-10-25 01:20:00',14, 3, 17);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'iaculis justo in hac habitasse platea dictumst etiam faucibus cursus urna ut tellus nulla ut', 11, '2022-10-11 06:41:39',14, 24, 21);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'eget eros elementum pellentesque quisque porta volutpat erat quisque erat', 46, '2022-07-20 16:07:09',19, 13, 1);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'quisque id justo sit amet sapien dignissim vestibulum vestibulum ante ipsum primis in faucibus', 22, '2022-10-20 19:33:37',15, 51, 30);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'in eleifend quam a odio in hac habitasse platea dictumst maecenas', 1, '2021-12-15 14:21:25',16, 36, 12);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'condimentum neque sapien placerat ante nulla justo aliquam quis turpis eget elit', 5, '2022-04-18 11:41:13',17, 44, 8);
INSERT INTO comment( full_text, num_votes, date, question_id, answer_id, user_id) VALUES ( 'dui vel nisl duis ac nibh fusce lacus purus aliquet at feugiat non pretium quis', 49, '2022-06-30 22:51:50',18, 23, 11);

INSERT INTO report(reason,date,question_id,answer_id,comment_id)
VALUES
  ('Spam','Dec 30, 2021',1,NULL,NULL),
  ('Hate speech','Mar 23, 2022',2,2,NULL),
  ('Harassment','May 26, 2022',3,3,3),
  ('blandit. Nam nulla magna, malesuada vel, convallis','Nov 16, 2021',4,NULL,NULL),
  ('sit amet, consectetuer adipiscing elit. Etiam laoreet,','Dec 3, 2021',5,5,5);

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

insert into user_tag (user_id, tag_id) values (1, 1);
insert into user_tag (user_id, tag_id) values (1, 2);
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

insert into question_tag (question_id, tag_id) values (1, 7);
insert into question_tag (question_id, tag_id) values (1, 10);
insert into question_tag (question_id, tag_id) values (2, 2);
insert into question_tag (question_id, tag_id) values (3, 19);
insert into question_tag (question_id, tag_id) values (3, 20);
insert into question_tag (question_id, tag_id) values (3, 21);
insert into question_tag (question_id, tag_id) values (17, 8);
insert into question_tag (question_id, tag_id) values (2, 37);
insert into question_tag (question_id, tag_id) values (15, 32);
insert into question_tag (question_id, tag_id) values (9, 12);
insert into question_tag (question_id, tag_id) values (30, 30);
insert into question_tag (question_id, tag_id) values (30, 41);
insert into question_tag (question_id, tag_id) values (25, 27);
insert into question_tag (question_id, tag_id) values (26, 12);
insert into question_tag (question_id, tag_id) values (5, 8);
insert into question_tag (question_id, tag_id) values (6, 33);
insert into question_tag (question_id, tag_id) values (16, 41);
insert into question_tag (question_id, tag_id) values (5, 27);
insert into question_tag (question_id, tag_id) values (27, 23);
insert into question_tag (question_id, tag_id) values (9, 14);
insert into question_tag (question_id, tag_id) values (28, 7);
insert into question_tag (question_id, tag_id) values (17, 19);
insert into question_tag (question_id, tag_id) values (7, 1);
insert into question_tag (question_id, tag_id) values (10, 3);
insert into question_tag (question_id, tag_id) values (9, 23);
insert into question_tag (question_id, tag_id) values (24, 1);
insert into question_tag (question_id, tag_id) values (27, 34);
insert into question_tag (question_id, tag_id) values (13, 9);
insert into question_tag (question_id, tag_id) values (8, 29);
insert into question_tag (question_id, tag_id) values (29, 29);
insert into question_tag (question_id, tag_id) values (21, 29);
insert into question_tag (question_id, tag_id) values (25, 16);
insert into question_tag (question_id, tag_id) values (26, 38);
insert into question_tag (question_id, tag_id) values (19, 15);
insert into question_tag (question_id, tag_id) values (30, 31);
insert into question_tag (question_id, tag_id) values (21, 3);
insert into question_tag (question_id, tag_id) values (29, 20);
insert into question_tag (question_id, tag_id) values (29, 11);
insert into question_tag (question_id, tag_id) values (13, 23);
insert into question_tag (question_id, tag_id) values (4, 12);
insert into question_tag (question_id, tag_id) values (4, 30);
insert into question_tag (question_id, tag_id) values (18, 31);
insert into question_tag (question_id, tag_id) values (17, 9);
insert into question_tag (question_id, tag_id) values (11, 18);
insert into question_tag (question_id, tag_id) values (20, 23);
insert into question_tag (question_id, tag_id) values (26, 10);
insert into question_tag (question_id, tag_id) values (30, 24);

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
