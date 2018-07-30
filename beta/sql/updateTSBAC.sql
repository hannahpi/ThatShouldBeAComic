Update `comments` c, `user` u set c.UserID = u.UserID Where c.Email = u.Email;
Update `images` i, `user` u set i.UserID = u.UserID Where i.Email = u.Email;
Update `chatter` ch, `user` u set ch.UserID = u.UserID Where ch.Email = u.Email;
Update `bio` b, `user` u b.UserID = u.UserID Where b.Email = u.Email;