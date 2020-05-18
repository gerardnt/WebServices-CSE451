/*
Nicholas Gerard
CSE 451 Spring 2020
Final Project 

This program handles the Alexa interactions with the 
Robots and updates the database with the new information. 

*/

const Alexa = require('ask-sdk');
var AWS = require("aws-sdk");

AWS.config.update({
    region: "us-east-1",
});
var docClient = new AWS.DynamoDB.DocumentClient();



/*

This function handles the user requesting positional data about a 
specific robot.

*/
const robotPositionHandler = {
    canHandle(handlerInput) {
        return handlerInput.requestEnvelope.request.type === 'IntentRequest'
            && handlerInput.requestEnvelope.request.intent.name === 'robotPositionIntent';
    },
    async handle(handlerInput) {
        //the text to be returned 
        let speechText;
        try {
            //get the robot name stated by the user
            const name = Alexa.getSlotValue(handlerInput.requestEnvelope, 'name').toLowerCase();

            //make sure the name is either bob or alice
            if (name === 'alice' || name === 'bob') {

                //call to get the robot information
                var data = await getRobotData(name);

                //create variables to be used in the response of the positional data
                let orientation = data.Item.orientation;
                let xPos = data.Item.X;
                let yPos = data.Item.Y;

                //if the robot is facing north
                if (orientation === 'N') {
                    speechText = `I am at position ${xPos} by ${yPos} facing North. I can move ${199 - yPos} units forward`;
                }
                //if the robot is facing south
                else if (orientation === 'S') {
                    speechText = `I am at position ${xPos} by ${yPos} facing South. I can move ${yPos} units forward`;
                }
                //if the robot is facing east
                else if (orientation === 'E') {
                    speechText = `I am at position ${xPos} by ${yPos} facing East. I can move ${199 - xPos} units forward`;
                }
                //if the robot is facing west
                else {
                    speechText = `I am at position ${xPos} by ${yPos} facing West. I can move ${xPos} units forward`;
                }
            }
            //if the robot name stated by the user was invalid
            else {
                speechText = 'I was unable to recognize the name. Please try again with the robot names of either Bob or Alice.';
            }
        }
        catch (error) {
            console.error("Unable to query. Error:", JSON.stringify(error, null, 2));
            speechText = 'I was unable to recognize the name. Please try again with the robot names of either Bob or Alice.';

        }
        //response returned by Alexa
        return handlerInput.responseBuilder
            .speak(speechText)
            .withSimpleCard('Robots Position', speechText)
            .withShouldEndSession(true)
            .getResponse();
    }
};


