<?php
  class Post {
    public static function createPost($postbody,$loggedIn_user,$profileUserId){


          if(strlen($postbody)>260 || strlen($postbody)<1){

            die('incorrect lenght');

          }


          if ($loggedIn_user == $profileUserId) {

                DB::query('INSERT INTO posts(body,posted_at,user_id) VALUES(:postbody, NOW(), :user_id)',array(':postbody'=>$postbody,':user_id'=>$profileUserId));
          }

          else {
                  die('Incorrect user!');

          }
        }


          public static function createImgPost($postbody, $loggedInUserId, $profileUserId) {
                if (strlen($postbody) > 160) {
                        die('Incorrect length!');
                }
                if ($loggedInUserId == $profileUserId) {
                        DB::query('INSERT INTO posts VALUES (\'\', :postbody, NOW(), :userid, 0, \'\')', array(':postbody'=>$postbody, ':userid'=>$profileUserId));
                        $postid = DB::query('SELECT id FROM posts WHERE user_id=:userid ORDER BY ID DESC LIMIT 1;', array(':userid'=>$loggedInUserId))[0]['id'];
                        return $postid;
                } else {
                        die('Incorrect user!');
                }
    }


    public static function likePost($postid,$likedUserID) {

      if(!DB::query('SELECT user_id FROM post_likes WHERE user_id=:userid AND post_id=:postid',array(':userid'=>$likedUserID,':postid'=>$postid))){

          DB::query('UPDATE posts set likes=likes+1 WHERE id = :postid',array(':postid'=>$postid));

          DB::query('INSERT INTO post_likes (post_id,user_id) VALUES (:postid,:userid)',array(':postid'=>$postid,':userid'=>$likedUserID));

        }

        else {

          DB::query('UPDATE posts set likes=likes-1 WHERE id = :postid',array(':postid'=>$postid));

          DB::query('DELETE FROM post_likes WHERE user_id=:userid AND post_id=:postid', array(':userid'=>$likedUserID,':postid'=>$postid));

        }

    }

    public static function link_add($text) {
                $text = explode(" ", $text);
                $newstring = "";
                foreach ($text as $word) {
                        if (substr($word, 0, 1) == "@") {
                                $newstring .= "<a href='profile.php?username=".substr($word, 1)."'>".htmlspecialchars($word)."</a> ";
                        } else {
                                $newstring .= htmlspecialchars($word)." ";
                        }
                }
                return $newstring;
        }




  public static function getPosts($userid,$username,$loggedInUserId) {

                    $dbposts = DB::query('SELECT * FROM posts WHERE user_id=:userid ORDER BY id DESC', array(':userid'=>$userid));
                    $posts = "";
                    foreach($dbposts as $p) {
                            if (!DB::query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$p['id'], ':userid'=>$loggedInUserId))) {
                                    $posts .= "<img src='".$p['postimg']."'>".self::link_add($p['body'])."
                                    <form action='profile.php?username=$username&postid=".$p['id']."' method='post'>
                                            <input type='submit' name='like' value='Like'>
                                            <span>".$p['likes']." likes</span>
                                    ";
                                    if ($userid == $loggedInUserId) {
                                            $posts .= "<input type='submit' name='deletepost' value='x' />";
                                    }
                                    $posts .= "
                                    </form><hr /></br />
                                    ";
                            } else {
                                    $posts .= "<img src='".$p['postimg']."'>".self::link_add($p['body'])."
                                    <form action='profile.php?username=$username&postid=".$p['id']."' method='post'>
                                    <input type='submit' name='unlike' value='Unlike'>
                                    <span>".$p['likes']." likes</span>
                                    ";
                                    if ($userid == $loggedInUserId) {
                                            $posts .= "<input type='submit' name='deletepost' value='x' />";
                                    }
                                    $posts .= "
                                    </form><hr /></br />
                                    ";
                            }
                    }
                    return $posts;
            }
}
?>
