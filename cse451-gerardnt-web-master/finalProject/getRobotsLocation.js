/*
Nicholas Gerard
CSE 451 Spring 2020 
Final Project 

This is program retrieves the robots information from the database 
and returns the data.

*/

var AWS = require("aws-sdk");

//main entry point
exports.handler = (event, context, callback) => {
    console.log("event", event);

    console.log("starting getting robots lambda call.");
    
    AWS.config.update({
        region: "us-east-1",
    });
    
    var docClient = new AWS.DynamoDB.DocumentClient()

    var table = "robots";

    //if the method is a Get then we will start the process of getting the info
    if (event.httpMethod == 'GET') {
        
        
        var params = {
            TableName: table
        };
        
        //call the doc client scan function to get all the robot data
        docClient.scan(params, function(err, data) {
            console.log("Calling robots table  " + data);
            //if there is an error return a 500 
            if (err) {
                console.error("Unable to Query. Error JSON:", JSON.stringify(err, null, 2));

                const response = {
                    statusCode: 500
                }
                callback(null, response);
            }
            //if there is no error return the robot data
            else {
                console.log("Query successfull:", JSON.stringify(data, null, 2));

                const response = {
                    statusCode: 200,
                    headers: {
                        "Access-Control-Allow-Origin": "*"
                    },
                    body: JSON.stringify({ "robots": data.Items }),
                };
                callback(null, response);
            }
        });
    }
}