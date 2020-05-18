<?php
/*
 * Scott Campbell
 * todoist code for 3/20
 * cse451
 * */

//detect if not run from command line
if (!isset($argv)) {
	print "This can only be run from command line";
	die;
}


require "todoistModelToken.php";

$projects = getProjects();

//find project called "class"
$classProject=null;
foreach ($projects as $p) {
	if ($p->name == "class") {
		$classProject = $p;
		break;
	}
}

//if not present, create
if ($classProject == null) {
	print "Adding project\n";
	$classProject = addProject("class");
}
print "Project\n";
print_r($classProject);


//add task
$task = addTask("work " . uniqid('work'),$classProject->id);


//print all tasks
print ("All tasks in class\n");
print_r(getTasks($classProject->id));


//add your code here to close all tasks in project "class"
//use the todoist web interface (https://todoist.com) to verify behavior
$tasks = getTasks($classProject->id);
foreach($tasks as $task){
	print("closing task ".$task->content. "\n");
	closeTask($task->id);
	
}


//do not modify code after this
print "Tasks at end\n";
$a = getTasks($classProject->id);
foreach ($a as $i) {
	print '* ' . $i->content ."\n";
}
