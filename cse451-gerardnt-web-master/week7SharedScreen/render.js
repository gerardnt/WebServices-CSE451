/*
 * Nicholas Gerard
 * CSE 451
 * 3/18/20
 * Week 7 shared screen Assignment
 * */

var r=g=b=0;

function handleClick(evt) {
	x=evt.offsetX;
	y=evt.offsetY;
	
	//check to see which dropdown size is selected and adjust size of block accordignly 
	var input = $("#size").val();

      if(input === 'point'){
        
	   var data = JSON.stringify({x:x,y:y,x1:(x+10),y1:(y+10),r:r,g:g,b:b});
	   var x1 = x+10;
	   var y1 = y+10;

	 }
      else{
	
	   var data = JSON.stringify({x:x,y:y,x1:(x+40),y1:(y+40),r:r,g:g,b:b});
	   var x1 = x+40;
	   var y1 = y+40; 

	}
	console.log("x=" + x + "," + y);
	
	//add checks to make sure the 4x4 block stays in range
	if (x<390 && y<390 && x1<=410 && y1<=410  ) {
		$.ajax({ 
			url:'screenAPI.php/',
			method: 'post',
			data: data
		});
	}
}

//gets the current users who are accessing the page 
function getCurrentUsers(){

	$("#users ul").html('');

	$.ajax({
		url:'screenAPI.php/v1/users',
		method:'get',
		success:function(data){

			$(data).each(function(index, value) {
				
			for(var i = 0; i < value.users.length ; i ++){

				if(value.users[i].owner !== null && value.users[i].owner !== undefined ){
					//apend each current user to the list 
					$("#users ul").append('<li>'+value.users[i].owner+'</li>');
				}
			}

			});
		},
		error:function(jqXHR, textStatus, errorThrown){
		
			$("#users ul").append("<div class='alert alert-warning' role='alert'> Error getting Current Users </div>");
			
		}
	});
	
	
	
}

$(document).ready(function() {
	//setup colors for this user
	r = Math.floor(Math.random()*255);
	g = Math.floor(Math.random()*255);
	b = Math.floor(Math.random()*255);

        getCurrentUsers();

	$("#msg").append("Your color is  = " + r + " " + g + " " + b);
	
	// refresh image every second-->
	window.setInterval(() => {
		var d = new Date();
		//append the current time to get around caching. Also append the user requesting the image to show they are a current user
		$("img").attr("src",'makeCommonImage.php?t='+d.getTime()+'&uid='+user);
	},300);

	window.setInterval(()=>{

	getCurrentUsers();

	},1000);

	<!-- on click, get coordinate and add point-->
		$("#pic").click(handleClick)
	$("#clear").click(() => {
		$.ajax({
			url: 'screenAPI.php',
			method: 'delete'
		});
	});
});
