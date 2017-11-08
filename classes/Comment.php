<?php
  class Comment {
    public static function createComment($commentBody,$postID,$userID) {

            if(strlen($commentBody)>260 || strlen($commentBody)<1){

              die('incorrect lenght');

            }

            if (!DB::query('SELECT id FROM posts WHERE id=:postid',array(':postid'=>$postID))) {
              echo "invalid postID";
            }
            else {
              DB::query('INSERT INTO comments (comment,user_id,posted_at,post_id) VALUES (:comment,:userid,NOW(),:postid)',array(':comment'=>$commentBody,':userid'=>$userID,':postid'=>$postID));
            }

  }



        public static function displayComments($postID) {
                $comments = DB::query('SELECT comments.comment, users.username FROM comments, users WHERE post_id = :postid AND comments.user_id = users.id', array(':postid'=>$postID));
                foreach($comments as $comment) {
                        echo $comment['comment']." ~ ".$comment['username']."<hr />";
                }
        }
}

?>
