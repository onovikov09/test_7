<?php

use yii\db\Migration;

class m171019_195602_init_tables extends Migration
{
    public function safeUp()
    {
        $sSql = <<<SQL

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nickname` varchar(255) NOT NULL,
  `balance` int(11) NOT NULL DEFAULT '0',
  `is_active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nickname` (`nickname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_user` int(11) NOT NULL,
  `to_user` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  `dt` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SQL;
        $this->execute($sSql);
    }

    public function safeDown()
    {
        $this->execute('DROP TABLE `history`; DROP TABLE `user`;');
    }
}