/*
This function handles the users intent on turning a 
specific robot on the board
*/
const robotTurnHandler = {
    canHandle(handlerInput) {
        return handlerInput.requestEnvelope.request.type === 'IntentRequest'
            && handlerInput.requestEnvelope.request.intent.name === 'robotTurnIntent';
    },
    async handle(handlerInput) {
        //the returned text
        let speechText;
        try {

            //the name of the robot stated by the user and the direction to turn the robot
            const name = Alexa.getSlotValue(handlerInput.requestEnvelope, 'name').toLowerCase();
            const direction = Alexa.getSlotValue(handlerInput.requestEnvelope, 'direction');

            console.log(name);
            console.log(direction);

            //make sure that the name is either bob or alice and that the direction to turn is left or right
            if ((name == 'alice' || name == 'bob') && direction !== undefined && (direction === 'left' || direction === 'right')) {

                //get the robots information from the DB
                var data = await getRobotData(name);

                let orientation = data.Item.orientation;
                let newDirection;

                //if the robot is facing north a left turn would make it 
                //west and a right turn would make it east
                if (orientation === 'N') {
                    if (direction === 'left') {
                        newDirection = 'W';
                    }
                    else if (direction === 'right') {
                        newDirection = 'E';
                    }

                }
                //if the robot is facing south a left turn would make it 
                //east and a right turn would make it west
                else if (orientation === 'S') {
                    if (direction === 'left') {
                        newDirection = 'E';
                    }
                    else if (direction === 'right') {
                        newDirection = 'W';
                    }

                }
                //if the robot is facing east a left turn would make it 
                //north and a right turn would make it south
                else if (orientation === 'E') {
                    if (direction === 'left') {
                        newDirection = 'N';
                    }
                    else if (direction === 'right') {
                        newDirection = 'S';
                    }
                }
                //if the robot is facing west a left turn would make it 
                //south and a right turn would make it north
                else {
                    if (direction === 'left') {
                        newDirection = 'S';
                    }
                    else if (direction === 'right') {
                        newDirection = 'N';
                    }
                }

                //send the updated information to the db
                let updated = await updateOrientation(name, newDirection);

                //if there is an error set the returned text to the error
                //if there is no error return ok
                if (updated === 'error') {
                    speechText = 'There was a problem updating the orientation of the robot. Please Try again.';
                }
                else {
                    speechText = 'ok';

                }
            }
            //if the name is not bob or alice and or the direction is not left or right
            else {
                speechText = 'I was unable to recognize the name or direction. Please try again with the robot names of either Bob or Alice and the directions of left or right.';

            }
        }
        catch (error) {
            console.error("Unable to query. Error:", JSON.stringify(error, null, 2));
            speechText = 'I was unable to recognize the name. Please try again with the robot names of either Bob or Alice.';

        }
        //alexa's response 
        return handlerInput.responseBuilder
            .speak(speechText)
            .withShouldEndSession(true)
            .getResponse();
    }
};


/*
This function handles the intent for the robots to moved

*/
const robotMoveHandler = {
    canHandle(handlerInput) {
        return handlerInput.requestEnvelope.request.type === 'IntentRequest'
            && handlerInput.requestEnvelope.request.intent.name === 'robotMoveIntent';
    },
    async handle(handlerInput) {
        //the speech to be returned
        let speechText;
        try {

            // the name direction and units input by the user for the robot to move
            const name = Alexa.getSlotValue(handlerInput.requestEnvelope, 'name').toLowerCase();
            const direction = Alexa.getSlotValue(handlerInput.requestEnvelope, 'movement').toLowerCase();
            let units = parseInt(Alexa.getSlotValue(handlerInput.requestEnvelope, 'units'));

            console.log(name);
            console.log(direction);

            //make sure that the name is either bob or alice and that the direction is valid
            if ((name == 'alice' || name == 'bob') && direction !== undefined && (direction === 'forward' || direction === 'backwards' || direction === 'forwards' || direction === 'backward')) {

                //if the user does not specify how much to move it defaults to one
                if (units === undefined || isNaN(units)) {
                    units = 1;
                }

                var otherRobot;
                var robotToMove;

                //get the information of both robots and name
                //them based on which one is trying to be moved 
                if (name === 'alice') {
                    or = await getRobotData('bob');
                    otherRobot = or.Item;
                    rm = await getRobotData('alice');
                    robotToMove = rm.Item;

                } else {
                    or = await getRobotData('alice');
                    otherRobot = or.Item;
                    rm = await getRobotData('bob');
                    robotToMove = rm.Item;
                }

                console.log('robot to move data', robotToMove);
                console.log('other robot on the board ', otherRobot);

                //call to a method that makes sure that the move is a legal move 
                var response = moveToPosition(robotToMove, otherRobot, direction, units);

                //if the response returns the number the coordinate that should be changed 
                //then we call to our actual update DB method
                if (!isNaN(response)) {
                    //if the value is north or south we move in the y position
                    if (robotToMove.orientation === 'N' || robotToMove.orientation === 'S') {

                        var proccessed = await moveRobotsPosition(name, robotToMove.X, response);

                        //if there was a DB update error we set the speechText to inform the user
                        if (proccessed === 'error') {
                            speechText = 'There was a problem updating the position of the robot. Please Try again.';
                        }
                        //If the DB updated successfully we tell the user ok
                        else {
                            speechText = 'ok';
                        }
                    }
                    //if the orientation is east or west we move in the x position 
                    else {

                        var proccessed = await moveRobotsPosition(name, response, robotToMove.Y);

                        //if there was a DB update error we set the speechText to inform the user
                        if (proccessed === 'error') {
                            speechText = 'There was a problem updating the position of the robot. Please Try again.';
                        }
                        //If the DB updated successfully we tell the user ok
                        else {
                            speechText = 'ok';
                        }
                    }
                }
                //if the returned response from the validation checking is not the number
                //we set the text to be said by Alexa as the error found
                else {

                    speechText = response;
                }
            }
            //if the name of the robots are invalid and not Bob or Alice
            else {
                speechText = 'I was unable to recognize the command. Please make sure the robot names that are used are Bob or Alice and that the direction to move is forward or backwards.';
            }
        }
        catch (error) {
            console.error("Unable to query. Error:", JSON.stringify(error, null, 2));
            speechText = 'I was unable to recognize the name. Please try again with the robot names of either Bob or Alice.';

        }

        //Alexa's response
        return handlerInput.responseBuilder
            .speak(speechText)
            .withShouldEndSession(true)
            .getResponse();
    }
};

