<?php

Include'Core/init.php';

if(Input::exists('get'))	// check if there's a query string or not
{	
	if(Input::get('user'))	// check if there's a query user with username 'user'
	{
		$username = Input::get('user');
		$writer = DB::getInstance()->get('users', array('username', '=', $username));
		if($writer->count() == 0)
		{
			Redirect::to(404);
			die();
		}
		else
		{
			$writer = $writer->first();
		}

		// $blogs_based_on_user = DB::getInstance()->SortByField('blogs', array('created_on', 'DESC'), array('users_id', '=', $writer->id));
		// $blog_count = $blogs_based_on_user->count();	// getting the total count of blogs based on the queried user
		// $blogs_based_on_user = $blogs_based_on_user->results();		// getting the id of blogs who have queried tag in them
	}
	else
	{
		Redirect::to('index.php');
	}
}
else
{
	Redirect::to('index.php');
}

?>

<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>

	<title>Profile- <?php echo $username;?></title>

	<link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.8/css/materialize.min.css">

	<style>
        body
        {
            background-color: #fafafa;
        } 
		.brand-logo
	    {
	        display: inline-block;
	        height: 100%;
	    }
	    .brand-logo > img 
	    {
	            vertical-align: middle
	    }
	    input[type="search"]
	    {
	        height: 64px !important; /* or height of nav */
	    }
	    nav ul .dropdown-button
	    {
	        width: 200px !important;
	    }
	    .user-profile-card-content
	    {
	    	padding-bottom: 20px !important;
	    }
	    .card .card-content
	    {
	        padding-bottom: 0px;
	        padding-top: 10px;
	    }
	    div .margin-eliminate
	    {
	        margin-bottom: 10px;
	    }
	    p .margin-eliminate
	    {
	        margin: 0px;
	    }        
		.user-profile-image
		{
			height: 100px !important;
			width: 100px !important;
			border: 0 solid black !important;
			border-radius: 50% !important;
		}
		.social-icons
		{
			margin-right:15px;
		}
	</style>

</head>

