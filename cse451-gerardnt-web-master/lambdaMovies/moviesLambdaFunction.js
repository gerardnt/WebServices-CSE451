/*
Nicholas Gerard
Movies Lambda function
This lambda function returns the movies for a given inputed
year from a GET request path parameter

*/

var AWS = require("aws-sdk");

//main entry point
exports.handler = (event,context,callback) => {
    AWS.config.update({
        region: "us-east-1",
    });

    //default year
    var year = 0;

    var docClient = new AWS.DynamoDB.DocumentClient();
    
    //only test for a GET method
    if (event.httpMethod == 'GET') {       
    //setup for parsing path parameter
    console.log("test of path parameters",event.pathParameters==null)
    if ((typeof event.pathParameters !== 'undefined' )  && event.pathParameters != null && typeof event.pathParameters.year !== 'undefined') {
        try {
            //try to parse and see if the path parameter year is a valid number
            year = parseInt(event.pathParameters.year);
        } catch ( err) {
            const response = {
                statusCode: 500
            }
            console.log("can't parse year",event.pathParameters.year);
            console.log("event",event)
            callback(null,response)
        }
    }
    }
    
    //created the parameters for the dynamodb query. The given year from above
    var params = {
        TableName: "Movies",
        KeyConditionExpression: "#yr = :yyyy",
        ExpressionAttributeNames: {
            "#yr": "year"
        },
        ExpressionAttributeValues: {
            ":yyyy": year
        }
    };
    
    //log what params we are going to pass and the year we are going to pass 
    console.log("params",params,"year",year);

    //make call and on callbacks, return response
    docClient.query(params, function(err, data) {
        console.log("making call");
        if (err) {
            //return a 500 if there is an error
            console.error("Unable to query. Error:", JSON.stringify(err, null, 2));
            const response = {
                statusCode: 500
            }
            callback(null,response);
        }
        else {
            //return info if the query is successfull 
            console.log("Query succeeded.");
            const response = {
                statusCode: 200,
                headers: {
                    "Access-Control-Allow-Origin": "*"
                },
                body: JSON.stringify({ "movies": data.Items }),
            };
            callback(null,response);

        }
    });
}