/*
input: 
    start- the start position of the robot trying to move
    potentialObstruction- the other robots position that has the potential of being in the way
    final - the location that the robot trying to move is trying to move to 

This function tells wether or not the robot that is not moving is in the way of the robot trying to move
*/
function isObstruction(start, potentialObstruction, final) {
    //if we are going from a low number to a high number
    if (start < final) {
        //if the potentialObstruction is greater than the start value and less than or equal to the final value then it is in the way
        if (start < potentialObstruction && final >= potentialObstruction) {
            return true;
        }
        //the Obstruction is not in the way
        else {
            return false;
        }
    }
    //if we are going from a large number to a lower
    else {
        //if the potentialObstruction is less than the start value and greater than or equal to the final value then it is in the way
        if (start > potentialObstruction && final <= potentialObstruction) {
            return true;
        }
        //the obstruction is not in the way
        else {
            return false;
        }
    }
}


/*
This function handles if the user asks for help to alexa
It will tell the user what they are able to do
*/
const HelpIntentHandler = {
    canHandle(handlerInput) {
        return handlerInput.requestEnvelope.request.type === 'IntentRequest'
            && (handlerInput.requestEnvelope.request.intent.name === 'AMAZON.HelpIntent'
                || Alexa.getIntentName(handlerInput.requestEnvelope) === 'AMAZON.YesIntent'
                || Alexa.getIntentName(handlerInput.requestEnvelope) === 'AMAZON.NoIntent');
    },
    handle(handlerInput) {
        const speechText = 'You can ask me to move robots Bob or Alice along the playing area.';

        return handlerInput.responseBuilder
            .speak(speechText)
            .withShouldEndSession(true)
            .withSimpleCard('Robots', speechText)
            .getResponse();
    }
};


/*
This function handles if the user stops or cancels the 
robot interactions
*/
const CancelAndStopIntentHandler = {
    canHandle(handlerInput) {

        return handlerInput.requestEnvelope.request.type === 'IntentRequest'
            && (handlerInput.requestEnvelope.request.intent.name === 'AMAZON.CancelIntent'
                || handlerInput.requestEnvelope.request.intent.name === 'AMAZON.StopIntent');
    },
    handle(handlerInput) {

        const speechText = 'Good Bye!';

        return handlerInput.responseBuilder
            .speak(speechText)
            .withSimpleCard('Robots', speechText)
            .withShouldEndSession(true)
            .getResponse();
    }
};

