# Introduction

The Interactive Decision Tree is a web-based tool that will walk users through a decision process by asking questions to lead them down the appropriate decision path. Think of it as a user-friendly flow chart.

Decision tree data is stored as standard XML and the "viewer" is made up of HTML, CSS and Javascript. The fact that the viewer uses only client-side code allows a decision tree to be hosted on any flavor of web server or even from a local computer.

Also included is a PHP-driven decision tree editor. This allows users to create the underlying XML data for a decision tree more easily than manually editing XML files. The editor requires PHP version 5 or higher to be running on your web server. Since the editor far simplifies the process of creating decision trees, it is recommended that you use it as well. 

A functional demo can be viewed at [hungry-media.com](http://www.hungry-media.com/code/interactive-decision-tree/demo.html?0001) and an example video on [YouTube](http://www.youtube.com/embed/ngcjYuJHZ4Q "View example on YouTube")

# Installation

This section assumes you already have your web server up and running with PHP version 5 or greater.

+ clone this repo
+ Modify the permissions of the xml/ directory such that it is writable by your web server.
+ Navigate to the editor page in your web browser: 
	e.g. `http://your-web-server.org/decision-tree/editTree.php` 
	(where `your-web-server` is the domain name of your web server and `decision-tree` is the directory created in step 2, above)

That's it. You should see a couple of example decision trees in the editor to experiment with.

## to do: More docs on how to use the editor
