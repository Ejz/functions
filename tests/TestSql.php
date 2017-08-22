<?php

class TestSql extends PHPUnit_Framework_TestCase {
    public function testSql() {
        $host = getenv('SQL_HOST');
        $user = getenv('SQL_USER');
        $pass = getenv('SQL_PASS');
        $db = getenv('SQL_DB');
        SQL($host, $user, $pass, $db);
        SQL("
            CREATE TABLE `user` (
                `user_id` INTEGER PRIMARY KEY AUTO_INCREMENT NOT NULL,
                `user` VARCHAR(255) UNIQUE NOT NULL DEFAULT '',
                `city` VARCHAR(255) DEFAULT ''
            )
        ");
        $id = SQL("INSERT INTO user (user, city) VALUES ('Tristian', 'Chicago')");
        $this->assertTrue(is_numeric($id));
        $id = SQL("INSERT INTO user SET %s", array('user' => 'Ivan', 'city' => 'Moscow'));
        $this->assertTrue(is_numeric($id));
        $all = SQL("SELECT * FROM user WHERE city NOT IN (%s)", array('Chicago', 'New York'));
        $this->assertTrue(count($all) === 1);
        $n = SQL("UPDATE user SET city = %s", 'Moscow');
        $this->assertTrue($n == 1);
        $n = SQL("DELETE FROM user");
        $this->assertTrue($n == 2);
    }
}
