-- Add columns in user table
ALTER TABLE wcf1_user ADD usermapLatitude FLOAT(10,7) NOT NULL DEFAULT 0.0;
ALTER TABLE wcf1_user ADD usermapLongitude FLOAT(10,7) NOT NULL DEFAULT 0.0;
ALTER TABLE wcf1_user ADD usermapLocation VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE wcf1_user ADD usermapTime INT(10) NOT NULL DEFAULT 0;

-- Add columns in usergroup table
ALTER TABLE wcf1_user_group ADD usermapMarker VARCHAR(255) NOT NULL DEFAULT 'marker_red.png';
ALTER TABLE wcf1_user_group ADD usermapFilter TINYINT(1) NOT NULL DEFAULT 1;
ALTER TABLE wcf1_user_group ADD usermapShow TINYINT(1) NOT NULL DEFAULT 1;

-- Usermap
DROP TABLE IF EXISTS usermap1_geocache;
CREATE TABLE usermap1_geocache (
	geocacheID		INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	hash			VARCHAR(32) NOT NULL,
	location		VARCHAR(255) NOT NULL,
	lat				FLOAT(10,7) NOT NULL,
	lng				FLOAT(10,7) NOT NULL,
	time			INT(10) NOT NULL,
	type			TINYINT(1) NOT NULL DEFAULT 0,
	
	UNIQUE KEY (hash)
);

DROP TABLE IF EXISTS usermap1_log;
CREATE TABLE usermap1_log (
	logID			INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	time			INT(10) NOT NULL DEFAULT 0,
	log				VARCHAR(255) NOT NULL DEFAULT '',
	remark			TEXT,
	status			TINYINT(1) DEFAULT 0,
	userID			INT(10),
	username 		VARCHAR(255) NOT NULL DEFAULT '',
);

ALTER TABLE usermap1_log ADD FOREIGN KEY (userID) REFERENCES wcf1_user (userID) ON DELETE SET NULL;