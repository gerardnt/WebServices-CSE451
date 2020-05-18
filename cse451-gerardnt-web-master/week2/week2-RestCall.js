/*
Nicholas Gerard
CSE 451 
Javascript Week 2 Assignment 
2/11/2020

Used: 
Campbells code of Jquery example
https://stackoverflow.com/questions/733314/jquery-loop-over-json-result-from-ajax-success : for each loop after an Ajax success
https://codepen.io/Huso/pen/YOZWoV : have bootstrap alerts automatically fade out 
https://www.w3schools.com/bootstrap4/bootstrap_alerts.asp : how to create alerts in bootstrap
https://www.w3schools.com/howto/howto_css_image_center.asp : how to center the image 
https://www.kirupa.com/html5/clipping_content_using_css.htm : how to handle the error messages with a scroll bar

/*
Calling the Rest Services and obtaining all the keys in the array. 
Then calling the values function with the associated keys by looping through the returned array
*/
function getKeys() {
	$.ajax({url:"http://campbest.451.csi.miamioh.edu/cse451-campbest-web-public-2020/week2/week2-rest.php/api/v1/info",
		type:"get",
		dataType:'json',
		success: function(data) {
        $.each(data.keys, function(val, key){
            getValues(key);

        });
        },
        // If unable to reach the service then we show a bootstrap alert and retry. We then wait 3 seconds and fade out the message 
		error:function(data) {
            $("#error").append(`<div class="alert alert-danger alert-dismissible " role="alert">  Error Fetching the Keys retrying momentarily. </div>`);
            window.setTimeout(function() {
                $(".alert").fadeTo(500, 0).slideUp(500, function(){
                    $(this).remove(); 
                });
            }, 15000);
            getKeys();
			}
	      });
}

/*
Method that uses the key and calls the rest service with the key to obtain the value of the key
we then append both the key and value to a table on the dom
*/
function getValues(key) {
	$.ajax({url:"http://campbest.451.csi.miamioh.edu/cse451-campbest-web-public-2020/week2/week2-rest.php/api/v1/info/"+key,
		type:"get",
		dataType:'json',
		success: function(data) {
        $("#TableBody").append(`<tr> <td> ${key} </td> <td> ${data.value} </td> </tr>`)
        },
        // If unable to reach the service then we show a bootstrap alert and retry. We then wait 3 seconds and fade out the message 
		error:function(data) {
            $("#error").append(`<div class="alert alert-danger alert-dismissible fade in" role="alert">  Error Fetching the Values retrying momentarily. </div>`);
            window.setTimeout(function() {
                $(".alert").fadeTo(500, 0).slideUp(500, function(){
                    $(this).remove(); 
                });
            }, 15000);
            getValues(key);
			}
	      });
}


//Call the getKeys method to call the Rest service 
$(document).ready(function() {
		getKeys();
		});


