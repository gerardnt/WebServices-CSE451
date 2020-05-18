/*
Nicholas Gerard
CSE 451 Spring 2020

This script has the alexa sum up the numbers provided by the user
 and gives the total after each number added by the user

*/

const Alexa = require('ask-sdk');


/*

This function handles the users starting the sum calculator with the 
given intent 

*/
const StartCalculatorHandler = {
	canHandle(handlerInput) {
        return handlerInput.requestEnvelope.request.type === 'IntentRequest' 
        && handlerInput.requestEnvelope.request.intent.name === 'StartCalculatorIntent';
	},
	handle(handlerInput) {

    const { attributesManager } = handlerInput;
    const sessionAttributes = attributesManager.getSessionAttributes();  

		const speechText = 'Welcome to calculator, Would you like to sum up numbers?';
       

    //We set the gameState and the sum session attributes
    if (Object.keys(sessionAttributes).length === 0) {
      sessionAttributes.gameState = 'ENDED';
    }

    //save the session variables
    attributesManager.setSessionAttributes(sessionAttributes);

		return handlerInput.responseBuilder
      .speak(speechText)
      .reprompt(speechText)
			.withSimpleCard('add Numbers', speechText)
			.getResponse();
	}
};


/*

This function handles when the users answers yes to starting
up the calculator
*/

const YesIntent = {
    canHandle(handlerInput) {

      let isCurrentlyPlaying = false;
      const { attributesManager } = handlerInput;
      const sessionAttributes = attributesManager.getSessionAttributes();
   

      //make sure that there is not already a session going on
      if (sessionAttributes && sessionAttributes.gameState === 'STARTED') {
        isCurrentlyPlaying = true;
      }

      return !isCurrentlyPlaying
      && Alexa.getRequestType(handlerInput.requestEnvelope) === 'IntentRequest' 
      && Alexa.getIntentName(handlerInput.requestEnvelope) === 'AMAZON.YesIntent';
    },
    handle(handlerInput) {

      const { attributesManager } = handlerInput;
      const sessionAttributes = attributesManager.getSessionAttributes();

      //set the session variables to 0 and gameState to started
      sessionAttributes.sum = 0;
      sessionAttributes.gameState = 'STARTED'

      attributesManager.setSessionAttributes(sessionAttributes);

      const text= 'Great! Start by saying a number for me to sum';

      return handlerInput.responseBuilder
        .speak(text)
        .reprompt(text)
        .withSimpleCard('add Numbers', text)
        .getResponse();
    },
  };


/*
This function handles if the user says no when asked if they want to use
the calculator. 
*/
  const NoIntent = {
    canHandle(handlerInput) {
      
      let isCurrentlyPlaying = false;
      const { attributesManager } = handlerInput;
      const sessionAttributes = attributesManager.getSessionAttributes();
  
      //make sure the calculator is not in use already
      if (sessionAttributes && sessionAttributes.gameState === 'STARTED') {
        isCurrentlyPlaying = true;
      }

      return !isCurrentlyPlaying 
      && handlerInput.requestEnvelope.request.type === 'IntentRequest' 
      && handlerInput.requestEnvelope.request.intent.name === 'AMAZON.NoIntent';
    },
    handle(handlerInput) {
    
      const text= 'Ok Bye!';

      return handlerInput.responseBuilder
        .speak(text)
        .withShouldEndSession(true)
        .getResponse();
  
    },
  };
  

  
/*

This function handles the summing and prompting of the total of the input 
given by the user to alexa

*/

const SumIntentHandler = {
	canHandle(handlerInput) {

        let isCurrentlyPlaying = false;
       const { attributesManager } = handlerInput;
       const sessionAttributes = attributesManager.getSessionAttributes() || {};
        
  
        // make sure calculator is in session
      if (sessionAttributes.gameState && sessionAttributes.gameState === 'STARTED') {
        isCurrentlyPlaying = true;
      }
    
        return isCurrentlyPlaying 
            && handlerInput.requestEnvelope.request.type === 'IntentRequest'
			      && handlerInput.requestEnvelope.request.intent.name === 'SumIntent';
	},
	handle(handlerInput) {

      const { attributesManager } = handlerInput;
      const sessionAttributes = attributesManager.getSessionAttributes() || {};
      let speechText;

        try{

        const guessNum = parseInt(Alexa.getSlotValue(handlerInput.requestEnvelope, 'number'), 10);

        if(!isNaN(guessNum + sessionAttributes.sum)){
        const sum = guessNum + sessionAttributes.sum;

        sessionAttributes.sum = sum;

        attributesManager.setSessionAttributes(sessionAttributes);

    		 speechText = 'The sum is '+ sum;
        }
        else{

        speechText = 'The sum is '+ sessionAttributes.sum +'. This did not change because the number you gave was not a number. Please try again.';
        }
        }
        catch(E){
          console.log(E);
          speechText = 'Please try again the number you gave was not a number or misrepresented. ';
        }
      
		return handlerInput.responseBuilder
      .speak(speechText)
      .reprompt(speechText)
			.withSimpleCard('add Numbers', speechText)
			.getResponse();
	}
};


/*
This function handles if the user asks for help to alexa
*/
const HelpIntentHandler = {
	canHandle(handlerInput) {
		return handlerInput.requestEnvelope.request.type === 'IntentRequest'
			&& handlerInput.requestEnvelope.request.intent.name === 'AMAZON.HelpIntent';
	},
	handle(handlerInput) {
		const speechText = 'You can ask me to sum up some numbers';

		return handlerInput.responseBuilder
      .speak(speechText)
      .reprompt(speechText)
			.withSimpleCard('add Numbers', speechText)
			.getResponse();
	}
};


/*
This function handles if the user stops or cancels the calculator 
*/
const CancelAndStopIntentHandler = {
	canHandle(handlerInput) {

        let isCurrentlyPlaying = false;
        const { attributesManager } = handlerInput;
        const sessionAttributes = attributesManager.getSessionAttributes();
    
        if (sessionAttributes && sessionAttributes.gameState === 'STARTED') {
          isCurrentlyPlaying = true;
        }
    
        // make sure that the session of the calculator is in progress 
        return isCurrentlyPlaying 
            && handlerInput.requestEnvelope.request.type === 'IntentRequest'
			&& (handlerInput.requestEnvelope.request.intent.name === 'AMAZON.CancelIntent'
				|| handlerInput.requestEnvelope.request.intent.name === 'AMAZON.StopIntent');
	},
	handle(handlerInput) {

    const speechText = 'Thanks For using the Calculator!';
  
		return handlerInput.responseBuilder
      .speak(speechText)
      .reprompt(speechText)
			.withSimpleCard('add Numbers', speechText)
			.withShouldEndSession(true)
			.getResponse();
	}
};

/*
This function handles the errors with the calculator 
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




let skill;

exports.handler = async function (event, context) {

console.log(`REQUEST++++${JSON.stringify(event)}`);


skill = Alexa.SkillBuilders.custom()
			.addRequestHandlers(
        YesIntent,
        StartCalculatorHandler,
        NoIntent,
				SumIntentHandler,
				 HelpIntentHandler,
				 CancelAndStopIntentHandler,
  )
 .addErrorHandlers(ErrorHandler)
  .create();


  const response = await skill.invoke(event, context);
	console.log(`RESPONSE++++${JSON.stringify(response)}`);

	return response;

      }