<body>
	<?php 

		include'header.php';

	?>

	<script type="text/javascript">
    	document.getElementById('nav-bar').classList.remove('transparent');
    </script>

    <div class="container">
		<div class="row">
        	<div class="col s12 l12">
          		<div class="card">
            		<div class="card-image">
              			<img src="Includes/images/user-profile-background.jpg">
              			<span class="card-title">
              				<img class="user-profile-image responsive-img" src="<?php echo Config::get('url/upload_dir').'/'.$writer->image_url ?>">
              			</span>              
            		</div>
            		<div class="card-content user-profile-card-content">
        				<h4><?php echo ucwords($writer->name); ?></h4>
        				<h6><?php echo $writer->username; ?></h6>
	              		<?php
	              			if(!empty($writer->user_description))
	              			{
	              				echo
	              				"<p>{$writer->user_description}</p>";
	              			}
	              			else
	              			{
	              				echo
	              				"<p> No description yet</p>";
	              			}
	              		?>
        			</div>
            		<div class="card-action">
            			<?php
            				if(!empty($writer->twitter_username))
            				{
              					echo
              					"<a href='www.twitter.com/{$writer->twitter_username}'><i class='fa fa-twitter fa-2x social-icons' aria-hidden='true' style='color:gray;'></i></a>";
            				}
            				if(!empty($writer->facebook_username))
            				{
              					echo
              					"<a href='www.twitter.com/{$writer->facebook_username}'><i class='fa fa-facebook fa-2x social-icons' aria-hidden='true' style='color:gray;'></i></a>";
            				}
            				if(!empty($writer->google_profileId))
            				{
              					echo
              					"<a href='www.twitter.com/{$writer->google_profileId}'><i class='fa fa-google-plus fa-2x social-icons' aria-hidden='true' style='color:gray;'></i></a>";
            				}
            				if(!empty($writer->github_username))
            				{
              					echo
              					"<a href='www.twitter.com/{$writer->github_username}'><i class='fa fa-github fa-2x social-icons' aria-hidden='true' style='color:gray;'></i></a>";
            				}
            			?>
            		</div>
          		</div>
    		</div>
      	</div>
      	<div class="section">
			<?php
				$blogs_based_on_user = DB::getInstance()->SortByField('blogs', array('created_on', 'DESC'), array('users_id', '=', $writer->id));
				$blog_count = $blogs_based_on_user->count();	// getting the total count of blogs based on the queried user
				$blogs_based_on_user = $blogs_based_on_user->results();		// getting the id of blogs who have queried tag in them

				if($blog_count == 0)
				{
					echo
					"<h5 class='red-text num-result'>No blogs written by <em> {$username} </em></h5>";
				}
				else
				{
					echo 
					"<div class='content'>";
	    				foreach($blogs_based_on_user as $blog_based_on_user)
	    				{
	    					$blog = DB::getInstance()->get('blogs', array('id', '=', $blog_based_on_user->id))->first();		// fetch blogs from table 'blogs' with blog_id of $blog_based_on_tag as parameter
		                    $blog_tags = DB::getInstance()->get('blog_tags', array('blog_id', '=', $blog->id));	// getting all blog_tags associated with the fetched blog
		                    $blog_tags = $blog_tags->results();
		                    $date=strtotime($blog->created_on); // changing the format of timestamp fetched from the database, converting it to milliseconds
		                    $writer = DB::getInstance()->get('users', array('id', '=', $blog->users_id))->first()->username;

		                    echo
	                        "<div class='col s12 m12'>
	                            <div class='card horizontal white'>
	                                <div class='card-content'> <span class='card-title'>".date('M d Y', $date)."</span>
	                                    <div class='row margin-eliminate'>
	                                        <div class='col s12'>
	                                            <h5><a class='views' data-attribute='{$blog->views}' href='".Config::get('url/endpoint')."/view_blog.php?blog_id={$blog->id}'".">".ucfirst($blog->title)."</a></h5>
	                                            <h6>".ucfirst($blog->description)."</h6>
	                                        </div>
	                                    </div>
	                                    <div class='row margin-eliminate'>  
	                                        <div class='valign-wrapper'>
	                                            <div class='col l6 s4'>
	                                                <div class='valign-wrapper'>
	                                                    <i class='material-icons hide-on-small-only' style='color:grey'>book</i>
	                                                    <p class='grey-text'>".$blog->blog_minutes_read." min read</p>
	                                                </div>
	                                            </div>
	                                            <div class='col l6 s8'>
	                                                <div class='chip'>
	                                                    <img src='Includes/images/og_image.jpg' alt='Contact Person'>{$writer}
	                                                </div>
	                                            </div>
	                                        </div>
	                                    </div>
	                                    <div class='row'>
	                                        <div class='measure-count' data-attribute='{$blog->id}'>
	                                            <div class='col s2 l1 m1'>
	                                                <i class='fa fa-eye fa-2x' aria-hidden='true' style='color:grey'></i>
	                                            </div>
	                                            <div class='col s1 l1 m1'>
	                                                {$blog->views}
	                                            </div>
	                                            <div class='col s2 l1 m1 offset-m1 offset-s1 offset-l1'>
	                                                <i class='fa fa-thumbs-up fa-2x' aria-hidden='true' style='color:grey'></i>
	                                            </div>
	                                            <div class='col s1 l1 m1'>
	                                                {$blog->likes}
	                                            </div>
	                                            <div class='col s2 l1 m1 offset-m1 offset-s1 offset-l1'>
	                                                <i class='fa fa-thumbs-down fa-2x' aria-hidden='true' style='color:grey'></i>
	                                            </div>
	                                            <div class='col s1 l1 m1'>
	                                                {$blog->dislikes}
	                                            </div>
	                                        </div>
	                                    </div>
	                                    <div class='row'>
	                                        <div class='col s12'>";
	                                        foreach($blog_tags as $blog_tag)
	                                        {
	                                            echo "<div class='chip'>".$blog_tag->tags."</div>";
	                                        }
	                                        echo
	                                        "</div>
	                                    </div>
	                                </div>
	                            </div>
	                        </div>";
	    				}
					echo
					"</div>";
				}
			?>
		</div>
    </div>

	<script src="Includes/js/jquery.min.js"></script>
    <script src="https://use.fontawesome.com/819d78ad52.js"></script>
    <script type="text/javascript" src="Includes/js/materialize.min.js"></script>
</body>
</html>