/*
This function handles the errors with the robot interactions between the 
user and Alexa
*/
const ErrorHandler = {
    canHandle() {
        return true;
    },
    handle(handlerInput, error) {
        console.log(`Error handled: ${error.message}`);

        return handlerInput.responseBuilder
            .speak('Sorry, I can\'t understand the command. Please say again.')
            .reprompt('Sorry, I can\'t understand the command. Please say again.')
            .getResponse();
    },
};

/*
This function catches any intent requests that were missed
*/
const FallbackHandler = {
    canHandle(handlerInput) {

        return Alexa.getRequestType(handlerInput.requestEnvelope) === 'IntentRequest'
            && Alexa.getIntentName(handlerInput.requestEnvelope) === 'AMAZON.FallbackIntent';
    },
    handle(handlerInput) {
        console.log(handlerInput);
        return handlerInput.responseBuilder
            .speak('I did not recognize the command make sure it is valid and try again.')
            .withShouldEndSession(true)
            .getResponse();
    },
};


/*
input: 
    name- the name of the given robot

This function returns a promise with the data for a robot based off the 
given name in the parameters
*/
function getRobotData(name) {
    return new Promise((resolve, reject) => {

        //create the parameters to get the requested robot
        let params = {
            TableName: "robots",
            Key: {
                "name": name
            }
        };

        console.log('Getting robot information with', params);
        //call the update function and if successful an ok is returned if not an error is returned
        docClient.get(params, function (err, data) {
            if (err) {
                console.error("Unable to query. Error:", err);
                reject('error');
            }
            else {
                console.log("This is the data from the promise ", data);
                resolve(data);
            }
        });
    })
}



/*
input:
    robotName- the name of the robot whose position is going to be updated
    x- the x position of the robot
    y - the y position of the robot

this function returns a promise that updates the db based on the given robotName. It updates
the X and Y values in the db to the ones provided in the parameter
*/
function moveRobotsPosition(robotName, x, y) {
    return new Promise((resolve, reject) => {

        //Create a new date object to signify when the robot was last updated
        var date = new Date();
        //My code is hosted on a box 4 hours ahead then my actual time, so we adjust
        date.setHours(date.getHours() - 4);
        var localDate = date.toLocaleString();

        //create the parameters for the updated robot position 
        var params = {
            TableName: "robots",
            Key: {
                "name": robotName
            },
            UpdateExpression: 'SET X = :x, Y = :y, lastMove = :date ',
            ExpressionAttributeValues: {
                ':x': x,
                ':y': y,
                ':date': localDate
            }
        };

        console.log('Updating robots orientation with ', params);
        //call the update function and if successful an ok is returned if not an error is returned
        docClient.update(params, function (err, data) {
            if (err) {
                console.error("Unable to updated position. Error:", JSON.stringify(err, null, 2));
                reject('error');
            }
            else {
                console.log("Update to position succeeded.");
                resolve('Ok.');

            }
        });
    })



}


/*
input:
    robotName- the name of the robot that is being turned 
    direction - the direction that the robot is going to be turned to 

This function creates a promise which updates the database and updates the item
with the given robot name to the new facing direction
*/
function updateOrientation(robotName, direction) {
    return new Promise((resolve, reject) => {

        //Create a new date object to signify when the robot was last updated
        var date = new Date();
        //My code is hosted on a box 4 hours ahead then my actual time, so we adjust 
        date.setHours(date.getHours() - 4);
        var localDate = date.toLocaleString();

        //create the parameters to update the db
        var params = {
            TableName: "robots",
            Key: {
                "name": robotName
            },
            UpdateExpression: 'SET orientation  = :d, lastMove = :date ',
            ExpressionAttributeValues: {
                ':d': direction,
                ':date': localDate
            }
        };

        console.log('Updating robots orientation with ', params);
        //call the update function and if successful an ok is returned if not an error is returned 
        docClient.update(params, function (err, data) {
            if (err) {
                console.error("Unable to updated position. Error:", JSON.stringify(err, null, 2));
                reject('error');
            }
            else {
                console.log("Update to orientation succeeded.");
                resolve('Ok.');

            }
        });
    })

}



