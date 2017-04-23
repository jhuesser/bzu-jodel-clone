<?php

function voteJodel($jodel2vote, $how2vote){

	//Get the post to upvote and users who voted this post
	$callurl = $apiroot . "jodels?transform=1&filter=jodelID,eq," . $jodel2vote;
	$jodeljson = getCall($callurl);
	$callurl = $apiroot . "jodelvotes?transform=1&filter=jodelIDFK,eq," . $jodel2vote;
	$votejson = getCall($callurl);
	$votes = json_decode($votejson,true);
	//Check if ID of the user already voted this post
	foreach($votes['jodelvotes'] as $vote){
		if($vote['userIDFK'] == $userid){
			$voted = true;
		}
	}
	//If user hasn't voted for this post yet
	if(!$voted){
	$jodel = json_decode($jodeljson, true);
	//Get current votes, score and author of the post, add 1 to vote and score
	foreach($jodel['jodels'] as $post){
		$votes = $post['votes_cnt'];
		$score = $post['score'];
		$author = $post['jodlerIDFK'];
		if ($how2vote == "up"){
			$votes++;
			$score++;
		} elseif ($how2vote == 'down'){
			$votes--;
			$score--;
		}
	}
	//Update votes & score of post in DB
	$postfields = "{\n  \n  \"votes_cnt\": $votes,\n  \"score\": $score\n}";
	$callurl = $apiroot . "jodels/" . $jodel2vote;
	$voted = putCall($callurl,$postfields);

	//Wirte to DB, that this user now voted on this post
	$postfields = "{\n  \n  \"userIDFK\": $userid,\n  \"jodelIDFK\": $jodel2vote\n}";
	$callurl = $apiroot . "jodelvotes";
	$uservoted = postCall($callurl,$postfields);

	//Get current karma of post author
	$callurl = $apiroot . "jodlers?transform=1&filter=jodlerID,eq," . $author;
	$authorkarmajson = getCall($callurl);
	$authorkarma = json_decode($authorkarmajson, true);
	foreach($authorkarma['jodlers'] as $user){
		$karmaFromAuthor = $user['karma'];
	}

	//incerase karma of the author, update it in DB
	if ($how2vote == "up"){
		$karmaFromAuthor = $karmaFromAuthor + $config->karma_calc['get_upvote'];
	} elseif ($how2vote == "down"){
		$karmaFromAuthor = $karmaFromAuthor + $config->karma_calc['get_downvote'];
	}
	$postfields = "{\n  \n  \"karma\": $karmaFromAuthor\n}";
	$callurl = $apiroot . "jodlers/" . $author;
	$karmaupdated = putCall($callurl, $postfields);

	//incerase the karma of the voter (current user) and update it in DB
	if ($how2vote == "up"){
		$karmaFromAuthor = $karmaFromAuthor + $config->karma_calc['do_upvote'];
	} elseif ($how2vote == "down"){
		$karmaFromAuthor = $karmaFromAuthor - $config->karma_calc['do_downvote'];
	}
	$postfields = "{\n  \n  \"karma\": $karma\n}";
	$callurl = $apiroot . "jodlers/" . $userid;
	$karmaupdated = putCall($callurl, $postfields);

	} else {
		//user has already voted on this post
	$_SESSION['errorMsg'] = "Already voted";
}
//redirect again to jodels.php to show clean URL in browser
header('Location: https://jodel.domayntec.ch/jodels.php');


}

function voteComment($comment2Vote){


}