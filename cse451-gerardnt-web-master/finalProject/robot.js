/*
   Scott Campbell
   cse451 final project
   This is a TEST file to invoke the robotDrawing.

   1) 
   create new robot:

   var bob  = new Robot("Bob","#ff0000") -> color at end is color to draw bob
   robotQueue.add(bob);

   update bob's position with an object:
   pos = {X:10,Y:10,orientation:"N"}
   robotQueue.updatePosition("Bob",pos);

   the robotDrawins page has its own timer for drawing the robots.



 */

var bob;
var alice;
var testPositions = [];
var max = 200

$(document).ready(() => {
	$('#errorMSG').hide();
	bob = new Robot("Bob", "#00acc1");
	alice = new Robot("Alice", "#f57c00");
	robotQueue.addRobot(bob);
	robotQueue.addRobot(alice);
	getRobotData();
	setInterval(update, 1000);
});

/*
This function calls to the 
API in order to set the Bob and 
Alice robot with updated information 
*/
 function getRobotData() {
	$.ajax({
		url: 'https://r8g38ilrgk.execute-api.us-east-1.amazonaws.com/Production/v1/robots',
		method: 'GET',
		success: (data) => {
			//loop through the data from the API and distribute it to either Alice or Bob depending on the data
			for (var i = 0; i < data.robots.length; i++) {
			 try {
				if (data.robots[i].name === 'alice') {
					//set Alices information
					testPositions[1] = { X: parseInt(data.robots[i].X), Y: parseInt(data.robots[i].Y), orientation: data.robots[i].orientation };
					//set the last move tile to when alice was last moved 
					$('#lastMoveAlice').html(data.robots[i].lastMove)
				}
				else {
					//set bobs information 
					testPositions[0] = { X: parseInt(data.robots[i].X), Y: parseInt(data.robots[i].Y), orientation: data.robots[i].orientation };
					//set the last move tile to when bob last moved
					$('#lastMoveBob').html(data.robots[i].lastMove)
				}
			}catch(e){
				//log the error instead of displaying an error message to the user due to how often the API is called
				console.log(e);
			}
			}
		},
		//log the error instead of displaying an error message to the user due to how often the API is called
		error:(error)=>{
			console.log(error);
		}
	});
}

function update() {
	for (i = 0; i < 2; i++) {
		if (testPositions[i].orientation == "E") {
			if (testPositions[i].X > max)
				testPositions[i].orientation = "N";	
		} else if (testPositions[i].orientation == "N") {
			if (testPositions[i].Y > max)
				testPositions[i].orientation = "W";
		}
		else if (testPositions[i].orientation == "W") {
			if (testPositions[i].X < 0)
				testPositions[i].orientation = "S";
		}
		else if (testPositions[i].orientation == "S") {
			if (testPositions[i].Y < 0)
				testPositions[i].orientation = "E";
		}
	}
	console.log("update");
	//call the update data method before updating bob and alice
	getRobotData();
	robotQueue.updateRobot("Bob", testPositions[0]);
	robotQueue.updateRobot("Alice", testPositions[1]);
}

