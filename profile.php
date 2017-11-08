<?php
include ('classes/DB.php');
include('classes/Login.php');
include ('classes/Post.php');
include('./classes/Image.php');

$username = '';
$isFollowing = False;
$isVerified = False;

if(isset($_GET['username'])){
    if(DB::query('SELECT username FROM users WHERE username=:username',array(':username'=>$_GET['username']))) {

        $username = DB::query('SELECT username FROM users WHERE username=:username',array(':username'=>$_GET['username']))[0]['username'];

        $userid = DB::query('SELECT id from users where username=:username',array(':username'=>$_GET['username']))[0]['id'];

        $followerid = Login::isLoggedIn();

        $isVerified = DB::query('SELECT verified FROM users WHERE username=:username',array(':username'=>$_GET['username']))[0]['verified'];

        if(isset($_POST['follow'])){

          if ($userid != $followerid) {

            if(!DB::query('SELECT follower_id FROM followers WHERE user_id = :userid AND follower_id = :followerid',array(':userid'=>$userid,':followerid'=>$followerid))) {

                DB::query('INSERT INTO followers (user_id,follower_id) VALUES(:userid,:followerid)',array(':userid'=>$userid,':followerid'=>$followerid));
            }

            else{

              echo 'Already Following';

            }

            $isFollowing = True;

          }

        }

        if(isset($_POST['unfollow'])){

          if ($userid != $followerid) {

            if(DB::query('SELECT follower_id FROM followers WHERE user_id = :userid AND follower_id = :followerid',array(':userid'=>$userid,':followerid'=>$followerid))){

                DB::query('DELETE FROM followers WHERE user_id = :userid AND follower_id = :followerid',array(':userid'=>$userid,':followerid'=>$followerid));

            }

            $isFollowing = False;

        }

      }


        if (DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))) {

                      //echo 'Already following!';
                      $isFollowing = True;

      }

      if (isset($_POST['deletepost'])) {
                      if (DB::query('SELECT id FROM posts WHERE id=:postid AND user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$followerid))) {
                              DB::query('DELETE FROM posts WHERE id=:postid and user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$followerid));
                              DB::query('DELETE FROM post_likes WHERE post_id=:postid', array(':postid'=>$_GET['postid']));
                              echo 'Post deleted!';
                      }
              }


      if (isset($_POST['post'])) {

            if ($_FILES['postimg']['size'] == 0) {
                                Post::createPost($_POST['postbody'], Login::isLoggedIn(), $userid);
                                header('Location: '.$_SERVER['REQUEST_URI']);
              } else {
                            $postid = Post::createImgPost($_POST['postbody'], Login::isLoggedIn(), $userid);
                            Image::uploadImage('postimg', "UPDATE posts SET postimg=:postimg WHERE id=:postid", array(':postid'=>$postid));
                            header('Location: '.$_SERVER['REQUEST_URI']);

                }

        }

        if(isset($_GET['postid']) && !isset($_POST['deletepost'])){
          Post::likePost($_GET['postid'],$followerid);
        }


        $posts = Post::getPosts($userid,$username,$followerid);

      }

    else {

      die("user doesn't exist");

      exit();
    }

  }

  //TODO Add a function to change verified to 1
  //TODO Follow button disappears after posting

?>

<!-- <h1><?php echo $username; ?></h1>
<h4><?php if($isVerified) {echo '- verified user'; } ?></h4>
<form action="profile.php?username=<?php echo $username; ?>" method="post"> -->
<?php
    if ($userid != $followerid) {
    if(!$isFollowing) {
      echo '<input type="submit" name="follow" value="Follow">';
    } else {
      echo '<input type="submit" name="unfollow" value="Unfollow">';
    }
  }
?>
</form>
<!--
<form action="profile.php?username=<?php echo $username; ?>" method="post" enctype="multipart/form-data">
        <textarea name="postbody" rows="8" cols="80"></textarea>
        <br />Upload an image:
        <input type="file" name="postimg">
        <input type="submit" name="post" value="Post">
</form>

<div class="posts">
  <?php echo $posts; ?>

</div>
 -->




<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pixter</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/fonts/ionicons.min.css">
    <link rel="stylesheet" href="assets/css/Footer-Dark.css">
    <link rel="stylesheet" href="assets/css/Highlight-Clean.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.1.1/aos.css">
    <link rel="stylesheet" href="assets/css/Login-Form-Clean.css">
    <link rel="stylesheet" href="assets/css/Navigation-Clean1.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/untitled.css">
</head>

