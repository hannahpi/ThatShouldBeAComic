Update `comments` c, `user` u set c.UserID = u.UserID Where c.Email = u.Email;
Update `images` i, `user` u set i.UserID = u.UserID Where i.Email = u.Email;
Update `chatter` ch, `user` u set ch.UserID = u.UserID Where ch.Email = u.Email;
Update `bio` b, `user` u set b.UserID = u.UserID Where b.Email = u.Email;
Update `licks` l, `user` u set l.UserID = u.UserID Where l.Email = u.Email;
ALTER TABLE `user` ADD `PassVer` DOUBLE NOT NULL DEFAULT '1.0' AFTER `Password`;
ALTER TABLE `user` CHANGE `PassVer` `PassVer` DOUBLE NOT NULL DEFAULT '2.0';
