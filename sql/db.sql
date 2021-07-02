USE demo_project;
DROP TABLE IF EXISTS files;
DROP TABLE IF EXISTS users;

CREATE TABLE IF NOT EXISTS users (
	id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
	username VARCHAR(24) NOT NULL,
	email VARCHAR(255) NOT NULL,
	password TEXT NOT NULL,
	confirmed TINYINT(1) DEFAULT 0 NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP() NOT NULL
);

CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
CREATE INDEX IF NOT EXISTS idx_users_con_cr_at ON users(confirmed, created_at);

CREATE TABLE IF NOT EXISTS files (
	id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
	user_id INT NOT null,
	CONSTRAINT `fk_files_user` FOREIGN KEY (user_id) REFERENCES users(id),
	name VARCHAR(255) NOT NULL,
	path TEXT NOT NULL,
	mimetype TEXT NOT NULL,
	size BIGINT NOT NULL,
	uploaded TINYINT(1) DEFAULT 0 NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP() NOT NULL
);

CREATE INDEX IF NOT EXISTS idx_files_user_id ON files(user_id);
CREATE INDEX IF NOT EXISTS idx_files_upl_cr_at ON files(uploaded, created_at);