/*
   Scott Campbell
   cse451
   wikiTable script

   script to update table data
   */


//get titles from wiki table
function updateWikiTable() {
	$("#info-body").html("");
	//send jquery
	$.ajax({
		url:'wikiRestServer.php/api/v1/wiki',
		method: 'get',
		dataType: 'json',
		success: function(result) {
			console.log(result);
			for (var i=0,l=result.keys.length;i<l;i++) {
				$("#info-body").append("<tr><td  onclick='getDetails(" + result.keys[i].pk +")'>" + result.keys[i].title+ "</td></tr>");
			}
		},

		error: function(err) {

			$("#msgText").append("error getting keys ");
			$("#msg").show();
			console.log("error ingetValue",err);
			//getKeys();
		}
	})
}


//delete item
function deleteItem(pk) {
	console.log("delete " + pk);
	var sendData = {}
	sendData = {pk:pk}

	$.ajax({
		url: 'wikiRestServer.php/api/v1/wiki',
		method: 'delete',
		data: JSON.stringify(sendData),
		dataType: 'json',
		success: (data) => {
			console.log(data);
			if (data.status == "FAIL") {
				$("#msgText").html("Failed - " + data.msg);
				$("#msg").show();
			} else {
				$("#msgText").html("Record delete");
				$("#msg").show();

				updateWikiTable();
			}

		},
		error: (error) => {
			$("#msgText").html("Error deleting");
			$("#msg").show();
		}
	});

		$("#details").hide();



}

//get details of wiki item
function getDetails(pk) {
	console.log("get details for " +pk);
	$.ajax({
		url:'wikiRestServer.php/api/v1/wiki/'+pk,
		method: 'get',
		dataType: 'json',
		success: function(result) {
			console.log(result);
			$("#detail-title").html(result.value.title)
			$("#detail-rating").html(result.value.rating)
			$("#detail-reason").html(result.value.reason)
			$("#detail-numAccess").html(result.value.numAccess)
			$("#detail-link").html("<a href='https://en.wikipedia.org/wiki/" + result.value.title + "' target='_blank'>Link</a>");
			$("#details").show();
			$("#deleteItem").html("<a onclick='deleteItem(" + pk + ")'>Delete</a>");

		},

		error: function(err) {

			$("#msgText").append("error getting details");
			$("#msg").show();
			console.log("error in getDetails",err);
		}
	})


}


//handle form submit
function submitAddForm(event) {
	event.preventDefault();
	var sendData = {}
	title = $("#add-title").val();
	rating = $("input[name=rating]:checked").val()
	reason= $("#add-reason").val();
	sendData = {title:title,rating:rating,reason:reason}

	console.log("add ",title,rating,reason);
	$("#addForm").hide();
	$.ajax({
		url: 'wikiRestServer.php/api/v1/wiki',
		method: 'put',
		data: JSON.stringify(sendData),
		dataType: 'json',
		success: (data) => {
			console.log(data);
			if (data.status == "FAIL") {
				$("#msgText").html("Failed - " + data.msg);
				$("#msg").show();
			} else {
				$("#msgText").html("Record Added");
				$("#msg").show();

				updateWikiTable();
			}

		},
		error: (error) => {
			$("#msgText").html("Error adding");
			$("#msg").show();
		}
	});


}

//display the add form
function showAddForm() {
	console.log("show add form");
	$("#add-title").val("");
	$("#add-reason").val("");
	$("input[name=rating]").prop('checked',false);
	$("#addForm").show();
}

//hide the add form on a cancel
function cancel() {
	$("#addForm").hide();
}


//hide error message
function hideMessage() {
	console.log("hideMessage");
	$("#msg").hide();
}

//update temp field
function getTemp() {
	console.log("gettemp");
	$.ajax({
		url: 'wikiRestServer.php/api/v1/temp',
		method: 'get',
		dataType: 'json',
		success: (data) => {
			console.log("get temp ok");
			$("#temp").html(data.temp);

		},
		error: (error) => {
			console.log("get temp failed");
			$("#msgText").html("Error getting temperature");
			$("#msg").show();
		}
	});
}

//setup all code
$(document).ready(function() {
	$("#details").hide();
	updateWikiTable();
	$("#hideDetails").click(() => { $("#details").hide();});
	$("#showAddForm").click(showAddForm);
	$("#addForm").submit(submitAddForm);
	$("#msg").hide();
	getTemp();
});


