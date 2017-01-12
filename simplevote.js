function submitVote(rate, articleId){		
	var form = document.getElementById("vote_form"+articleId);	
	form.getElementById("user_rating").value = rate
	form.submit();
}