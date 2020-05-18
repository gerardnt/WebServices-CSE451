/*
Nicholas gerard 
Comment Lambda Function 
This function adds a comment to a specific movie item in the 
dynamodb db with the given title and year 


*/
var AWS = require("aws-sdk");

//main entry point
exports.handler = (event,context,callback) => {
    
    AWS.config.update({
        region: "us-east-1",
    });

    //default year
    var comment;
    var title ;
    var year;
    var data;
     
    var docClient = new AWS.DynamoDB.DocumentClient();
    
    //make sure that the call is a post and the year is in the path 
    if (event.httpMethod == 'POST' && (typeof event.pathParameters !== 'undefined' )  && event.pathParameters != null && typeof event.pathParameters.year !== 'undefined') {     
      
      try{  
        //try to extract the comment and the title from the body of the POST request
        var j=JSON.parse(event.body)
        comment = j.comment;    
        title =j.title;
        year = parseInt(event.pathParameters.year);
        
        //create a json obj to add to the item in the db
        data = {
            comment:comment
        }
        
    
        // double check the data 
        console.log("Contents of the comment going to be passed to the movies table  ",data);
        console.log("The title being used ",title);
        
        //setup for parsing path parameter
         console.log("test of path parameters",event.pathParameters==null);
      
        } catch ( err) {
            const response = {
                statusCode: 500
            }
            console.log("can't parse the comments in the body for the following title",event.pathParameters.title);
            console.log("event",event)
            callback(null,response)
        }
    }
    
    //create the parameters for the movies table to 
    //update the comments of the movie with the given title
    var params = {
        TableName: "Movies",
        Key:{
            "year":year,
            "title": title
        },
        UpdateExpression:'SET #comment  = :d',
        ExpressionAttributeNames: { "#comment" : "comments" },
        ExpressionAttributeValues:{
            ':d':data
            }
         
    };
    console.log("data passed in to be commented ",data);

    //make call and on callbacks, return response
    docClient.update(params, function(err, data) {
        console.log("making call");
        if (err) {
            console.error("Unable to update the movie . Error:", JSON.stringify(err, null, 2));
            const response = {
                statusCode: 500
            }
            callback(null,response);
        }
        else {
            
            //if successfull return the comment that was added 
            console.log("update succeeded.");
            const response = {
                statusCode: 200,
                headers: {
                    "Access-Control-Allow-Origin": "*"
                },
                body: JSON.stringify({ "addedComment": comment }),
            };
            callback(null,response);

        }
    });
}
