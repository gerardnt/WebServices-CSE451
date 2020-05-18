/*
Nicholas Gerard
Movie.js 
CSE 451 Spring 2020 
This JS file is to populate my html page with movies from my dynamodb on my AWS account through my API Gateway


links used:

    https://stackoverflow.com/questions/18445590/jquery-animate-stop-scrolling-when-user-scrolls-manually
    JQUERY documentation 
    Past projects  --- Week 6 REST sErver Project
    Dr. Campbells solutions to projects 




*/

$(document).ready(function () {
    //hides the comments section and error message to begin with
    $("#comments").hide();
    $("#ErrorMsg").html('');



    /*
    input : 
        int year
    desc:
        Gets the movies for the given year from my Dynamodb db on my AWS account and populates the table on the page with them
    */
    function getYear(year) {

        //clears the table
        $("#movieTable").html('');

        //checks that they year is actually a number 
        if (isNaN(year) || year === undefined || year === null || year === '') {

            $("#ErrorMsg").html('<div class="center-align card-panel red lighten-1 white-text center-align"><h6> The number entered was not valid. Please Try again<h6></div>');
            return;
        }

        //ajax call to my API gateway to get all the movies 
        $.ajax({
            url: 'https://xfnue7hkr0.execute-api.us-east-1.amazonaws.com/default/Movies/v1/' + year,
            method: 'GET',
            success: (data) => {
                var length = data.movies.length;

                //make sure that the results is not empty if it is we will send an info message not an error message to try another year
                if (length > 0) {

                    for (var i = 0; i < length; i++) {


                        // if the variables are undefined leave them blank, besides the comments one because we need ot check for that later to manipulate the TR accordingly
                        var title = data.movies[i].title;
                        var plot = data.movies[i].info.plot === undefined ? '' : data.movies[i].info.plot;
                        var genres = data.movies[i].info.genres === undefined ? '' : data.movies[i].info.genres;
                        var comment = data.movies[i].comments;



                        if (comment === undefined) {
                            // if there are no comments add a table row without any comments and just a blank
                            $("#movieTable").append('<tr><td>' + title + '</td><td>' + genres + '</td><td>' + plot + '</td><td></td><td>' + '<a value="' + title + '" datayear ="' + year + '"  datacomment="' + '' + '" class="btn-floating btn-large waves-effect waves-light red add-comment">add</a></td>');
                            //add an onclick to each add button which
                            // will trigger the page to the top and call the get comments function
                            $('.add-comment').click(function (event) {
                                event.stopPropagation();
                                $('html, body').animate({
                                    scrollTop: $('#comments').offset().top
                                },
                                    1000);
                                $('html, body').on("scroll mousedown wheel DOMMouseScroll mousewheel keyup touchmove", function () {
                                    $('html, body').stop();
                                });

                                getComments($(this))
                            });
                        } else {
                            //if there are comments add them to them to the table row
                            $("#movieTable").append('<tr><td>' + title + '</td><td>' + genres + '</td><td>' + plot + '</td><td>' + comment.comment + '</td><td>' + '<a value ="' + title + '" datayear ="' + year + '" datacomment="' + comment.comment + '" class="btn-floating btn-large waves-effect waves-light red add-comment">add</a></td>');
                            //add an onclick to each add button which
                            // will trigger the page to the top and call the get comments function
                            $('.add-comment').click(function (event) {
                                event.stopPropagation();
                                $('html, body').animate({
                                    scrollTop: $('#comments').offset().top
                                },
                                    1000);
                                $('html, body').on("scroll mousedown wheel DOMMouseScroll mousewheel keyup touchmove", function () {
                                    $('html, body').stop();
                                });

                                getComments($(this))
                            });
                        }
                    }
                }
                else {
                    //info message to try another year
                    $("#ErrorMsg").html('<div class="center-align card-panel light-blue darken-1 white-text"><h5> It seems the year you tried has no movies. Please try another between 1920-2018 inclusive. <h5></div>');
                }
            },
            fail: (error) => {
                //error on ajax call
                $("#ErrorMsg").html('<div class="center-align card-panel red lighten-1 white-text"><h4> Could not fetch the movies. Please try again.<h4></div>');

            }
        });
    }


    /*
    input:
        the individual node for each table rows add button
    desc:
        gets the comment form ready for the specific node by adding the data to the form and then showing the form

    */
    function getComments(node) {
        var title = node[0].attributes.value.nodeValue;
        var year = node[0].attributes.datayear.nodeValue;
        var comment = node[0].attributes.datacomment.nodeValue;

        $('#commentTitle').html('<h6>Add or Edit comments for ' + title + ' From ' + year + '</h6>');
        $('#movieTitle').val(title);
        $('#yearInput').val(year);
        $('#commentText').val(comment);
        $('#commentText placeholder').val(comment);
        $('#comments').show();



    }

    /*
   input:
       the individual event from the form submit of adding the comments
   desc:
      makes an ajax call to the API gateway to update the comment associated with a specific movie based off the title and year

   */

    function addComment(event) {
        event.preventDefault();

        var comment = $('#commentText').val();
        var title = $('#movieTitle').val();
        var year = $('#yearInput').val();

        //check that the comment is valid

        if (comment === undefined || comment === ' ' || comment === null) {

            $("#ErrorMsg").html('<div class=" center-align card-panel red lighten-1 white-text"><h4> Please make sure to enter a valid comment.<h4></div>');

        }
        else {

            //if the comment is valid make an ajax call to the API gateway 

            var data = JSON.stringify({ comment: comment, title: title });

            $.ajax({
                url: 'https://xfnue7hkr0.execute-api.us-east-1.amazonaws.com/default/Movies/v1/' + year,
                method: 'POST',
                data: data,
                datatype: 'application/json',
                success: (data) => {
                    //add success message to the page, update the table with the new info, and hide the comment box

                    $("#ErrorMsg").html('<div class="center-align card-panel green lighten-1 white-text"><h4> The comment has been added.<h4></div>');
                    var comment = $('#commentText').val(data.addedComment);
                    getYear(year);
                    $('#comments').hide();



                },
                fail: (error) => {
                    //on error show error message and hide the comments box

                    $("#ErrorMsg").html('<div class="center-align card-panel red lighten-1 white-text"><h4> Could not add the comment. Please try again.<h4></div>');
                    $('#comments').hide();

                }
            });
        }
    }


    //on click of the comment form submit call the function addcomment 
    $('#commentSubmit').click(addComment);

    //on click of year form submit, the search function, call the get year function 
    $("#yearSubmit").click(function () {
        event.preventDefault();
        var year = $("#year").val();
        $("#ErrorMsg").html('');
        getYear(year);
    });


});
