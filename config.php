<?php

return (object) array(

	/*
	*
	*	Basic app settings
	*
	*/
    'baseUrl' => "ENTER_YOUR_APP_URL", //with ending slash
	'apiUrl' => "ENTER_URL_TO_MYSQL_REST_API", //with ending slash
	'recaptcha_sitekey' => "ENTER_RECAPTCHA_SITE_KEY",
	'recaptcha_secret' => "ENTER_RECAPTCHA_SECRET_KEY",
    'app_info' => array(
        'appName'=>"ENTER_APP_NAME",
        'appURL'=> "ENTER_APP_URL", //with ending slash
		'appVersion' => "ENTER_APP_VERSION",
		'appAuthor' => "ENTER_APP_AUTHOR",
		'appLang' => "ENTER_APP_LANGUGAGE"
    ),

	/*
	*
	*	Some vocabulary to change app wording for your needs
	*
	*
	*/ 
	'app_vocabulary' => array(
		'post' => "post",
		'posts' => "posts",
		'comment' => "comment",
		'comments' => "comments",
		'jodler' => "poster",
		'karma' => "flair",
		'superadmin' => "Herscher des Flairs",
		'admin' => "Hüter des Flairs",
		'mod' => 'Wächter des Flairs',
		'baned' => "burned user",
		'votes' => "votes",
		'latest' => "latest",
		'hotest' => "Hotest",
		'popular' => "Popular"
	),

	/*
	*
	*	How much karma you want to give for different acctions
	*
	*/
	'karma_calc' => array(
		// incerase karma
		'create_jodel' => 8,
		'post_comment' => 6,
		'get_comment' => 4,
		'do_upvote' => 2,
		'get_upvote' => 4,
		// decerase (is that english?) karma. - symbol is in code, do not add it here.
		'do_downvote' => 2,
		'get_downvote' => 4,
		'mod_deleted_post' => 10,
		'promote_mod' => 50000

	),

	'postmeta' => array(
		'needed_downvotes' => -5, //needed downvotes to hide the post
		'needed_score_mod' => 50,
		'get_upvote' => 10,
		'mod_approve' => 20,
		'get_comment' => 5,
		'get_downvote' => 10,
		'mod_deny' => 20,
		'get_report' => 50
	),

	'user_caps' => (object) array(
		'superadmin' => array(
			'delete_posts' => true,
			'mod_posts' => true,
			'delete_users' => true,
			'reset_paswd' => true,
			'change_karma' => true,
			'change_votes' => true,
			'promote_to_mod' => true,
			'promote_to_admin' => true,
			'promote_to_user' => true,
			'promote_to_superadmin' => true,
			'ban' => true,
			'add_color' => true,
			'change_post_score' => true,
			'edit_posts' => true,
			'delete_user_votes' => true,
			'manage_abuse' => true,
			'create_admin_notice' => true
		),

		'admin' => array(
			'delete_posts' => true,
			'mod_posts' => true,
			'delete_users' => false,
			'reset_paswd' => true,
			'change_karma' => false,
			'change_votes' => false,
			'promote_to_mod' => true,
			'promote_to_admin' => false,
			'promote_to_user' => true,
			'promote_to_superadmin' => false,
			'ban' => true,
			'add_color' => true,
			'change_post_score' => false,
			'edit_posts' => true,
			'delete_user_votes' => true,
			'manage_abuse' => true,
			'create_admin_notice' => true

	),

	'mod' => array(
			'delete_posts' => false,
			'mod_posts' => true,
			'delete_users' => false,
			'reset_paswd' => false,
			'change_karma' => false,
			'change_votes' => false,
			'promote_to_mod' => false,
			'promote_to_admin' => false,
			'promote_to_user' => false,
			'promote_to_superadmin' => false,
			'ban' => false,
			'add_color' => false,
			'change_post_score' => false,
			'edit_posts' => false,
			'delete_user_votes' => false,
			'manage_abuse' => false,
			'create_admin_notice' => false

	),

	'user' => array(
			'delete_posts' => false,
			'mod_posts' => false,
			'delete_users' => false,
			'reset_paswd' => false,
			'change_karma' => false,
			'change_votes' => false,
			'promote_to_mod' => false,
			'promote_to_admin' => false,
			'promote_to_user' => false,
			'promote_to_superadmin' => false,
			'ban' => false,
			'add_color' => false,
			'change_post_score' => false,
			'edit_posts' => false,
			'delete_user_votes' => false,
			'manage_abuse' => false,
			'create_admin_notice' => false

	),

	),

	/*
	*
	* 	Messages which will be shown by the app
	*
	*/
	'app_msgs' => array(
		'acc_created' => 'Account created. You can now go to <a href="login.php">Login</a>',
		'captcha_not_solved' => "Please solve the captcha!",
		'captcha_fail' => "Captcha failed!",
		'set_paswd' => "Please set a password!",
		'paswd_mismatch' => "Passwords don't match!",
		'nametaken' => "This username is taken, please use another",
		'general_error' => "Something didn't went well!",
		'login_fail' => "Username or password are incorrect!"
 
	),

	/*
	*
	*	Strings on login / signup page
	*
	*/
	'login_strings' => array(
		'title_signup' => "Welcome! Create your account",
		'title_login' => "Welcome Back! Please Sign In",
		'username' => "Username",
		'paswd' => "Password",
		'repeat_paswd' => "Repeat password",
		'signup' => "Register",
		'acc_exists' => 'Already have an account? <a href="login.php">Sign in!</a>',
		'login' => "Log in",
		'create_acc' => 'Don\'t have an account? <a href="signup.php">Create one!</a>'
	)
);