<body>
    <header class="hidden-sm hidden-md hidden-lg">
        <div class="searchbox">
            <form>
                <h1 class="text-left">Pixter</h1>
                <div class="searchbox"><i class="glyphicon glyphicon-search"></i>
                    <input class="form-control" type="text">
                </div>
                <div class="dropdown">
                    <button class="btn btn-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false" type="button">MENU <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                        <li role="presentation"><a href="#">My Profile</a></li>
                        <li class="divider" role="presentation"></li>
                        <li role="presentation"><a href="index.php">Timeline </a></li>
                        <li role="presentation"><a href="#">My Account</a></li>
                        <li role="presentation"><a href="#" class="logout">Logout </a></li>
                    </ul>
                </div>
            </form>
        </div>
        <hr>
    </header>
    <div>
        <nav class="navbar navbar-default hidden-xs navigation-clean">
            <div class="container">
                <div class="navbar-header"><a class="navbar-brand navbar-link" href="#"><i class="icon ion-ios-navigate"></i></a>
                    <button class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navcol-1"><span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>
                </div>
                <div class="collapse navbar-collapse" id="navcol-1">
                    <form class="navbar-form navbar-left">
                        <div class="searchbox"><i class="glyphicon glyphicon-search"></i>
                            <input class="form-control" type="text">
                        </div>
                    </form>
                    <ul class="nav navbar-nav hidden-md hidden-lg navbar-right">
                        <li role="presentation"><a href="index.php">My Timeline</a></li>
                        <li class="dropdown open"><a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true" href="#"><?php echo $username; ?> <span class="caret"></span></a>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                <li role="presentation"><a href="#">My Profile</a></li>
                                <li class="divider" role="presentation"></li>
                                <li role="presentation"><a href="index.php">Timeline </a></li>
                                <li role="presentation"><a href="#">My Account</a></li>
                                <li role="presentation"><a href="#" class="logout">Logout </a></li>
                            </ul>
                        </li>
                    </ul>
                    <ul class="nav navbar-nav hidden-xs hidden-sm navbar-right">
                        <li role="presentation"><a href="index.php">Timeline</a></li>
                        <li class="active" role="presentation"><a href="#">Profile</a></li>
                        <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false" href="#"><?php echo $username; ?> <span class="caret"></span></a>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                <li role="presentation"><a href="#">My Profile</a></li>
                                <li class="divider" role="presentation"></li>
                                <li role="presentation"><a href="index.php">Timeline </a></li>
                                <li role="presentation"><a href="#">My Account</a></li>
                                <li role="presentation"><a href="#" class="logout">Logout </a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
    <div class="container">
        <h1><?php echo $username; ?><?php if($isVerified){echo '<i class="glyphicon glyphicon-ok-sign verified" data-toggle="tooltip" title="Verified User" style="font-size:28px;color:#da052b;"></i>';} ?></h1></div>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <ul class="list-group">
                        <li class="list-group-item"><span><strong>About Me</strong></span>
                            <p>Welcome to <?php echo $username; ?>'s profile</p>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-group">
                      <div class="timelineposts">

                      </div>
                    </ul>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-default" id="newPost" type="button" style="width:100%;background-image:url(&quot;none&quot;);background-color:#da052b;color:#fff;padding:16px 32px;margin:0px 0px 6px;border:none;box-shadow:none;text-shadow:none;opacity:0.9;text-transform:uppercase;font-weight:bold;font-size:13px;letter-spacing:0.4px;line-height:1;outline:none;" >NEW POST</button>
                    <ul class="list-group"></ul>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" role="dialog" tabindex="-1" style="padding-top:100px;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">Comments</h4></div>
                <div class="modal-body" style="max-height: 400px; overflow-y: auto">
                    <p>The content of your modal.</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" type="button" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-dark" style="postion: relative">
        <footer>
            <div class="container">
                <p class="copyright">Pixter© 2017</p>
            </div>
        </footer>
    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/bs-animation.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.1.1/aos.js"></script>
    <script type="text/javascript">

        $(document).ready(function() {
                $.ajax({

                        type: "GET",
                        url: "api/profileposts?username=<?php echo $username; ?>",
                        processData: false,
                        contentType: "application/json",
                        data: '',
                        success: function(r) {
                                var posts = JSON.parse(r)
                                $.each(posts, function(index) {
                                  if(posts[index].PostImage == "") {

                                  $('.timelineposts').html(
                                          $('.timelineposts').html() +
                                              '<li class="list-group-item"><blockquote><p>'+posts[index].PostBody+'</p><footer>Posted by '+posts[index].PostedBy+' on '+posts[index].PostDate+'<button class="btn btn-default" type="button" style="color:#eb3b60;background-image:url(&quot;none&quot;);background-color:transparent;" data-id=\"'+posts[index].PostId+'\"> <i class="glyphicon glyphicon-heart" data-aos="flip-right"></i><span> '+posts[index].Likes+' Likes</span></button><button class="btn btn-default comment" data-postid=\"'+posts[index].PostId+'\" type="button" style="color:#eb3b60;background-image:url(&quot;none&quot;);background-color:transparent;"><i class="glyphicon glyphicon-flash" style="color:#f9d616;"></i><span style="color:#f9d616;"> Comments</span></button></footer></blockquote></li>'
                                  )
                                }
                                else {

                                  $('.timelineposts').html(
                                          $('.timelineposts').html() +
                                              '<li class="list-group-item"><blockquote><p>'+posts[index].PostBody+'</p><img src="" data-imagesrc="'+posts[index].PostImage+'" class="postimg" id="img'+posts[index].PostId+'"><footer>Posted by '+posts[index].PostedBy+' on '+posts[index].PostDate+'<button class="btn btn-default" type="button" style="color:#eb3b60;background-image:url(&quot;none&quot;);background-color:transparent;" data-id=\"'+posts[index].PostId+'\"> <i class="glyphicon glyphicon-heart" data-aos="flip-right"></i><span> '+posts[index].Likes+' Likes</span></button><button class="btn btn-default comment" data-postid=\"'+posts[index].PostId+'\" type="button" style="color:#eb3b60;background-image:url(&quot;none&quot;);background-color:transparent;"><i class="glyphicon glyphicon-flash" style="color:#f9d616;"></i><span style="color:#f9d616;"> Comments</span></button></footer></blockquote></li>'
                                            )
                                }

                                        $('[data-postid]').click(function() {
                                                var buttonid = $(this).attr('data-postid');

                                                $.ajax({

                                                        type: "GET",
                                                        url: "api/comments?postid=" + $(this).attr('data-postid'),
                                                        processData: false,
                                                        contentType: "application/json",
                                                        data: '',
                                                        success: function(r) {
                                                                var res = JSON.parse(r)
                                                                showCommentsModal(res);
                                                        },
                                                        error: function(r) {
                                                                console.log(r)
                                                        }

                                                });
                                        });

                                        $('[data-id]').click(function() {
                                                var buttonid = $(this).attr('data-id');
                                                $.ajax({

                                                        type: "POST",
                                                        url: "api/likes?id=" + $(this).attr('data-id'),
                                                        processData: false,
                                                        contentType: "application/json",
                                                        data: '',
                                                        success: function(r) {
                                                                var res = JSON.parse(r)
                                                                $("[data-id='"+buttonid+"']").html(' <i class="glyphicon glyphicon-heart" data-aos="flip-right"></i><span> '+res.Likes+' Likes</span>')
                                                        },
                                                        error: function(r) {
                                                                console.log(r)
                                                        }

                                                });
                                        })
                                })

                                $('.postimg').each(function(){
                                  this.src=$(this).attr('data-imagesrc')
                                  this.onload = function() {
                                    this.style.opacity = '1';
                                  }

                                })

                        },

                        error: function(r) {
                                console.log(r)
                        }

                });

        });


        $('.logout').click(function(){
          $('.modal').modal('show')
          $('.modal-title').text(" " +'Logout')
          var res = '<form action="index.php" method="post"><input type="checkbox" name="alldevices">Sign out of All Devices</br><button type="button" class="btn btn-primary" id="logout-btn">Logout</button></form>'
          var button = '<button class="btn btn-default" type="button" data-dismiss="modal">Close</button>'
          $('.modal-body').html(res)
          $('.modal-footer').html(button)
          var check = 0
          $('#logout-btn').click(function(){
            if($('input[name="alldevices"]').prop("checked")==true){
              check = 1
          }
                $.ajax({

                    type: "DELETE",
                    url: "api/auth?check="+check,
                    processData: false,
                    contentType: "application/JSON",
                    data: '',
                    success: function(){
                      window.location.href = "login.html"
                    },
                    error: function (resp){
                      alert(resp)
                    }


                });

          });

        });

        $('#newPost').click(function(){
          var res = '<form action="profile.php?username=<?php echo $username; ?>" method="post" enctype="multipart/form-data"><textarea name="postbody" rows="8" cols="70"></textarea><br />Upload an image:<input type="file" name="postimg"><input type="submit" name="post" value="Post"></form>';
          // var button = ''
          $('.modal').modal('show')
          $('.modal-title').text(" " +'New post')
          $('.modal-body').html(res)
          // $('.modal-footer').html(button)

        });

        function showCommentsModal(res) {
                $('.modal').modal('show')
                $('.modal-title').text(" " +'Comments')
                var button = '<button class="btn btn-default" type="button" data-dismiss="modal">Close</button>'
                var output = "";
                for (var i = 0; i < res.length; i++) {
                        output += res[i].Comment;
                        output += " ~ ";
                        output += res[i].CommentedBy;
                        output += "<hr />";
                }

                $('.modal-body').html(output)
                $('.modal-footer').html(button)

        }



    </script>
</body>

</html>
