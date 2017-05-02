<?php

/**
 *
 * @param array $config The whole config
 * @return integer $jodel2vote The ID of the post to vote
 * @return string $how2vote "Up" or "down"
 *
 * @author Jonas Hüsser
 *
 * @SuppressWarnings(PHPMD.ElseExpression)
 *
 * @since 0.3
 */
function voteJodel($config, $jodel2vote, $how2vote){
	$apiroot = $config->apiUrl;
	//Get the post to upvote and users who voted this post
	$callurl = $apiroot . "jodels?transform=1&filter=jodelID,eq," . $jodel2vote;
	$jodeljson = getCall($callurl);
	$callurl = $apiroot . "jodelvotes?transform=1&filter=jodelIDFK,eq," . $jodel2vote;
	$votejson = getCall($callurl);
	$votes = json_decode($votejson,true);
	//Check if ID of the user already voted this post
	foreach($votes['jodelvotes'] as $vote){
		if($vote['userIDFK'] == $_SESSION['userid']){
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
		} elseif ($how2vote == "down"){
			$votes--;
			$score--;
		}
	}
	//Update votes & score of post in DB
	$postfields = "{\n  \n  \"votes_cnt\": $votes,\n  \"score\": $score\n}";
	$callurl = $apiroot . "jodels/" . $jodel2vote;
	$voted = putCall($callurl,$postfields);
	$userid = $_SESSION['userid'];
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

	//Get current karma of voter
	$callurl = $apiroot . "jodlers?transform=1&filter=jodlerID,eq," . $userid;
	$voterkarmajson = getCall($callurl);
	$voterkarma = json_decode($voterkarmajson, true);
	foreach($voterkarma['jodlers'] as $user){
		$voterKarma = $user['karma'];
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
		$voterKarma = $voterKarma + $config->karma_calc['do_upvote'];
	} elseif ($how2vote == "down"){
		$voterKarma = $voterKarma - $config->karma_calc['do_downvote'];
	}
	$postfields = "{\n  \n  \"karma\": $voterKarma\n}";
	$callurl = $apiroot . "jodlers/" . $userid;
	$karmaupdated = putCall($callurl, $postfields);

	} else {
		//user has already voted on this post
	$_SESSION['errorMsg'] = "Already voted";
}
//redirect again to jodels.php to show clean URL in browser
header('Location: https://jodel.domayntec.ch/jodels.php');


}


/**
 *
 * @param array $config The whole config
 * @return integer $comment2vote The ID of the comment to vote
 * @return string $how2vote "Up" or "down"
 *
 * @author Jonas Hüsser
 *
 * @SuppressWarnings(PHPMD.ElseExpression)
 *
 * @since 0.3
 */
function voteComment($config, $comment2vote, $how2vote){
	$userid = $_SESSION['userid'];
	$apiroot = $config->apiUrl;
	$commentsjson = getCall($apiroot . "comments?transform=1&filter=commentID,eq," . $comment2vote);
	$votejson = getCall($apiroot . "commentvotes?transform=1&filter=commentIDFK,eq," . $comment2vote);
	$votes = json_decode($votejson,true);
	foreach($votes['commentvotes'] as $vote){
		if($vote['jodlerIDFK'] == $userid){
			$voted = true;
		}
	}
	if(!$voted){
	$comment = json_decode($commentsjson, true);
	foreach($comment['comments'] as $post){
		$votes = $post['votes_cnt'];
		$score = $post['score'];
		$author = $post['jodlerIDFK'];
		if ($how2vote == "up"){
			$votes++;
			$score++;
		} elseif($how2vote == "down"){
			$votes--;
			$score--;
		}
	}
	$postfields = "{\n  \n  \"votes_cnt\": $votes,\n  \"score\": $score\n}";
	$voted = putCall("https://jodel.domayntec.ch/api.php/comments/" . $comment2vote,$postfields);

	$postfields = "{\n  \n  \"jodlerIDFK\": $userid,\n  \"commentIDFK\": $comment2vote\n}";
	$uservoted = postCall("https://jodel.domayntec.ch/api.php/commentvotes", $postfields);

	$authorkarmajson = getCall("https://jodel.domayntec.ch/api.php/jodlers?transform=1&filter=jodlerID,eq," . $author);
	$authorkarma = json_decode($authorkarmajson, true);
	foreach($authorkarma['jodlers'] as $user){
		$karmaFromAuthor = $user['karma'];
	}
	if($how2vote == "up"){
		$karmaFromAuthor = $karmaFromAuthor + $config->karma_calc['get_upvote'];
	} elseif($how2vote == "down"){
		$karmaFromAuthor = $karmaFromAuthor - $config->karma_calc['get_downvote'];
	}
	$postfields = "{\n  \n  \"karma\": $karmaFromAuthor\n}";
	$karmaupdated = putCall("https://jodel.domayntec.ch/api.php/jodlers/" . $author, $postfields);

	if($how2vote == "up"){
		$karma = $karma + $config->karma_calc['do_upvote'];
	} elseif($how2vote == "down"){
		$karma = $karma - $config->karma_calc['do_downvote'];
	}
	$postfields = "{\n  \n  \"karma\": $karma\n}";
	$karmaupdated = putCall("https://jodel.domayntec.ch/api.php/jodlers/" . $userid, $postfields);

	} else {
	$_SESSION['errorMsg'] = "Already voted";
}
header('Location: https://jodel.domayntec.ch/comments.php?showcomment=' .$postID);


}