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
    is_admin BOOLEAN NOT NULL
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