/*

input: 
    robotToMove - the robot the user is wishing to move
    robotOnBoard - the robot that is on the board that could be in the way
    direction - the direction that the robot is going to go (forwards or backwards)
    units- how many units the robot should go

This function checks that the move made by the user is a valid move to move the robot. It checks that 
the robot is in bounds of the canvas and that it does not collide with the other robot.

*/
function moveToPosition(robotToMove, robotOnBoard, direction, units) {

    //the new position of X or Y of the robot
    var updatedPosition;

    //if the robot is asked to move forward 
    if (direction === 'forward') {
        //if the robot is facing north 
        if (robotToMove.orientation === 'N') {
            updatedPosition = robotToMove.Y + units;

            //if the updated position is out of bounds we return an error message to be displayed
            if (updatedPosition > 199 || updatedPosition < 0) {
                return `I cant, the way forward is blocked. The board is only 200 by 200 starting at 0 to 199. You tried to move to ${updatedPosition}. Please stay in the board`;
            }

            //If the robots collide when the robot tries to move an error message will be returned and displayed
            if ((isObstruction(robotToMove.Y, robotOnBoard.Y, updatedPosition) && robotOnBoard.X === robotToMove.X)) {
                var distance = Math.abs(botOnBoard.Y - robotToMove.Y) -1;
                return `I can't, the way ${direction} is blocked. I can only move ${distance} steps forward.`;
            }
            //if the move is legal we return the proposed position
            else {
                return updatedPosition;
            }
        }
        //if the robot is facing south and asked to move forward 
        else if (robotToMove.orientation === 'S') {
            updatedPosition = robotToMove.Y - units;
            if (updatedPosition > 199 || updatedPosition < 0) {
                return `I cant, the way forward is blocked. The board is only 200 by 200 starting at 0 to 199. You tried to move to ${updatedPosition}. Please stay in the board`;
            }
            if ((isObstruction(robotToMove.Y, robotOnBoard.Y, updatedPosition) && robotOnBoard.X === robotToMove.X)) {
                var distance = Math.abs(robotToMove.Y -robotOnBoard.Y)-1 ;
                return `I can't, the way ${direction} is blocked. I can only move ${distance} steps forward.`;
            }
            else {
                return updatedPosition;
            }

        }
        //if the robot is facing east and asked to move forward 
        else if (robotToMove.orientation === 'E') {
            updatedPosition = robotToMove.X + units;

            if (updatedPosition > 199 || updatedPosition < 0) {
                return `I cant, the way forward is blocked. The board is only 200 by 200 starting at 0 to 199. You tried to move to ${updatedPosition}. Please stay in the board`;

            }
            if ((isObstruction(robotToMove.X, robotOnBoard.X, updatedPosition) && robotOnBoard.Y === robotToMove.Y)) {
                var distance = Math.abs(robotOnBoard.X - robotToMove.X) -1 ;
                return `I can't, the way ${direction} is blocked. I can only move ${distance} steps forward.`;
            }
            else {
                return updatedPosition;
            }
        }
        //if the robot is facing west and asked to move forward
        else {
            updatedPosition = robotToMove.X - units;

            if (updatedPosition > 199 || updatedPosition < 0) {
                return `I cant, the way forward is blocked. The board is only 200 by 200 starting at 0 to 199. You tried to move to ${updatedPosition}. Please stay in the board`;
            }

            if ((isObstruction(robotToMove.X, robotOnBoard.X, updatedPosition) && robotOnBoard.Y === robotToMove.Y)) {
                var distance = Math.abs(robotToMove.X - robotOnBoard.X) -1 ;
                return `I can't, the way ${direction} is blocked. I can only move ${distance} steps forward.`;
            }
            else {
                return updatedPosition;
            }
        }
        //if the user asks the robot to move backwards 
    } else {
        //if the robot is facing north and asked to move backwards 
        if (robotToMove.orientation === 'N') {
            updatedPosition = robotToMove.Y - units;

            if (updatedPosition > 199 || updatedPosition < 0) {
                return `I cant, the way backwards is blocked. The board is only 200 by 200 starting at 0 to 199. You tried to move to ${updatedPosition}. Please stay in the board`;
            }
            if ((isObstruction(robotToMove.Y, robotOnBoard.Y, updatedPosition) && robotOnBoard.X === robotToMove.X)) {
                var distance = Math.abs(robotToMove.Y - robotOnBoard.Y) -1 ;
                return `I can't, the way ${direction} is blocked. I can only move ${distance} steps backwards.`;
            }
            else {
                return updatedPosition;
            }
        }
        //if the robot is facing south and asked to move backwards
        else if (robotToMove.orientation === 'S') {
            updatedPosition = robotToMove.Y + units;

            if (updatedPosition > 199 || updatedPosition < 0) {
                return `I cant, the way backwards is blocked. The board is only 200 by 200 starting at 0 to 199. You tried to move to ${updatedPosition}. Please stay in the board`;
            }

            if ((isObstruction(robotToMove.Y, robotOnBoard.Y, updatedPosition) && robotOnBoard.X === robotToMove.X) || updatedPosition > 199 || updatedPosition < 0) {
                var distance = Math.abs(robotOnBoard.Y - robotToMove.Y) -1 ;
                return `I can't, the way ${direction} is blocked. I can only move ${distance} steps forward.`;
            }
            else {
                return updatedPosition;
            }
        }
        //if the robot is facing east and asked to move backwards
        else if (robotToMove.orientation === 'E') {
            updatedPosition = robotToMove.X - units;

            if (updatedPosition > 199 || updatedPosition < 0) {
                return `I cant, the way backwards is blocked. The board is only 200 by 200 starting at 0 to 199. You tried to move to ${updatedPosition}. Please stay in the board`;
            }

            if ((isObstruction(robotToMove.X, robotOnBoard.X, updatedPosition) && robotOnBoard.Y === robotToMove.Y)) {
                var distance = Math.abs(robotToMove.X - robotOnBoard.X) -1;
                return `I can't, the way ${direction} is blocked. I can only move ${distance} steps forward.`;
            }
            else {
                return updatedPosition;
            }
        }
        //if the robot is facing west and asked to move backwards 
        else {
            updatedPosition = robotToMove.X + units;

            if (updatedPosition > 199 || updatedPosition < 0) {
                return `I cant, the way backwards is blocked. The board is only 200 by 200 starting at 0 to 199. You tried to move to ${updatedPosition}. Please stay in the board`;
            }

            if ((isObstruction(robotToMove.X, robotOnBoard.X, updatedPosition) && robotOnBoard.Y === robotToMove.Y)) {
                var distance = Math.abs(robotOnBoard.X - robotToMove.X) -1 ;
                speechText = `I can't, the way ${direction} is blocked. I can only move ${distance} steps forward.`;
            }
            else {
                return updatedPosition;
            }
        }
    }
}


let skill;

/*
Function that make the listed Handlers available for Alexa to use
*/
exports.handler = async function (event, context) {

    console.log(`REQUEST++++${JSON.stringify(event)}`);


    skill = Alexa.SkillBuilders.custom()
        .addRequestHandlers(
            robotPositionHandler,
            robotTurnHandler,
            robotMoveHandler,
            HelpIntentHandler,
            CancelAndStopIntentHandler,
            FallbackHandler,
        )
        .addErrorHandlers(ErrorHandler)
        .create();


    const response = await skill.invoke(event, context);
    console.log(`RESPONSE++++${JSON.stringify(response)}`);

    return response;

}
